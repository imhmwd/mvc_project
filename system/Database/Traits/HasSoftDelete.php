<?php

namespace System\Database\Traits;

trait HasSoftDelete
{
    protected function deleteMethod($id = null)
    {
        $object = $this;
        if ($id) {
            $this->resetQuery();
            $object = $this->findMethod($id);
        }
        if ($object) {
            $object->resetQuery();
            $object->setSql("UPDATE FROM" . $object->getTableName()
                ." SET ".$this->getAttributeName($this->deleted_at). " =NOW()  ");
            $object->setWhere("AND", $this->getAttributeName($object ->primaryKey) . " = ?");
            $object->addValue($object->primayKey, $object->{$object->primaryKey});

            return $object->executeQuery();
        }

    }

    protected function allMethod()
    {
        $this->resetQuery();
        $this->setSql("SELECT " . $this->getTableName().".* FROM ".$this->getTableName());
        $this->setWhere("AND", $this->getAttributeName($this ->deleted_at) . " IS NULL ");
        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function findMethod($id)
    {
        $this->setSql("SELECT " . $this->getTableName().".* FROM ".$this->getTableName());
          $this->addValue($this->primayKey, $id);
        $this->setWhere("AND", $this->getAttributeName($this ->deleted_at) . " IS NULL ");
        $statement = $this->executeQuery();
        $data = $statement->fetch();
        $this->setAllowedMethods(['update', 'delete', 'save']);

        if ($data) {
            return $this->arrayToAttributes($data);
        }

        return null;
    }

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

        $this->setWhere("AND", $this->getAttributeName($this ->deleted_at) . " IS NULL ");

        $statement = $this->executeQuery();
        $data = $statement->fetchAll();
        if ($data) {
            $this->arrayToObjects($data);
            return $this->collection;
        }
        return [];
    }

    protected function paginateMethod($perPage){
        $this->setWhere("AND", $this->getAttributeName($this ->deleted_at) . " IS NULL ");

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

}