?php

namespace App\Http\ViewComposers\Project;

use Illuminate\View\View;

class CatalogsComposer
{
	public function compose(View $view)
	{

        $site = $view->site->load(['catalogs' => function ($q) {
            $q
            ->with(['items' => function ($q) {
                $q->orderBy('sort');
            }])
            ->select([
                'catalogs.id',
                'name',
            ])
        	->where('display', true)
        	->orderBy('sort');
        }]);
        // dd($site);

        $catalog = $site->catalogs->first();
        // dd($catalog);

        return $view->with('catalog', $catalog);
    }

}