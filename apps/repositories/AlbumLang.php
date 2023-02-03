<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScAlbumLang;

class AlbumLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = ScAlbumLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return ScAlbumLang::findFirst(array(
            "album_id = :ID: AND album_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}



