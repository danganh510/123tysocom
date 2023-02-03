<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinBannerLang;

class BannerLang extends Component
{
        public static  function deleteById($id){
            $arr_lang = BinBannerLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static  function findFirstByIdAndLang($id,$lang_code){
            return BinBannerLang::findFirst(array (
                "banner_id = :ID: AND banner_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }
}



