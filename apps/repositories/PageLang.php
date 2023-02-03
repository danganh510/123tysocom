<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinPageLang;

class PageLang extends Component
{
    public static  function deleteById($id){
        $arr_lang = BinPageLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return BinPageLang::findFirst(array(
            "page_id = :ID: AND page_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}