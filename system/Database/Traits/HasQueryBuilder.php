<?php

namespace System\Database\Traits;

use System\Database\DBConnection\DBConnection;

trait HasQueryBuilder
{
    private $sql = '';
    private $orderBy = [];
    private $limit = [];
    private $values = [];
    private $bindValues = [];
    protected $where = [];

    protected function setSql($query)
    {
        $this->sql = $query;
    }

    protected function getSql()
    {
        return $this->sql;
    }

    protected function resetSql()
    {
        $this->sql = '';
    }

    protected function setWhere($operator, $condition)
    {
        $array = [
            'operator' => $operator,
            'condition' => $condition
        ];
        array_push($this->where, $array);
    }

    protected function resetWhere()
    {
        $this->where = [];
    }

    protected function setOrderBy($name, $expression)
    {
        array_push($this->orderBy, $this->getAttributeName($name) . ' ' . $expression);
    }

    protected function resetOrderBy()
    {
        $this->orderBy = [];
    }

    protected function setLimit($from, $number)
    {
        $this->limit['from'] = (int)$from;
        $this->limit['number'] = (int)$number;
    }

    protected function resetLimit()
    {
        unset($this->limit['from']);
        unset($this->limit['number']);
    }

    protected function addValue($attribute, $value)
    {
        // product_id = 2
        //$values = ['id' => 3]
        //$bindValues = [2]
        $this->values[$attribute] = $value;
        array_push($this->bindValues, $value);
    }

    protected function removeValues()
    {
        $this->values = [];
        $this->bindValues = [];
    }

    protected function resetQuery()
    {
        $this->resetSql();
        $this->resetWhere();
        $this->resetOrderBy();
        $this->resetLimit();
        $this->removeValues();
    }

    protected function executeQuery()
    {
        $query = '';
        $query .= $this->sql;

        //select * from users where id > 5 and id != 17 or id =  2
        //part 1 : id > 5
        //part 2 : and id != 17
        //part 3 : or id =2
        //where . part1 . part2 . part3

        if (!empty($this->where)) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString == '' ?
                    $whereString .= $where['condition'] :
                    $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $query .= 'WHERE ' . $whereString;
        }

        if (!empty($this->orderBy)) {
            //order by column1 desc,column2 asc
            $query .= 'ORDER BY' . implode(', ', $this->orderBy);
        }

        if (!empty($this->limit)) {
            $query .= 'limit ' . $this->limit['from'] . ', ' . $this->limit['number'] . ' ';
        }

        $query .= ' ;';
        echo $query . '<hr>';

        $statement = $this->preparePdo($query);

        // select * from users ; ==> values and bind values = []
        // ------------------------------------------------------
        // select * from users where id >=  ?  id != ?
        // values : ['id' => 2 , 'id' => 3]
        // bind values : [2,3]

        if (sizeof($this->bindValues) > sizeof($this->values)) {
            sizeof($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();
        } else {
            sizeof($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
        }

        return $statement;
    }

    protected function getCount()
    {
        $query = '';
        $query .= "SELECT COUNT(" . $this->getTableName() . ".*) FROM" . $this->getTableName();

        if (!empty($this->where)) {
            $whereString = '';
            foreach ($this->where as $where) {
                $whereString == '' ?
                    $whereString .= $where['condition'] :
                    $whereString .= ' ' . $where['operator'] . ' ' . $where['condition'];
            }
            $query .= 'WHERE ' . $whereString;
        }

        $query .= ' ;';
        $statement = $this->preparePdo($query);

        if (sizeof($this->bindValues) > sizeof($this->values)) {
            sizeof($this->bindValues) > 0 ? $statement->execute($this->bindValues) : $statement->execute();
        } else {
            sizeof($this->values) > 0 ? $statement->execute(array_values($this->values)) : $statement->execute();
        }

        return $statement->fetchColumn();
    }

    private function preparePdo($query)
    {
        $pdoInstance = DBConnection::getDbConnectionInstance();
        return $pdoInstance->prepare($query);
    }

    protected function getTableName()
    {
        return ' `' . $this->table . '`';
    }

    protected function getAttributeName($attribute)
    {
        //method orderBy
        return ' `' . $this->table . '`.' . '`' . $attribute . '` ';
    }


}