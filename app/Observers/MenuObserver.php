<?php

namespace App\Observers;

use App\Observers\Traits\CategoriesTrait;
use App\Observers\Traits\CommonTrait;
use App\Menu as Category;

class MenuObserver
{

    public function __construct()
    {
        $this->request = request();
    }

    use CommonTrait;
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
        $this->setNewBlank($category);
    }

    protected function setTag(Category $category)
    {
        $request = $this->request;
        $tag = empty($request->tag) ? \Str::slug($category->name) : $request->tag;
        $category->tag = $tag;
    }

    protected function setNewBlank(Category $category)
    {
        $request = $this->request;
        $category->new_blank = $request->has('new_blank');
    }
}
