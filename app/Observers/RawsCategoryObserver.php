<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\CommonTrait;
use App\Observers\Traits\ProductsCategoriesTrait;
use App\RawsCategory as Category;

class RawsCategoryObserver
{

    use CommonTrait;
    use CategoriesTrait;
    use ProductsCategoriesTrait;

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

        $this->syncManufacturers($category);
    }

    public function deleting(Category $category)
    {
        $this->destroy($category);
    }
}
