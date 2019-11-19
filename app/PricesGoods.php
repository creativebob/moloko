<?php
	
	namespace App;
	
	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
	use App\Scopes\Traits\CompaniesLimitTraitScopes;
	use App\Scopes\Traits\AuthorsTraitScopes;
	use App\Scopes\Traits\SystemItemTraitScopes;
	use App\Scopes\Traits\FilialsTraitScopes;
	use App\Scopes\Traits\TemplateTraitScopes;
	use App\Scopes\Traits\ModeratorLimitTraitScopes;
	use App\Scopes\Traits\SuppliersTraitScopes;

// Подключаем кеш
	use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
	use App\Scopes\Filters\Filter;
	use App\Scopes\Filters\BooklistFilter;
	
	class PricesGoods extends Model
	{
		// Включаем кеш
		use Cachable;
		
		use Notifiable;
		use SoftDeletes;
		
		// Включаем Scopes
		use CompaniesLimitTraitScopes;
		use AuthorsTraitScopes;
		use SystemItemTraitScopes;
		use FilialsTraitScopes;
		use TemplateTraitScopes;
		use ModeratorLimitTraitScopes;
		use SuppliersTraitScopes;
		
		// Фильтры
		use Filter;
		use BooklistFilter;
		
		protected $table = 'prices_goods';
		
		protected $fillable = [
			'catalogs_goods_item_id',
			'catalogs_goods_id',
			'goods_id',
			'filial_id',
			'price',
			'archive',

            'currency_id',
			
			'status',
            'is_hit',
			
			'display',
			'system',
			'moderation'
		];
		
		
		// Каталог
		public function catalog()
		{
			return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id');
		}
		
		// Пункты каталога
		public function catalogs_item()
		{
			return $this->belongsTo(CatalogsGoodsItem::class, 'catalogs_goods_item_id');
		}
		
		public function catalogs_item_public()
		{
			return $this->belongsTo(CatalogsGoodsItem::class, 'catalogs_goods_item_id')
				->where('display', true);
		}
		
		// Филиал
		public function filial()
		{
			return $this->belongsTo(Department::class);
		}
		
		// Товар
		public function goods()
		{
			return $this->belongsTo(Goods::class);
		}
		
		public function goods_public()
		{
			return $this->belongsTo(Goods::class, 'goods_id')
				->with('article')
				->whereHas('article', function ($q) {
					$q->with([
						'raws'
					])
						->where([
							'draft' => false
						]);
				})
				->where([
					'display' => true,
					'archive' => false,
				]);
		}
		
		// История
		public function history()
		{
			return $this->hasMany(PricesGoodsHistory::class, 'prices_goods_id');
		}
		
		// Актуальная цена
		public function actual_price()
		{
			return $this->hasOne(PricesGoodsHistory::class, 'prices_goods_id')
				->whereNull('end_date');
		}
		
		// Предок
		public function ancestor()
		{
			return $this->belongsTo(PricesGoods::class);
		}
		
		// Последователь
		public function follower()
		{
			return $this->hasOne(PricesGoods::class, 'ancestor_id')
				->where('archive', false);
		}
		
		// Общее отношение для товаров и услуг
		public function product()
		{
			return $this->belongsTo(Goods::class, 'goods_id');
		}

        // Валюта
        public function currency()
        {
            return $this->belongsTo(Currency::class);
        }
		
		// Фильтр
		public function scopeFilter($query)
		{
			if (request('price')) {
				$price = request('price');
				if (isset($price['min'])) {
                    $query->where('price', '>=', $price['min']);
                }
                if (isset($price['max'])) {
                    $query->where('price', '<=', $price['max']);
                }
                $query->orderBy('price');
			}
			
			if (request('weight')) {
				$weight = request('weight');
				$query->whereHas('goods_public', function($q) use ($weight) {
					$q->whereHas('article', function($q) use ($weight) {
						$q->where('weight', '>=', $weight['min'] / 1000)
							->where('weight', '<=', $weight['max'] / 1000);
					});
				});
			}

	        if (request('catalogs_goods_item')) {
	            $catalogs_goods_item = request('catalogs_goods_item');
	            $query->where('catalogs_goods_item_id', $catalogs_goods_item);
	        }
	        
			if (request('raws_articles_groups')) {
				$raws_articles_groups = request('raws_articles_groups');
//		    dd($raws_articles_groups);
				
				$query->whereHas('goods_public', function($q) use ($raws_articles_groups) {
					$q->whereHas('article', function($q) use ($raws_articles_groups) {
						foreach($raws_articles_groups as $item){
							$q->whereHas('attachments',function($q) use ($item) {
								$q->whereHas('article', function ($q) use ($item) {
									$q->where('articles_group_id', $item);
								});
							});
						}
					});
				});
			}
			
			return $query;
		}


//    public function scopeFilter(Builder $builder, QueryFilter $filters)
//    {
//        return $filters->apply($builder);
//    }
	}
