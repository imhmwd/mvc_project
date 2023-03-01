<?php

namespace System\Request;

use System\Request\Traits\HasFileValidationRules;
use System\Request\Traits\HasRunValidation;
use System\Request\Traits\HasValidationRules;

class Request
{
    use HasFileValidationRules, HasRunValidation, HasValidationRules;

    protected $errorExist = false;
    protected $request;
    protected $files = null;
    protected $errorVariablesName = [];

    public function __construct()
    {  
        if (isset($_POST)) {
            $this->postAttributes();
        }

        if (!empty($_FILES)) {
            $this->files = $_FILES;
        }
        $rules = $this->rules();

        if (!empty($rules)){
            $this->run($rules);
        }

        $this->errorRedirect();
    }

    protected function run($roles)
    {
        foreach ($roles as $attribute => $values) {
            $ruleArray = explode('|', $values);

            if (in_array('file', $ruleArray)) {
                unset($ruleArray[array_search('file', $ruleArray)]);
                $this->fileValidation($attribute, $ruleArray);

            } elseif (in_array('number', $ruleArray)) {
                $this->numberValidation($attribute, $ruleArray);
            } else {
                $this->normalValidation($attribute, $ruleArray);

            }

        }
    }

    protected function rules()
    {
        return [];
    }

    public function file($name)
    {
        return $this->file[$name] ?? false;
    }

    protected function postAttributes()
    {
        foreach ($_POST as $key => $value) {
            $this->$key = htmlentities($value);
            $this->request[$key] = htmlentities($value);
        }
    }

    public function all()
    {
        return $this->request;
    }

}