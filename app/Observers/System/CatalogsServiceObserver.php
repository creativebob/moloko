<?php

namespace App\Observers\System;

use App\CatalogsService;

class CatalogsServiceObserver extends BaseObserver
{
    /**
     * Handle the catalogsService "creating" event.
     *
     * @param CatalogsService $catalogsService
     */
    public function creating(CatalogsService $catalogsService)
    {
        $this->store($catalogsService);
    }

    /**
     * Handle the catalogsService "updating" event.
     *
     * @param CatalogsService $catalogsService
     */
    public function updating(CatalogsService $catalogsService)
    {
        $this->update($catalogsService);
    }

    /**
     * Handle the catalogsService "deleting" event.
     *
     * @param CatalogsService $catalogsService
     */
    public function deleting(CatalogsService $catalogsService)
    {
        $this->destroy($catalogsService);
    }

    /**
     * Handle the catalogsService "saving" event.
     *
     * @param CatalogsService $catalogsService
     */
    public function saving(CatalogsService $catalogsService)
    {
        $this->setSlug($catalogsService);
    }

}
