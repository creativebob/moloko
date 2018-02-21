<?php

namespace App\Http\Controllers;

// Подключаем модели
use App\Area;
// Политика
use App\Policies\AreaPolicy;
// Подключаем фасады
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AreaController extends Controller
{
  // Сущность над которой производит операции контроллер
  protected $entity_name = 'areas';
  protected $entity_dependence = false;

  public function index()
  {
    //
  }

  public function create()
  {
    //
  }

  public function store(Request $request)
  {
    //
  }

  public function show($id)
  {
    //
  }

  public function edit($id)
  {
    //
  }

  public function update(Request $request, $id)
  {
    //
  }

  public function destroy(Request $request, $id)
  {

    // Получаем метод
    $method = 'delete';

    // Получаем из сессии необходимые данные (Функция находиться в Helpers)
    $answer = operator_right('areas', $this->entity_dependence, $method);

    // Удаляем с обновлением
    // Находим область и район города
    $area = Area::with('cities')->moderatorLimit($answer)->findOrFail($id);
    $region_id = $area->region_id;
    
    // Подключение политики
    $this->authorize('delete', $area);
    // dd($area);
    $user = $request->user();
    if (count($area->cities) > 0) {
      abort(403, 'Район не пустой!');
    } else {
      $area->editor_id = $user->id;
      $area->save();
      // Удаляем район с обновлением
      $area = Area::destroy($id);
      if ($area) {
        return Redirect('current_city/'.$region_id.'/0');
      } else {
        abort(403, 'Ошибка при удалении района!');
      }
    }
  }
}
