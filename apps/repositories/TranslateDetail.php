<?php

namespace Score\Repositories;

use Phalcon\Di;
use Phalcon\Mvc\User\Component;

class TranslateDetail extends Component
{
    public function getAllTranslateDetail (){
        $result = array();
        $sql = "SELECT * FROM Score\Models\ScTranslateDetail  
                    ORDER BY detail_insert_time DESC ";
        $lists = $this->modelsManager->executeQuery($sql);
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}
