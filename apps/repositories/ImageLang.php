<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinImageLang;

class ImageLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = BinImageLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return BinImageLang::findFirst(array(
            "image_id = :ID: AND image_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}



