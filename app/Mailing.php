<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use GeneaLabs\LaravelModelCaching\Traits\Cachable;

class Mailing extends BaseModel
{
    use SoftDeletes;
    use Cachable;

    protected $fillable = [
        'name',
        'description',

        'subject',
        'from_name',
        'from_email',

        'template_id',
        'mailing_list_id',

        'display',
        'system',
        'moderation'
    ];
    
    public function list() {
        return $this->belongsTo(MailingList::class, 'mailing_list_id');
    }
    
    public function template() {
        return $this->belongsTo(Template::class);
    }
}
