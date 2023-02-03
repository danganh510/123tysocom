<?php

namespace Bincg\Repositories;

use Bincg\Models\BinOfficeLang;
use Phalcon\Mvc\User\Component;

class OfficeLang extends Component
{
        public static  function deleteById($id){
            $arr_lang = BinOfficeLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static  function findFirstByIdAndLang($id,$lang_code){
            return BinOfficeLang::findFirst(array (
                "office_id = :ID: AND office_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



