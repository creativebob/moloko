<?php

namespace App\Http\Controllers;

use App\Attachment;
use App\AttachmentsCategory;

use App\Http\Requests\AttachmentStoreRequest;
use App\Http\Requests\AttachmentUpdateRequest;
use App\Manufacturer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Traits\Articlable;

class AttachmentController extends Controller
{

    // Настройки сконтроллера
    public function __construct(Attachment $attachment)
    {
        $this->middleware('auth');
        $this->attachment = $attachment;
        $this->class = Attachment::class;
        $this->model = 'App\Attachment';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
    }

    use Articlable;


    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Включение контроля активного фильтра
        $filter_url = autoFilter($request, $this->entity_alias);

        if (($filter_url != null)&&($request->filter != 'active')) {
            Cookie::queue(Cookie::forget('filter_' . $this->entity_alias));
            return Redirect($filter_url);
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));
        // dd($answer);

        // -------------------------------------------------------------------------------------------------------------
        // ГЛАВНЫЙ ЗАПРОС
        // -------------------------------------------------------------------------------------------------------------

        $columns = [
            'id',
            'article_id',
            'category_id',
            'price_unit_id',
            'price_unit_category_id',

            'portion_status',
            'portion_name',
            'portion_abbreviation',
            'unit_portion_id',
            'portion_count',

            'author_id',
            'company_id',
            'display',
            'system',
            'unit_for_composition_id'
        ];


        $attachments = Attachment::with([
            'author',
            'company',
            'in_cleans',
            'in_drafts',
            'compositions.cur_goods',
            'article' => function ($q) {
                $q->with([
                    'group',
                    'photo',
                    'unit',
                    'unit_weight',
                    'unit_volume'
                ]);
            },
            'category'
//            => function ($q) {
//                $q->select([
//                    'id',
//                    'name'
//                ]);
//            }
            ,
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer) // Фильтр по системным записям
            ->booklistFilter($request)
            ->filter($request, 'author_id')

            ->whereHas('article', function($q) use ($request){
                $q->filter($request, 'articles_group_id');
            })

            ->filter($request, 'category_id')

            ->where('archive', false)
//            ->select($columns)
            ->orderBy('moderation', 'desc')
            ->orderBy('id', 'desc')
            ->paginate(30);
        // dd($attachments);


        // -----------------------------------------------------------------------------------------------------------
        // ФОРМИРУЕМ СПИСКИ ДЛЯ ФИЛЬТРА ------------------------------------------------------------------------------
        // -----------------------------------------------------------------------------------------------------------

        $filter = setFilter($this->entity_alias, $request, [
            'author',               // Автор записи
//            'attachments_category',  // Категория вложений
            'articles_group',       // Группа артикула
            'booklist'              // Списки пользователя
        ]);

        // Окончание фильтра -----------------------------------------------------------------------------------------

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        return view('products.articles.common.index.index', [
            'items' => $attachments,
            'page_info' => $page_info,
            'class' => $this->class,
            'entity' => $this->entity_alias,
            'category_entity' => 'attachments_categories',
            'filter' => $filter,
        ]);
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('attachments_categories', false, 'index');

        // Главный запрос
        $attachments_categories = AttachmentsCategory::withCount('manufacturers')
            ->with('manufacturers')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->orderBy('sort', 'asc')
            ->get();

        if($attachments_categories->count() == 0){

            // Описание ошибки
            $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!";
            $ajax_error['text'] = "Для начала необходимо создать категории вложений. А уже потом будем добавлять вложение. Ок?";
            $ajax_error['link'] = "/admin/attachments_categories";
            $ajax_error['title_link'] = "Идем в раздел категорий";

            return view('ajax_error', compact('ajax_error'));
        }

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right('manufacturers', false, 'index');

        $manufacturers_count = Manufacturer::moderatorLimit($answer)
            ->companiesLimit($answer)
            ->systemItem($answer)
            ->count();

        // Если нет производителей
        if ($manufacturers_count == 0){

            // Описание ошибки
            // $ajax_error = [];
            $ajax_error['title'] = "Обратите внимание!"; // Верхняя часть модалки
            $ajax_error['text'] = "Для начала необходимо добавить производителей. А уже потом будем добавлять вложение. Ок?";
            $ajax_error['link'] = "/admin/manufacturers/create"; // Ссылка на кнопке
            $ajax_error['title_link'] = "Идем в раздел производителей"; // Текст на кнопке

            return view('ajax_error', compact('ajax_error'));
        }

        return view('products.articles.common.create.create', [
            'item' => new $this->class,
            'title' => 'Добавление вложения',
            'entity' => $this->entity_alias,
            'category_entity' => 'Attachments_categories',
            'units_category_default' => 6,
            'unit_default' => 32,
        ]);
    }

    public function store(AttachmentStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        Log::channel('operations')
            ->info('========================================== НАЧИНАЕМ ЗАПИСЬ УПАКОВКИ ==============================================');

        $attachments_category = AttachmentsCategory::findOrFail($request->category_id);
        // dd($goods_category->load('groups'));
        $article = $this->storeArticle($request, $attachments_category);

        if ($article) {

            $data = $request->input();
            $data['article_id'] = $article->id;
            $data['price_unit_category_id'] = $data['units_category_id'];
            $data['price_unit_id'] = $data['unit_id'];

            $attachment = (new attachment())->create($data);

            if ($attachment) {

                // Пишем куки состояния
                // $mass = [
                //     'goods_category' => $goods_category_id,
                // ];
                // Cookie::queue('conditions_goods_category', $goods_category_id, 1440);

                Log::channel('operations')
                    ->info('Записали вложение c id: ' . $attachment->id);
                Log::channel('operations')
                    ->info('Автор: ' . $attachment->author->name . ' id: ' . $attachment->author_id .  ', компания: ' . $attachment->company->name . ', id: ' .$attachment->company_id);
                Log::channel('operations')
                    ->info('========================================== КОНЕЦ ЗАПИСИ УПАКОВКИ ==============================================

                    ');

                // dd($request->quickly);
                if ($request->quickly == 1) {
                    return redirect()->route('attachments.index');
                } else {
                    return redirect()->route('attachments.edit', $attachment->id);
                }
            } else {
                abort(403, 'Ошибка записи вложений');
            }
        } else {
            abort(403, 'Ошибка записи информации вложений');
        }
    }

    public function show($id)
    {
        //
    }

    public function edit(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // Главный запрос
        $attachment = Attachment::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($attachment);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $attachment);

        $attachment->load([
            'article' => function ($q) {
                $q->with([
                    'unit'
                ]);
            }
        ]);
        $article = $attachment->article;
        // dd($article);

        // Получаем настройки по умолчанию
//        $dropzone = getPhotoSettings($this->entity_alias);
//        $dropzone['id'] = $article->id;
//        $dropzone['entity'] = $article->getTable();
//        dd($dropzone);

        // Получаем настройки по умолчанию
        $settings = getPhotoSettings($this->entity_alias);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);
        // dd($page_info);

        return view('products.articles.common.edit.edit', [
            'title' => 'Редактировать вложение',
            'item' => $attachment,
            'article' => $article,
            'page_info' => $page_info,
            'settings' => $settings,
//            'dropzone' => json_encode($dropzone),
            'entity' => $this->entity_alias,
            'category_entity' => 'attachments_categories',
            'categories_select_name' => 'attachments_category_id',
            'attachment' => $attachment,
            'paginator_url' => url()->previous()
        ]);
    }

    public function update(AttachmentUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находится в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $attachment = Attachment::moderatorLimit($answer)
            ->findOrFail($id);
        // dd($attachment);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $attachment);

        $article = $attachment->article;
        // dd($article);

        if ($article->draft) {
            $attachment->unit_for_composition_id = $request->unit_for_composition_id;

            $attachment->portion_status = $request->portion_status ?? 0;
            $attachment->portion_abbreviation = $request->portion_abbreviation;
            $attachment->unit_portion_id = $request->unit_portion_id;
            $attachment->portion_count = $request->portion_count;

            $attachment->price_unit_id = $request->price_unit_id;
            $attachment->price_unit_category_id = $request->price_unit_category_id;

            $attachment->serial = $request->serial;
        }

        $result = $this->updateArticle($request, $attachment);
        // Если результат не массив с ошибками, значит все прошло удачно

        if (!is_array($result)) {

            $attachment->display = $request->display;
            $attachment->system = $request->system;

            $attachment->save();

            // ПЕРЕНОС ГРУППЫ ТОВАРА В ДРУГУЮ КАТЕГОРИЮ ПОЛЬЗОВАТЕЛЕМ
            $this->changeCategory($request, $attachment);

            // Метрики
            if ($request->has('metrics')) {
                // dd($request);

                $metrics_insert = [];
                foreach ($request->metrics as $metric_id => $value) {
                    if (is_array($value)) {
                        $metrics_insert[$metric_id]['value'] = implode(',', $value);
                    } else {
//                        if (!is_null($value)) {
                        $metrics_insert[$metric_id]['value'] = $value;
//                        }
                    }
                }
                $attachment->metrics()->syncWithoutDetaching($metrics_insert);
            }

            // Если ли есть
            if ($request->cookie('backlink') != null) {
                $backlink = Cookie::get('backlink');
                return Redirect($backlink);
            }

            if ($request->has('paginator_url')) {
                return redirect($request->paginator_url);
            }

            return redirect()->route('attachments.index');
        } else {

            return back()
                ->withErrors($result)
                ->withInput();
        }
    }

    public function destroy($id)
    {
        //
    }

    public function archive(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, 'delete');

        // ГЛАВНЫЙ ЗАПРОС:
        $attachment = Attachment::with([
            'compositions.goods',
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod('destroy'), $attachment);

        if ($attachment) {

            $attachment->archive = true;

            // Скрываем бога
            $attachment->editor_id = hideGod($request->user());
            $attachment->save();

            if ($attachment) {
                return redirect()->route('attachments.index');
            } else {
                abort(403, 'Ошибка при архивации вложений');
            }
        } else {
            abort(403, 'Вложение не найдено');
        }
    }

    public function replicate(Request $request, $id)
    {
        $attachment = Attachment::findOrFail($id);

        $attachment->load('article');
        $article = $attachment->article;
        $new_article = $this->replicateArticle($request, $attachment);

        $new_attachment = $attachment->replicate();
        $new_attachment->article_id = $new_article->id;
        $new_attachment->save();

        $attachment->load('metrics');
        if ($attachment->metrics->isNotEmpty()) {
            $metrics_insert = [];
            foreach ($attachment->metrics as $metric) {
                $metrics_insert[$metric->id]['value'] = $metric->pivot->value;
            }
            $res = $new_attachment->metrics()->attach($metrics_insert);
        }

        if($article->kit) {
            $article->load('attachments');
            if ($article->attachments->isNotEmpty()) {
                $attachments_insert = [];
                foreach ($article->attachments as $attachment) {
                    $attachments_insert[$attachment->id]['value'] = $attachment->pivot->value;
                }
                $res = $new_article->raws()->attach($attachments_insert);
            }
        }

        return redirect()->route('attachments.index');
    }

    // --------------------------------------------- Ajax -------------------------------------------------

    public function ajax_get_attachment(Request $request)
    {
        $attachment = Attachment::with([
            'article.group.unit',
            'category'
        ])
            ->find($request->id);

        return view('products.articles.goods.attachments.attachment_input', compact('attachment'));
    }

    // Добавляем состав
    public function ajax_get_category_attachment(Request $request)
    {

        $attachment = Attachment::with([
            'article.group.unit',
            'category'
        ])
            ->findOrFail($request->id);

        return view('products.articles_categories.goods_categories.attachments.attachment_tr', compact('attachment'));
    }
}
