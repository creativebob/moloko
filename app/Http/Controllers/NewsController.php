<?php

namespace App\Http\Controllers;

// Модели
use App\News;

use App\Site;
use App\Photo;
use App\AlbumsCategory;
use App\AlbumEntity;
use App\CityEntity;

// Валидация
use Illuminate\Http\Request;
use App\Http\Requests\NewsRequest;

// Общие классы
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

// Специфические классы
// Фотографии
use Intervention\Image\ImageManagerStatic as Image;

// Транслитерация
use Illuminate\Support\Str;

class NewsController extends Controller
{

    // Настройки контроллера
    public function __construct(News $cur_news)
    {
        $this->middleware('auth');
        $this->cur_news = $cur_news;
        $this->class = News::class;
        $this->model = 'App\News';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $news = News::with('author', 'albums', 'company.location.city')
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        // ->orderBy('sort', 'asc')
        // ->filter($request, 'author_id') // Фильтр по авторам
        // ->filter($request, 'id', 'cities') // Фильтр по городам публикации
        ->booklistFilter($request)  // Фильтр по спискам
        ->dateIntervalFilter($request, 'publish_begin_date') // Интервальный фильтр по дате публикации
        ->orderBy('moderation', 'desc')
        ->orderBy('sort', 'asc')
        ->orderBy('publish_begin_date', 'desc')
        ->paginate(30);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'date_interval',        // Дата
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        return view('news.index',[
            'news' => $news,
            'page_info' => pageInfo($this->entity_alias),
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('news.create', [
            'cur_news' => new $this->class,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(NewsRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $cur_news = new News;

        $cur_news->name = $request->name;
        $cur_news->title = $request->title;
        $cur_news->preview = $request->preview;

        // Если ввели алиас руками
        if (isset($request->alias)) {
            $cur_news->alias = $request->alias;
        } else {
            // Иначе переводим заголовок в транслитерацию
            $cur_news->alias = Str::slug($request->title);
        }

        $cur_news->content = $request->content;

        $cur_news->publish_begin_date = Carbon::parse($request->publish_begin_date)->format('Y-m-d');

        if (isset($request->publish_end_date)) {
            $cur_news->publish_end_date = Carbon::parse($request->publish_end_date)->format('Y-m-d');
        }

        // Если нет прав на создание полноценной записи - запись отправляем на модерацию
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        if($answer['automoderate'] == false){
            $cur_news->moderation = 1;
        }

        // Cистемная запись
        $cur_news->system_item = $request->system_item;
        $cur_news->display = $request->display;

        // Получаем данные для авторизованного пользователя
        $user = $request->user();

        $cur_news->company_id = $user->company_id;
        $cur_news->author_id = hideGod($user);

        $cur_news->save();

        if ($cur_news) {

            // Cохраняем / обновляем фото
            savePhoto($request, $cur_news);

            // Когда новость записалась, смотрим пришедние для нее альбомы и пишем, т.к. это первая запись новости
            if (isset($request->albums)) {
                $cur_news->albums()->attach($request->albums);
            }

            // Когда новость записалась, смотрим пришедние для нее города и пишем, т.к. это первая запись новости
            // if (isset($request->cities)) {
            //     $cur_news->cities()->attach($request->cities);
            // }

            return redirect()->route('news.index');
        } else {
            abort(403, 'Ошибка при записи новости!');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $alias)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $cur_news = News::with([
            'albums.category',
            // 'cities',
        ])
        ->moderatorLimit($answer)
        ->whereAlias($alias)
        ->first();

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        return view('news.edit', [
            'cur_news' => $cur_news,
            'page_info' => pageInfo($this->entity_alias),
        ]);
    }

    public function update(NewsRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $cur_news = News::moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        $cur_news->name = $request->name;
        $cur_news->title = $request->title;

        if ($cur_news->alias != $request->alias) {

            $cur_news->alias = $request->alias;

        }

        $cur_news->preview = $request->preview;
        $cur_news->content = $request->content;

        $cur_news->publish_begin_date = Carbon::parse($request->publish_begin_date)->format('Y-m-d');

        if (isset($request->publish_end_date)) {
            $cur_news->publish_end_date = Carbon::parse($request->publish_end_date)->format('Y-m-d');
        }

        // Модерация и системная запись
        $cur_news->system_item = $request->system_item;
        $cur_news->moderation = $request->moderation;
        $cur_news->display = $request->display;

        $cur_news->editor_id = hideGod($request->user());

        $cur_news->save();

        if ($cur_news) {

            // Cохраняем / обновляем фото
            savePhoto($request, $cur_news);

            // Когда новость обновилась, смотрим пришедние для нее альбомы и сравниваем с существующими
            if (isset($request->albums)) {
                $cur_news->albums()->sync($request->albums);
            } else {
                // Если удалили последний альбом для новости и пришел пустой массив
                $cur_news->albums()->detach();
            }

            // Когда новость обновилась, смотрим пришедние для нее города и сравниваем с существующими
            // if (isset($request->cities)) {
            //     $cur_news->cities()->sync($request->cities);
            // } else {
            //     // Если удалили последний город для новости и пришел пустой массив
            //     $cur_news->cities()->detach();
            // }

            return redirect()->route('news.index');
        } else {
            abort(403, 'Ошибка обновления новости!');
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $cur_news = News::moderatorLimit($answer)
        ->findOrFail($id);
        // dd($cur_news);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        $cur_news->editor_id = hideGod($request->user());
        $cur_news->save();

        // Удаляем связи
        $cur_news->albums()->detach();
        $cur_news->photo()->delete();
        // $cur_news->cities()->detach();

        // Удаляем файлы
        $directory = $cur_news->company_id.'/media/news/'.$cur_news->id;
        $del_dir = Storage::disk('public')->deleteDirectory($directory);

        $cur_news->delete();

        if ($cur_news) {
            return redirect()->route('news.index');
        } else {
            abort(403, 'Ошибка при удалении новости');
        }
    }

    // ------------------------------------------- Ajax ---------------------------------------------

    // Проверка наличия в базе
    // public function ajax_check(Request $request, $alias)
    // {

    //     // Проверка новости по сайту в нашей базе данных
    //     $result_count = News::whereAlias($request->alias)
    //     ->where('id', '!=', $request->id)
    //     ->count();

    //     return response()->json($result_count);
    // }
}
