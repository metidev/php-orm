<?php

namespace App\Models;

use App\Helpers;

class User
{
    protected $first_name;
    protected $last_name;

    public function __call($method, $params)
    {
        $property = Helpers::camelToUnderscore(substr($method, 3));
        if (property_exists($this, $property)) {
            if (str_starts_with($method, 'set')) {
                $this->$property = trim($params[0]);
            }
            if (str_starts_with($method, 'get')) {
                return $this->$property;
            }
        }
    }

    public function getFullName(): string
    {
        return "{$this->getFirstName()} {$this->getLastName()}";
    }
}