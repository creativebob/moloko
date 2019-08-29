<?php
	
	namespace App\Http\Controllers\Project;
	
	use App\CatalogsGoodsItem;
	use App\Filters\Vkusnyashka\PricesGoodsFilter;
	use App\PricesGoods;
	use App\Site;
	use Illuminate\Http\Request;
	use App\Http\Controllers\Controller;
	
	class AppController extends Controller
	{
		
		// Настройки контроллера
		public function __construct(Request $request)
		{
//        $domain = $request->getHttpHost();
			$domain = $request->getHost();
//        dd($domain);
			
			$site = Site::where('domain', $domain)
				->with([
					'pages_public',
					'filials'
				])
				->first();
//        dd($site);
			
			$this->site = $site;
		}
		
		public function start(Request $request)
		{
			if (is_null($this->site)) {
				return view('project.pages.mains.main');
			} else {
				$site = $this->site;
				$page = $site->pages_public
					->where('alias'. 'main')
					->first();
				
				return view($site->alias.'.pages.mains.index', compact('site','page'));
			}
		}
		
		public function catalogs_goods(Request $request, $catalog_slug, $catalog_item_slug)
		{

//        dd($request);
			
			$site = $this->site;
			
			$page = $site->pages_public->where('alias', 'catalogs-goods')->first();
			
			$catalog_goods_item = CatalogsGoodsItem::whereHas('catalog_public', function ($q) use ($site, $catalog_slug) {
				$q->whereHas('sites', function ($q) use ($site) {
					$q->where('id', $site->id);
				})
					->where('slug', $catalog_slug);
			})
				->where([
					'slug' => $catalog_item_slug,
					'display' => true
				
				])
				->first();
//        dd($catalog_goods_item);
			
			$page->title = $catalog_goods_item->title;
			
			$prices_goods = PricesGoods::with([
				'goods_public'
			])
				->whereHas('catalogs_item_public', function ($q) use ($site, $catalog_slug, $catalog_item_slug) {
					$q->whereHas('catalog_public', function ($q) use ($site, $catalog_slug) {
						$q->whereHas('sites', function ($q) use ($site) {
							$q->where('id', $site->id);
						})
							->where('slug', $catalog_slug);
					})
						->where('slug', $catalog_item_slug);
					
				})
				->has('goods_public')
				->where([
					'display' => true,
					'archive' => false
				])
				->filter($request)
				->get();
//        dd($prices_goods->filter($request));
			
			
			return view($site->alias.'.pages.catalogs_goods.index', compact('site','page', 'request', 'catalog_goods_item', 'prices_goods'));
		}
		
		public function catalogs_services(Request $request, $catalog_slug, $catalog_item_slug)
		{
			$site = $this->site;
			
			// Вытаскивает через сайт каталог и его пункт с прайсами (не архивными), товаром и артикулом
			$site->load(['catalogs_services' => function ($q) use ($catalog_slug, $catalog_item_slug) {
				$q->with([
					'items' => function($q) use ($catalog_item_slug) {
						$q->with([
							'prices_services' => function ($q) {
								$q->with([
									'service' => function ($q) {
										$q->with(['process' => function ($q) {
											$q->where([
												'draft' => false
											]);
										}])
											->where([
												'display' => true,
												'archive' => false
											]);
									}
								])
									->where([
										'display' => true,
										'archive' => false
									]);
							}
						])
							->where([
								'slug' => $catalog_item_slug,
								'display' => true,
							]);
					}
				])
					->where([
						'slug' => $catalog_slug,
						'display' => true,
					]);
			}]);
			dd($site->catalogs_services->first()->items->first());
		}
		
		public function prices_goods(Request $request, $id)
		{
			$pice_goods = PricesGoods::with([
				'goods_public'
			])
				->where([
					'id' => $id,
					'display' => true
				])->first();
			
			dd($pice_goods);
		}
	}
