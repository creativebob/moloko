<?php

namespace App\Observers\Traits;

trait CategoriesTrait
{

    public function storeCategory($category)
    {
        $this->setCategorySlug($category);
        $this->setCategoryLevel($category);
        $this->setCategoryCategoryId($category);
    }

    public function updateCategory($category)
    {
        $request = request();

        $category->load('childs');

        if ($category->isDirty('name') || $category->isDirty('parent_id')) {
            $this->setCategorySlug($category);
            $this->setCategoryLevel($category);
            $this->setCategoryCategoryId($category);
        }

        $category->photo_id = savePhoto($request, $category);
    }

    protected function setCategorySlug($category)
    {
        $category_slug = \Str::slug($category->name);

        if (isset($category->parent_id)) {
            $slug = $category->parent->slug . '/' . $category_slug;
        } else {
            $slug = $category_slug;
        }

        $category->slug = $slug;
    }

    protected function setCategoryLevel($category)
    {

        if (isset($category->parent_id)) {
            $level = $category->parent->level + 1;
        } else {
            $level = 1;
        }

        $category->level = $level;
    }

    protected function setCategoryCategoryId($category)
    {
        if (isset($category->parent_id)) {
            $parent = $category->parent;
            $category_id = isset($parent->category_id) ? $parent->category_id : $parent->id;
            $category->category_id = $category_id;
        }
    }

    protected function updateCategoryChildsSlug($category)
    {
        if ($category->childs->isNotEmpty()) {
            foreach ($category->childs as $child) {
                $this->setCategorySlug($child);
                $child->save();

                $this->updateCategoryChildsSlug($child);
            }
        }
    }

    protected function updateCategoryChildsLevel($category)
    {
        if ($category->childs->isNotEmpty()) {
            foreach ($category->childs as $child) {
                $this->setCategoryLevel($child);
                $child->save();

                $this->updateCategoryChildsLevel($child);
            }
        }
    }

    protected function updateCategoryChildsCategoryId($category)
    {
        if ($category->childs->isNotEmpty()) {
            foreach ($category->childs as $child) {
                $this->setCategoryCategoryId($child);
                $child->save();

                $this->updateCategoryChildsCategoryId($child);
            }
        }
    }

}
