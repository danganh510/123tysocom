<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinTemplateEmailLang;

class EmailTemplateLang extends Component
{
    public static function deleteById($id)
    {
        $arr_lang = BinTemplateEmailLang::findById($id);
        foreach ($arr_lang as $lang) {
            $lang->delete();
        }
    }

    public static function findFirstByIdAndLang($id, $lang_code)
    {
        return BinTemplateEmailLang::findFirst(array(
            "email_id = :ID: AND email_lang_code = :CODE:",
            'bind' => array('ID' => $id,
                'CODE' => $lang_code)));
    }
}