<?php

namespace App\Observers\System;

use App\Observers\System\Traits\Categorable;
use App\TemplatesCategory as Category;

class TemplatesCategoryObserver extends BaseObserver
{

    use Categorable;

    /**
     * Handle the templatesCategory "creating" event.
     *
     * @param Category $category
     */
    public function creating(Category $category)
    {
        $this->store($category);
        $this->storeCategory($category);
    }

    /**
     * Handle the templatesCategory "updating" event.
     *
     * @param Category $category
     */
    public function updating(Category $category)
    {
        $this->update($category);
        $this->updateCategory($category);
    }

    /**
     * Handle the templatesCategory "updated" event.
     *
     * @param Category $category
     */
    public function updated(Category $category)
    {
        $this->updateCategoryChildsSlug($category);
        $this->updateCategoryChildsLevel($category);
        $this->updateCategoryChildsCategoryId($category);
    }

    /**
     * Handle the templatesCategory "deleting" event.
     *
     * @param Category $category
     */
    public function deleting(Category $category)
    {
        $this->destroy($category);
    }
}
