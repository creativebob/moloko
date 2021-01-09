<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Directionable;
use App\ServicesCategory as Category;

class ServicesCategoryObserver extends CategoryObserver
{
    use Directionable;

    /**
     * Handle the category "creating" event.
     *
     * @param Category $category
     */
    public function creating(Category $category)
    {
        $this->store($category);
        $this->storeCategory($category);
    }

    /**
     * Handle the category "updating" event.
     *
     * @param Category $category
     */
    public function updating(Category $category)
    {
        $this->update($category);
        $this->updateCategory($category);
    }

    /**
     * Handle the category "updated" event.
     *
     * @param Category $category
     */
    public function updated(Category $category)
    {
        $this->updateCategoryChildsSlug($category);
        $this->updateCategoryChildsLevel($category);
        $this->updateCategoryChildsCategoryId($category);

        $this->checkDirection($category, 'App\ServicesCategory');
    }

    /**
     * Handle the category "deleting" event.
     *
     * @param Category $category
     */
    public function deleting(Category $category)
    {
        $this->destroy($category);
    }
}
