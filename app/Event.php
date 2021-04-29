<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Processable;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends BaseModel
{
    use SoftDeletes;
    use Processable;

    const ALIAS = 'services';
    const DEPENDENCE = false;

    protected $fillable = [
        'category_id',
        'process_id',

        'archive',
        'serial',

        'display',
        'system',
        'moderation'
    ];

    public function category()
    {
        return $this->belongsTo(EventsCategory::class);
    }
    /**
     * Фильтр
     *
     * @param $query
     * @return mixed
     */
    public function scopeFilter($query)
    {
        $filters = $this->getFilters(self::ALIAS);

        if (isset($filters['events_categories'])) {
            $query->whereIn('category_id', request('events_categories'));
        }

        if (isset($filters['authors'])) {
            $query->whereIn('author_id', request('authors'));
        }
    }
}
