<?php

namespace App;

use App\Models\System\BaseModel;
use App\Models\System\Traits\Dispatchable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Mailing extends BaseModel
{
    use SoftDeletes,
        Cachable,
        Dispatchable;

    protected $withCount = [
        'dispatches',
        'sendedDispatches',
        'waitingDispatches'
    ];

    protected $dates = [
        'started_at',
        'begined_at',
        'ended_at',
    ];

    protected $fillable = [
        'name',
        'description',

        'subject',
        'from_name',
        'from_email',

        'template_id',
        'mailing_list_id',

        'is_active',
        'started_at',
        'begined_at',
        'ended_at',

        'display',
        'system',
        'moderation'
    ];

    public function getStatusAttribute()
    {
        $status = 'Неопределен';

        if ($this->is_active == 0) {
            if (empty($this->begined_at) && empty($this->ended_at)) {
                $status = 'Не активна';
            }
            if (isset($this->begined_at) && empty($this->ended_at)) {
                $status = 'Приостановлена';
            }
        }

        if ($this->is_active == 1) {
            if (empty($this->begined_at) && empty($this->ended_at)) {
                $status = 'В очереди';
            }
            if (isset($this->begined_at) && empty($this->ended_at)) {
                $status = 'Идет рассылка';
            }
            if (isset($this->begined_at) && isset($this->ended_at)) {
                $status = 'Завершена';
            }
        }

        return $status;
    }

    public function setStartedAtAttribute($value)
    {
        $this->attributes['started_at'] = Carbon::createFromFormat('d.m.Y', $value);
    }

    public function list() {
        return $this->belongsTo(MailingList::class, 'mailing_list_id')
            ->withDefault([
                'name' => 'Рассылка в ручную'
            ]);
    }

    public function template() {
        return $this->belongsTo(Template::class);
    }
}
