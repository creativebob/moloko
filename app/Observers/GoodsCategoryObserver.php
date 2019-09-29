<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\Commonable;
use App\GoodsCategory as Category;
use App\Observers\Traits\DirectionTrait;
use App\Observers\Traits\MetricTrait;
use App\Observers\Traits\ProductsCategoriesTrait;

class GoodsCategoryObserver
{
    public function __construct()
    {
        $this->model ='App\GoodsТCategory';
    }

    use Commonable;
    use CategoriesTrait;
    use ProductsCategoriesTrait;
    use DirectionTrait;
    use MetricTrait;

    public function creating(Category $category)
    {
        $this->store($category);
        $this->storeCategory($category);
    }

    public function updating(Category $category)
    {
        $this->update($category);
        $this->updateCategory($category);

//        $this->syncManufacturers($category);
//        $this->syncRaws($category);
//
//        $this->syncMetrics($category);

        $this->checkDirection($category);
    }

    public function updated(Category $category)
    {
        $this->updateCategoryChildsSlug($category);
        $this->updateCategoryChildsLevel($category);
        $this->updateCategoryChildsCategoryId($category);
    }

    public function deleting(Category $category)
    {
        $this->destroy($category);
    }
//
//    protected function syncRaws($category)
//    {
//        $request = request();
//        $category->raws()->sync($request->raws);
//    }

}
