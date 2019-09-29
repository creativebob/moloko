<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\Commonable;
use App\Sector as Category;

class SectorObserver
{

    public function __construct()
    {
        $this->request = request();
    }

    use Commonable;
    use CategoriesTrait;

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

    public function saving(Category $category)
    {
        $this->setTag($category);
    }

    protected function setTag(Category $category)
    {
        $request = $this->request;
        $tag = empty($request->tag) ? \Str::slug($category->name) : $request->tag;
        $category->tag = $tag;
    }
}
