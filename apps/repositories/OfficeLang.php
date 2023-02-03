<?php

namespace Score\Repositories;

use Score\Models\ScOfficeLang;
use Phalcon\Mvc\User\Component;

class OfficeLang extends Component
{
        public static  function deleteById($id){
            $arr_lang = ScOfficeLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static  function findFirstByIdAndLang($id,$lang_code){
            return ScOfficeLang::findFirst(array (
                "office_id = :ID: AND office_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



