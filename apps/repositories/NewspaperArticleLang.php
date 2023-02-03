<?php

namespace Score\Repositories;

use Score\Models\ScNewspaperArticleLang;
use Phalcon\Mvc\User\Component;
class NewspaperArticleLang extends Component
{
    public static function findById($id){
        return ScNewspaperArticleLang::find(array (
            "article_id = :ID:",
            'bind' => array('ID' => $id))
        );
    }
    public static  function findFirstByIdLang($id,$lang_code){
        return ScNewspaperArticleLang::findFirst(array (
            "article_id = :ID: AND article_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
    public static  function deleteById($id){
        $arr_lang = ScNewspaperArticleLang::find($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
}