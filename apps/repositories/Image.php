<?php

namespace Bincg\Repositories;

use Bincg\Models\BinImage;
use Phalcon\Mvc\User\Component;

class Image extends Component
{

    public static function getByID($image_id)
    {
        return BinImage::findFirst(array(
            'image_id = :image_id:',
            'bind' => array('image_id' => $image_id)
        ));
    }

    public static function getByAlbumAndID($album_id, $image_id)
    {
        return BinImage::findFirst(array(
            'image_album_id = :album_id: AND image_id = :image_id:',
            'bind' => array('album_id' => $album_id, 'image_id' => $image_id)
        ));
    }

    public static function getTotalByAlbum($album_id)
    {
        $list_images = BinImage::find(array(
            'image_album_id = :album_id:',
            'bind' => array('album_id' => $album_id)
        ));
        return count($list_images);
    }

    public static function getFirstByAlbum($album_id)
    {
        return BinImage::findFirst(array(
            'image_album_id = :album_id:',
            'bind' => array('album_id' => $album_id)
        ));
    }
    public function getAllActiveImageTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinImage as i
                WHERE  i.image_id NOT IN 
                 (SELECT il.image_id FROM Bincg\Models\BinImageLang as il WHERE il.image_lang_code =
                :lang_code:)";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}