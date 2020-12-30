<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Categorable;
use App\Observers\System\Traits\Commonable;
use App\GoodsCategory as Category;
use App\Observers\System\Traits\Directionable;

class GoodsCategoryObserver
{
    use Commonable;
    use Categorable;
    use Directionable;

    public function creating(Category $category)
    {
        $this->store($category);
        $this->storeCategory($category);
    }

    public function updating(Category $category)
    {
        $this->update($category);
        $this->updateCategory($category);

        $this->checkDirection($category, 'App\GoodsТCategory');
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
