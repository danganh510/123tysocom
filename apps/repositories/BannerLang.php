<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScBannerLang;

class BannerLang extends Component
{
        public static  function deleteById($id){
            $arr_lang = ScBannerLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static  function findFirstByIdAndLang($id,$lang_code){
            return ScBannerLang::findFirst(array (
                "banner_id = :ID: AND banner_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



