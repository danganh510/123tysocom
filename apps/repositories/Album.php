<?php

namespace Score\Repositories;

use Score\Models\ScAlbum;
use Phalcon\Mvc\User\Component;

class Album extends Component
{
    public function checkKeyword($keyword, $album_id)
    {
        return ScAlbum::findFirst(
            [
                'album_keyword = :albumkeyword: AND  album_id != :albumid:',
                'bind' => array('albumkeyword' => $keyword, 'albumid' => $album_id),
            ]
        );
    }

    public static function getByID($album_id)
    {
        return ScAlbum::findFirst(array(
            'album_id = :album_id:',
            'bind' => array('album_id' => $album_id)
        ));
    }

    public static function getNameByID($id)
    {
        $album = self::getByID($id);
        return ($album) ? $album->getAlbumName() : "";
    }
    public function getAllActiveAlbumTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Score\Models\ScAlbum as b
                WHERE b.album_active = 'Y' AND b.album_id NOT IN 
                 (SELECT bl.album_id FROM Score\Models\ScAlbumLang as bl WHERE bl.album_lang_code =
                :lang_code:)";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }

}