<?php

	namespace App;

	use Illuminate\Database\Eloquent\Model;
	use Illuminate\Notifications\Notifiable;
	use Illuminate\Database\Eloquent\SoftDeletes;

// Scopes для главного запроса
	use App\Scopes\Traits\CompaniesLimitTraitScopes;
	use App\Scopes\Traits\AuthorsTraitScopes;
	use App\Scopes\Traits\SystemItemTraitScopes;
// use App\Scopes\Traits\FilialsTraitScopes;
	use App\Scopes\Traits\TemplateTraitScopes;
	use App\Scopes\Traits\ModeratorLimitTraitScopes;

// Подключаем кеш
	use GeneaLabs\LaravelModelCaching\Traits\Cachable;

// Фильтры
use App\Scopes\Filters\Filter;
use App\Scopes\Filters\BooklistFilter;
// use App\Scopes\Filters\DateIntervalFilter;

	class CatalogsGoodsItem extends Model
	{

		// Включаем кеш
		use Cachable;

		use SoftDeletes;

		// Включаем Scopes
		use CompaniesLimitTraitScopes;
		use AuthorsTraitScopes;
		use SystemItemTraitScopes;
		// use FilialsTraitScopes;
		use TemplateTraitScopes;
		use ModeratorLimitTraitScopes;

		// Фильтры
		use Filter;
		use BooklistFilter;
		// use DateIntervalFilter;

		protected $dates = ['deleted_at'];

		protected $fillable = [
			'name',
			'description',
			'title',
			'seo_description',
			'parent_id',
			'photo_id',
			'catalogs_goods_id',
            'color',

            'display_mode_id',
            'directive_category_id',

            'is_controllable_mode',
            'is_show_subcategory',
            'is_hide_submenu',

            'is_discount',

			'display',
			'system',
			'moderation'
		];

		// Каталог
		public function catalog()
		{
			return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id');
		}

		public function catalog_public()
		{
			return $this->belongsTo(CatalogsGoods::class, 'catalogs_goods_id')
				->where('display', true);
		}

		// Родитель
		public function parent()
		{
			return $this->belongsTo(CatalogsGoodsItem::class);
		}

		// Вложенные
		public function childs()
		{
			return $this->hasMany(CatalogsGoodsItem::class, 'parent_id');
		}

		// Главный
		public function category()
		{
			return $this->belongsTo(CatalogsGoodsItem::class);
		}

		// Аватар
		public function photo()
		{
			return $this->belongsTo(Photo::class);
		}

		// Автор
		public function author()
		{
			return $this->belongsTo(User::class);
		}

		// Прайс
		public function prices_goods()
		{
			return $this->hasMany(PricesGoods::class);
		}

		public function prices()
		{
			return $this->hasMany(PricesGoods::class);
		}

		public function prices_public()
		{
			return $this->hasMany(PricesGoods::class)
				->has('goods_public')
				->where([
					'display' => true,
					'archive' => false
				]);
		}

        public function childs_prices_public()
        {
            return $this->hasManyThrough(PricesGoods::class, CatalogsGoodsItem::class, 'parent_id', 'catalogs_goods_item_id')
                ->has('goods_public')
                ->where([
                    'prices_goods.display' => true,
                    'prices_goods.archive' => false
                ]);
        }

		// Товары каталога
		public function goods()
		{
			return $this->belongsToMany(Goods::class, 'price_goods', 'catalogs_goods_item_id', 'goods_id');
		}

		// Фильтры
        public function filters()
        {
            return $this->belongsToMany(Metric::class, 'filters_goods', 'catalogs_goods_item_id', 'metric_id');
        }

        public function display_mode()
        {
            return $this->belongsTo(DisplayMode::class);
        }

        public function directive_category()
        {
            return $this->belongsTo(UnitsCategory::class);
        }

        public function discounts()
        {
            return $this->belongsToMany(Discount::class, 'discount_catalogs_goods_item', 'catalogs_goods_item_id', 'discount_id');
        }

        public function discounts_actual()
        {
            return $this->belongsToMany(Discount::class, 'discount_catalogs_goods_item', 'catalogs_goods_item_id', 'discount_id')
                ->where('archive', false)
                ->where('begined_at', '<=', now())
                ->where(function ($q) {
                    $q->where('ended_at', '>=', now())
                        ->orWhereNull('ended_at');
                });
        }

	    public function getNameWithParentAttribute()
	    {
	        if($this->parent_id != null){
	            return $this->parent->name . ' / ' . $this->name;
	        } else {
	            return $this->name;
	        }
	    }

	}
