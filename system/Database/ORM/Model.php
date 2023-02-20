<?php

namespace System\Database\ORM;

use System\Database\Traits\HasCRUD;
use System\Database\Traits\HasAttributes;
use System\Database\Traits\HasMethodCaller;
use System\Database\Traits\HasQueryBuilder;
use System\Database\Traits\HasRelation;
use System\Database\Traits\HasSoftDelete;

abstract class Model
{
    use HasCRUD, HasAttributes, HasMethodCaller, HasQueryBuilder, HasRelation ;

    protected $table;
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];
    protected $primaryKey = 'id';
    protected $created_at = 'created_at';
    protected $updated_at = 'updated_at';
    protected $deleted_at = null;
    protected $collection = [];
}