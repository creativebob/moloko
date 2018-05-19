<?php
namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;
use Input;
use App\Product;
use DB;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;

use App\Http\Controllers\Session;


class MaatwebsiteController extends Controller
{
	public function importExport()
	{
		return view('products.exel');
	}

	public function downloadExcel($type)
	{
		$data = Product::get()->toArray();

		return Excel::create('products-'.Carbon::now()->format('d.m.Y'), function($excel) use ($data) {
			$excel->sheet('Продукция', function($sheet) use ($data)
			{
				$sheet->fromArray($data);
			});
		})->download($type);
	}

	public function importExcel(Request $request)
	{
		if($request->hasFile('import_file')){

			// Получаем данные для авторизованного пользователя
			$user = $request->user();

    	// Смотрим компанию пользователя
			$company_id = $user->company_id;
			if($company_id == null) {
				abort(403, 'Необходимо авторизоваться под компанией');
			}

    	// Скрываем бога
			$user_id = hideGod($user);

			Excel::load($request->file('import_file')->getRealPath(), function ($reader) use ($user_id, $company_id){
				foreach ($reader->toArray() as $key => $row) {
					$data['company_id'] = $company_id;
					$data['name'] = $row['name'];
					$data['article'] = $row['article'];
					$data['cost'] = $row['cost'];
					// $data['description'] = $row['description'];
					$data['author_id'] = $user_id;

					if(!empty($data)) {
						DB::table('products')->insert($data);
					}
				}
			});
		}

		// Session::put('success', 'Youe file successfully import in database!!!');

		return back();
	}
}