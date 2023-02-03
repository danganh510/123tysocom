<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScTemplateEmailLang;

class EmailTemplateLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = ScTemplateEmailLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return ScTemplateEmailLang::findFirst(array(
            "email_id = :ID: AND email_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}