<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;

trait HasCRUD
{
    protected function createMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values , $this);
        return $this->saveMethod();
    }

    protected function updateMethod($values){
        $values = $this->arrayToCastEncodeValue($values);
        $this->arrayToAttributes($values , $this);
        return $this->saveMethod();
    }

    protected function deleteMethod($id = null)
    {
        //sometimes id with object
        $object = $this;
        $this->resetQuery();
        if ($id) {
            $object = $this->findMethod($id);
            $this->resetQuery();
        }

        $object->setSql("DELETE FROM" . $object->getTableName());
        $object->setWhere("AND", $this->getAttributeName($this->primaryKey) . " = ?");
        $object->addValue($object->primayKey, $object->{$object->primaryKey});

        return $object->executeQuery();
    }

    protected function findMethod($id)
    {
        $this->setSql("SELECT * FROM" . $this->getTableName());
        $this->setWhere("AND", $this->getAttributeName($this->primaryKey) . " = ?");
        $this->addValue($this->primayKey, $id);
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        $this->setAllowedMethods(['update', 'delete', 'save']);

        if ($data) {
            return $this->arrayToAttributes($data);
        }

        return null;
    }

    //for insert and update
    protected function saveMethod()
    {
        $fillString = $this->fill();

        //INSERT
        if (!isset($this->{$this->primaryKey})) {
            $this->setSql(
                "INSERT INTO" .
                $this->getTableName() .
                " SET $fillString , " . $this->getAttributeName($this->created_at) . "=Now()");
        } else {
            $this->setSql(
                "UPDATE " .
                $this->getTableName() .
                " SET $fillString , " . $this->getAttributeName($this->updated_at) . "=Now()");

            $this->setWhere("AND", $this->getAttributeName($this->primaryKey) . " = ?");
            $this->addValue($this->primaryKey, $this->{$this->primaryKey});
        }

        $this->executeQuery();
        $this->resetQuery();

        //$user->name = "mohammad'
        //$user->lsat_name = "jahangiri'
        //default status true when user created
        // for get column with default value
        if (!isset($this->{$this->primaryKey})) {

            $object = $this->findMethod(DBConnection::newInsertId());
            $defaultVars = get_class_vars(get_called_class());
            $allVars = get_object_vars($object);
            $diffrentVars = array_diff(array_keys($allVars), array_keys($defaultVars));
            foreach ($defaultVars as $attribute) {
                $this->inCastsAttribute($attribute) == true ?
                    $this->registerAttribute($this, $attribute, $this->castEncodeValue($attribute, $object->$attribute))
                    : $this->registerAttribute($this, $attribute, $object->$attribute);
            }

        }
        $this->resetQuery();
        $this->setAllowedMethods(['update', 'delete', 'find']);
        return $this;

    }

    protected function allMethod()
    {
        $this->setSql("SELECT * FROM" . $this->getTableName());
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    //->where('age',20)
    //->where('age', '>' ,20)
    protected function whereMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {
            // `users`.`age` = ?
            $condition = $this->getAttributeName($attribute) . " = ?";
            $this->addValue($attribute, $firstValue);
        } else {
            // `users` . `age` > ?
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ? ';
            $this->addValue($attribute, $secondValue);
        }

        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get', 'paginate']);

        return $this;
    }

    protected function whereOrMethod($attribute, $firstValue, $secondValue = null)
    {
        if ($secondValue === null) {
            // `users`.`age` = ?
            $condition = $this->getAttributeName($attribute) . " = ?";
            $this->addValue($attribute, $firstValue);
        } else {
            // `users` . `age` > ?
            $condition = $this->getAttributeName($attribute) . ' ' . $firstValue . ' ? ';
            $this->addValue($attribute, $secondValue);
        }

        $operator = 'OR';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get', 'paginate']);

        return $this;
    }

    protected function whereNullMethod($attribute)
    {
        $condition = $this->getAttributeName($attribute) . ' IS NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get', 'paginate']);

        return $this;
    }

    protected function whereNotNullMethod($attribute)
    {
        $condition = $this->getAttributeName($attribute) . ' IS NOT NULL';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get', 'paginate']);

        return $this;
    }

    protected function whereInMethod($attribute, array $values)
    {
        $valuesArray = [];
        foreach ($values as $value) {
            $this->addValue($attribute, $value);
            array_push($valuesArray, '?');
        }
        $condition = $this->getAttributeName($attribute) . ' IN (' . implode(' , ', $valuesArray) . ')';
        $operator = 'AND';
        $this->setWhere($operator, $condition);
        $this->setAllowedMethods(['where', 'whereOr', 'whereIn', 'whereNull', 'whereNotNull',
            'limit', 'orderBy', 'get', 'paginate']);

        return $this;
    }

    //->orderBy('name','desc')
    protected function orderByMethod($attribute , $expression){
        $this->setOrderBy($attribute,$expression);
        $this->setAllowedMethods(['limit' , 'orderBy' , 'get' , 'paginate']);
        return $this;
    }

    protected function limitMethod($from , $number){
        $this->setLimit($from,$number);
        $this->setAllowedMethods(['limit' , 'orderBy' , 'get' , 'paginate']);
        return $this;
    }

    // record ==> fetch(find) || fetch all
    // get doing fetch all
    // $array = columns ('name')
    protected function getMethod($array = []){
        if ($this->sql == ''){

            if (empty($array)){
                $fields = $this->getTableName().'.*';
            }else{
                foreach ($array as $key => $field){
                    $array[$key] = $this->getAttributeName($field);
                }
                $fields = implode(',',$array);
            }
            $this->setSql("SELECT $fields FROM".$this->getTableName());
        }

        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function paginateMethod($perPage){
        $totalRows =  $this->getCount();
        $currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1  ;
        $totalPages= ceil($totalRows / $perPage);
        //3 - 10 ==> 3
        $currentPage = min($currentPage,$totalPages);
        $currentPage = max($currentPage,1 );
        //3o posts
        //5 perPage
        //3 current_page
        $currentRow = ($currentPage - 1) * $perPage ;
        $this->setLimit($currentRow,$perPage);
        if ($this->sql == ''){
            $this->setSql("SELECT ".$this->getTableName().".* FROM ".$this->getTableName() );
        }
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data){
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    // fillable in model
    // 1) be dast avardan fillable
    // 2) check kardan cast - age bod encode value
    // 3) esme attribute be dast biarim
    protected function fill()
    {
        $fillArray = [];
        // `user`.`email` = ?
        foreach ($this->fillable as $attribute) {
            // $user->email
            if (isset($this->attribute)) {
                array_push($fillArray, $this->getAttributeName($attribute) . " = ?");
                $this->inCastsAttribute($attribute) == true ?
                    $this->addValue($attribute, $this->castEncodeValue($attribute, $this->attribute)) :
                    $this->addValue($attribute, $this->attribute);
            }
        }

        $fillString = implode(', ', $fillArray);
        return $fillString;
    }

}