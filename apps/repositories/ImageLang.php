<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScImageLang;

class ImageLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = ScImageLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return ScImageLang::findFirst(array(
            "image_id = :ID: AND image_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}



