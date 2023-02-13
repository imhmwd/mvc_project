<?php

namespace System\Database\Traits;

trait HasAttributes
{
    //test

    // collection[
    //      array[
    //              0 => [
    //                  'name' => 'mohammad'
    //                      ] ,
    //              1 => [
    //                  'name' => 'mohammad'
    //                      ]
    //              ]
    // ]

    // Attribute & value
    //1) dorost kardan sakhtar collection
    //2) dorost kardan hidden
    //3) dorost kardan cast

    //baraye dorost kardan sakhtare collection
    private function registerAttribute($object, string $attribute, $value)
    {
        // $user->name = 20 ;
        $this->inCastAttribute($attribute) == true
            ? $object->$attribute = $this->castDecodeValue($attribute, $value) :
            $object->$attribute = $value;
    }

    //baraye dorost kardan sakhtare collection
    protected function arrayToAttribute(array $array, $object = null)
    {
        if (!$object) {
            $className = get_called_class();
            $object = new $className;
        }
        foreach ($array as $attribute => $value) {
            if ($this->inHiddenAttributes($attribute)) {
                continue;
            }
            $this->registerAttribute($object, $attribute, $value);
        }
        return $object;
    }

    //baraye dorost kardan sakhtare collection
    protected function arrayToObjects(array $array)
    {
        $collection = [];

        foreach ($array as $value) {
            $object = $this->arrayToAttribute($value);
            array_push($collection, $object);
        }

        $this->collection = $collection;

    }

    // baraye hidden kardan
    private function inHiddenAttributes($attribute)
    {
        return in_array($attribute, $this->hidden);
    }

    // arrtibute haro cast mikone
    private function inCastAttribute($attribute)
    {
        return in_array($attribute, array_keys($this->casts));
    }

    //etelato az db mikhunim
    // unserialize
    private function castDecodeValue($attributeKey, $value)
    {
        if ($this->casts[$attributeKey] == 'array' || $this->casts[$attributeKey] == 'object') {
            return unserialize($value);
        }
        return $value;
    }

    // serialize
    private function castEnecodeValue($attributeKey, $value)
    {
        if ($this->casts[$attributeKey] == 'array' || $this->casts[$attributeKey] == 'object') {
            return serialize($value);
        }
        return $value;

    }

    // array az recorday ke encode shudan
    private function arrayToCastEncodeValue($values)
    {
        $newArray = [];

        foreach ($values as $attribute => $value) {
            $this->inCastAttribute($attribute) == true ?
                $newArray[$attribute] = $this->castEnecodeValue($attribute, $values) :
                $newArray[$attribute] = $value;
        }
        return $newArray;
    }


}