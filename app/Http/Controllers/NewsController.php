<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\News;
use App\RubricatorsItem;
use Illuminate\Http\Request;
use App\Http\Requests\System\NewsRequest;
use Illuminate\Support\Facades\Storage;

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

    use Photable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // -------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------
        $news = News::with([
            'author',
            'albums',
            'company.location.city'
        ])
        ->moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
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
            'pageInfo' => pageInfo($this->entity_alias),
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('rubricators_items', false, 'index');

        // Главный запрос
        $rubricators_items = RubricatorsItem::moderatorLimit($answer)
        ->companiesLimit($answer)
        ->authors($answer)
        ->systemItem($answer)
        ->template($answer)
        ->orderBy('sort', 'asc')
        ->get();

        if ($rubricators_items->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать рубрикатор и наполнить его рубриками. А уже потом будем добавлять новости. Ок?";
            $ajax_error['link'] = "/admin/rubricators";
            $ajax_error['title_link'] = "Идем в рубрикаторы";

            return view('ajax_error', compact('ajax_error'));
        }

        return view('news.create', [
            'cur_news' => new $this->class,
            'pageInfo' => pageInfo($this->entity_alias),
        ]);
    }

    public function store(NewsRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $cur_news = (new News())->create($data);

        if ($cur_news) {

            $photo_id = $this->getPhotoId($request, $cur_news);
            $cur_news->photo_id = $photo_id;
            $cur_news->save();

            return redirect()->route('news.index');
        } else {
            abort(403, 'Ошибка при записи новости!');
        }
    }

    public function show(Request $request)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $cur_news = News::with([
            'albums.category',
            // 'cities',
        ])
        ->moderatorLimit($answer)
        ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $cur_news);

        return view('news.edit', [
            'cur_news' => $cur_news,
            'pageInfo' => pageInfo($this->entity_alias),
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

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $cur_news);
        $cur_news->update($data);

        if ($cur_news) {

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
