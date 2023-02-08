<?php

namespace System\Database\ORM;

use System\Database\Traits\HasCrud;
use System\Database\Traits\HasAttribute;
use System\Database\Traits\HasMethodCaller;
use System\Database\Traits\HasQueryBuilder;
use System\Database\Traits\HasRelation;
use System\Database\Traits\HasSoftDelete;

abstract class Model
{
    use HasCrud, HasAttribute, HasMethodCaller, HasQueryBuilder, HasRelation, HasSoftDelete;

    protected $table;
    protected $fillable = [];
    protected $hidden = [];
    protected $casts = [];
    protected $primarykey = 'id';
    protected $created_at = 'created_at';
    protected $updated_at = 'updated_at';
    protected $deleted_at = null;
    protected $collection = [];
}