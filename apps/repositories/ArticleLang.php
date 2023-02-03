<?php

namespace Score\Repositories;

use Score\Models\ScArticleLang;
use Phalcon\Mvc\User\Component;
class ArticleLang extends Component
{
    public static function deleteById($id){
        $arr_lang = ScArticleLang::findById($id);
        foreach ($arr_lang as $lang){
            $lang->delete();
        }
    }
    public static function findFirstByIdAndLang($id,$lang_code){
        return ScArticleLang::findFirst(array (
            " article_id = :ID: AND article_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code )));
    }
}