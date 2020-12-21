<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as EloquentModel;

abstract class Model extends EloquentModel
{
    use FiledFilterTrait, DataFormatTrait, GrantTrait;

    /**
     * The attributes that aren't mass assignable.
     * 避免默认无法插入数据
     *
     * @var array
     */
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
}
