<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScCareerLang;

class CareerLang extends Component
{
        public static function deleteById($id){
            $arr_lang = ScCareerLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static function findFirstByIdAndLang($id,$lang_code){
            return ScCareerLang::findFirst(array (
                "career_id = :ID: AND career_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



