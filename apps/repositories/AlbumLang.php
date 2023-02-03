<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinAlbumLang;

class AlbumLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = BinAlbumLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return BinAlbumLang::findFirst(array(
            "album_id = :ID: AND album_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}



