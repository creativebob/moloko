<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\Commonable;
use App\Observers\Traits\DirectionTrait;
use App\Observers\Traits\ProductsCategoriesTrait;
use App\ServicesCategory as Category;

class ServicesCategoryObserver
{

    public function __construct()
    {
        $this->model ='App\ServicesCategory';
    }

    use Commonable;
    use CategoriesTrait;
    use ProductsCategoriesTrait;
    use DirectionTrait;

    public function creating(Category $category)
    {
        $this->store($category);
        $this->storeCategory($category);
    }

    public function updating(Category $category)
    {
        $this->update($category);
        $this->updateCategory($category);
    }

    public function updated(Category $category)
    {
        $this->updateCategoryChildsSlug($category);
        $this->updateCategoryChildsLevel($category);
        $this->updateCategoryChildsCategoryId($category);

        $this->checkDirection($category);
    }

    public function deleting(Category $category)
    {
        $this->destroy($category);
    }

    protected function syncWorkflows($category)
    {
        $request = request();
        $category->workflows()->sync($request->workflows);
    }
}
