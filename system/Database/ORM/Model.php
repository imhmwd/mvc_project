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
}