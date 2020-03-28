<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Categorable;
use App\PortfoliosItem as Category;
use App\Observers\System\Traits\Commonable;

class PortfoliosItemObserver
{

    use Commonable;
    use Categorable;

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
    }

    public function deleting(Category $category)
    {
        $this->destroy($category);
    }
}
