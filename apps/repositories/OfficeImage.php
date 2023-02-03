<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScOfficeImage;

class OfficeImage extends Component
{
    public static function countByOfficeId($office_id)
    {
        $OfficeImage = ScOfficeImage::find(array(
            'image_office_id = :image_office_id:',
            'bind' => array('image_office_id' => $office_id)
        ));
        return isset($OfficeImage) ? $OfficeImage->count() : 0;
    }
    public function getAllByOfficeId($id, $limit = null){
        $result = array();
        $para = array('ID' => $id);
        $sql = "SELECT * FROM Score\Models\ScOfficeImage  
                WHERE image_active = 'Y' AND image_office_id = :ID: 
                ORDER BY image_order ASC ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,$para);
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}



