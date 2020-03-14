<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\Photable;
use App\Http\Requests\AttachmentsCategoryUpdateRequest;
use App\Http\Requests\AttachmentsCategoryStoreRequest;
use App\AttachmentsCategory;
use Illuminate\Http\Request;

class AttachmentsCategoryController extends Controller
{

    // Настройки сконтроллера
    public function __construct(AttachmentsCategory $attachments_category)
    {
        $this->middleware('auth');
        $this->attachments_category = $attachments_category;
        $this->class = AttachmentsCategory::class;
        $this->model = 'App\AttachmentsCategory';
        $this->entity_alias = with(new $this->class)->getTable();
        $this->entity_dependence = false;
        $this->type = 'edit';
    }

    use Photable;

    public function index(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $attachments_categories = AttachmentsCategory::with([
            'attachments',
            'childs',
            'groups'
        ])
            ->withCount('childs')
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->template($answer)
            ->orderBy('moderation', 'desc')
            ->orderBy('sort', 'asc')
            ->get();

        // Отдаем Ajax
        if ($request->ajax()) {

            return view('system.common.accordions.categories_list',
                [
                    'items' => $attachments_categories,
                    'entity' => $this->entity_alias,
                    'class' => $this->model,
                    'type' => $this->type,
                    'count' => $attachments_categories->count(),
                    'id' => $request->id,
                    // 'nested' => 'attachments_products_count',
                ]
            );
        }

        // Отдаем на шаблон
        return view('system.common.accordions.index',
            [
                'items' => $attachments_categories,
                'page_info' => pageInfo($this->entity_alias),
                'entity' => $this->entity_alias,
                'class' => $this->model,
                'type' => $this->type,
                'id' => $request->id,
                'nested' => 'childs_count',
                'filter' => setFilter($this->entity_alias, $request, [
                    'booklist',
                ]),
            ]
        );
    }

    public function create(Request $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        return view('system.common.accordions.create', [
            'item' => new $this->class,
            'entity' => $this->entity_alias,
            'title' => 'Добавление категории вложений',
            'parent_id' => $request->parent_id,
            'category_id' => $request->category_id
        ]);
    }

    public function store(AttachmentsCategoryStoreRequest $request)
    {

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $this->class);

        $data = $request->input();
        $attachments_category = (new $this->class())->create($data);

        if ($attachments_category) {
            // Переадресовываем на index
            return redirect()->route('attachments_categories.index', ['id' => $attachments_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при записи категории вложений!',
            ];
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

        // ГЛАВНЫЙ ЗАПРОС:
        $attachments_category = AttachmentsCategory::with([
            'manufacturers',
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);
        // dd($attachments_category);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $attachments_category);

        // Инфо о странице
        $page_info = pageInfo($this->entity_alias);

        $settings = getPhotoSettings($this->entity_alias);

        // При добавлении метрики отдаем ajax новый список свойст и метрик
        if ($request->ajax()) {
            return view('products.common.metrics.properties_list', [
                'category' => $attachments_category,
                'page_info' => $page_info,
            ]);
        }

        return view('products.articles_categories.common.edit.edit', [
            'title' => 'Редактирование категории вложений',
            'category' => $attachments_category,
            'page_info' => $page_info,
            'settings' => $settings,
            'entity' => $this->entity_alias,
        ]);
    }

    public function update(AttachmentsCategoryUpdateRequest $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        $attachments_category = AttachmentsCategory::moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $attachments_category);

        $data = $request->input();
        $data['photo_id'] = $this->getPhotoId($request, $attachments_category);
        $result = $attachments_category->update($data);

        if ($result) {

            $attachments_category->manufacturers()->sync($request->manufacturers);
            $attachments_category->metrics()->sync($request->metrics);

            // Переадресовываем на index
            return redirect()->route('attachments_categories.index', ['id' => $attachments_category->id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при обновлении категории вложений!'
            ];
        }
    }

    public function destroy(Request $request, $id)
    {

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entity_alias, $this->entity_dependence, getmethod(__FUNCTION__));

        // ГЛАВНЫЙ ЗАПРОС:
        $attachments_category = AttachmentsCategory::with([
            'childs',
            'attachments'
        ])
            ->moderatorLimit($answer)
            ->findOrFail($id);

        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), $attachments_category);

        $parent_id = $attachments_category->parent_id;

        $attachments_category->delete();

        if ($attachments_category) {

            // Переадресовываем на index
            return redirect()->route('attachments_categories.index', ['id' => $parent_id]);
        } else {
            $result = [
                'error_status' => 1,
                'error_message' => 'Ошибка при удалении категории!'
            ];
        }
    }
}
