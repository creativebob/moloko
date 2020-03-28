<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Categorable;
use App\Observers\System\Traits\Commonable;
use App\Observers\System\Traits\Directionable;
use App\ServicesCategory as Category;

class ServicesCategoryObserver
{

    public function __construct()
    {
        $this->model ='App\ServicesCategory';
    }

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
}
