<?php

namespace App;

use App\Models\System\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class Entity extends BaseModel
{
    use SoftDeletes;

    protected $dates = [
        'deleted_at'
    ];

    protected $fillable = [
        'name',
        'alias',
        'model',
        'view_path',

        'rights',

        'ancestor_id',
        'author_id',

        'display',
        'system',
        'moderation'
    ];

    public function actions()
    {
        return $this->belongsToMany(Action::class, 'action_entity', 'entity_id', 'action_id');
    }

    public function actionentities()
    {
        return $this->hasMany(ActionEntity::class);
    }

    public function pages()
    {
        return $this->belongsToMany(Page::class);
    }

    public function booklists()
    {
        return $this->hasMany(Booklist::class);
    }

    /**
     * Настройки фоток по компании авторизованного пользователя
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphOne
     */
    public function photo_settings()
    {
        return $this->morphOne(PhotoSetting::class, 'photo_settings')
            ->where('company_id', auth()->user()->company_id);
    }

    // Предок
    public function ancestor()
    {
        return $this->belongsTo(Entity::class);
    }

    // Потомок
    public function descendant()
    {
        return $this->hasOne(Entity::class, 'ancestor_id');
    }

    // Состав
    public function consist()
    {
        return $this->belongsTo(Entity::class, 'consist_id')
        ->where('tmc', true);
    }

}
