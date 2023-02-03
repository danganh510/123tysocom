<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScPageLang;

class PageLang extends Component
{
    public static  function deleteById($id){
        $arr_lang = ScPageLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return ScPageLang::findFirst(array(
            "page_id = :ID: AND page_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}