<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinCareerLang;

class CareerLang extends Component
{
        public static function deleteById($id){
            $arr_lang = BinCareerLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static function findFirstByIdAndLang($id,$lang_code){
            return BinCareerLang::findFirst(array (
                "career_id = :ID: AND career_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



