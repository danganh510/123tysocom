<?php

namespace Score\Repositories;

use Score\Models\ScTypeLang;
use Phalcon\Mvc\User\Component;
class TypeLang extends Component
{
    public static  function findFirstByIdLang($id,$lang_code){
        return ScTypeLang::findFirst(array (
            "type_id = :ID: AND type_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
    public static  function deleteById($id){
        $arr_lang = ScTypeLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
}