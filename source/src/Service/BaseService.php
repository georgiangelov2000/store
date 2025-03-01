<?php

namespace App\Service;
class BaseService
{
    protected $relations = [];    
    protected $columns = [];

    protected function removeRelations(array $relations): void {
        foreach ($relations as $relation) {
            if (isset($this->relations[$relation])) {
                unset($this->relations[$relation]);
            } else{
                $key = array_search($relation, $this->relations, true);
                if ($key !== false) {
                    unset($this->relations[$key]);
                }
            }
        }
    }

}