<?php

namespace System\Database\Traits;

trait HasCRUD
{
    protected function fill()
    {
        $fillArray = [];
        // `user`.`email` = ?

        foreach ($this->fillable as $attribute) {
            if (isset($this->attribute)) {
                array_push($fillArray, $this->getAttributeName($attribute) . " = ?");
                $this->inCastsAttribute($attribute) == true ?
                    $this->addValue($attribute, $this->castEncodeValue($attribute, $this->attribute)) :
                    $this->addValue($attribute, $this->attribute);
            }
        }

        $fillString = implode(', ',$fillArray);
        return $fillString;
    }

}