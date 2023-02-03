<?php

namespace Bincg\Repositories;

use Bincg\Models\BinNewspaperArticleLang;
use Phalcon\Mvc\User\Component;
class NewspaperArticleLang extends Component
{
    public static function findById($id){
        return BinNewspaperArticleLang::find(array (
            "article_id = :ID:",
            'bind' => array('ID' => $id))
        );
    }
    public static  function findFirstByIdLang($id,$lang_code){
        return BinNewspaperArticleLang::findFirst(array (
            "article_id = :ID: AND article_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
    public static  function deleteById($id){
        $arr_lang = BinNewspaperArticleLang::find($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
}