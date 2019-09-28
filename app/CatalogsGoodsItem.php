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
		
		// Товары каталога
		public function goods()
		{
			return $this->belongsToMany(Goods::class, 'price_goods', 'catalogs_goods_item_id', 'goods_id');
		}
	}
