<?php

namespace Bincg\Repositories;

use Bincg\Models\BinTypeLang;
use Phalcon\Mvc\User\Component;
class TypeLang extends Component
{
    public static  function findFirstByIdLang($id,$lang_code){
        return BinTypeLang::findFirst(array (
            "type_id = :ID: AND type_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
    public static  function deleteById($id){
        $arr_lang = BinTypeLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
}