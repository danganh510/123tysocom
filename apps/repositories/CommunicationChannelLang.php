<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;
use Score\Models\ScCommunicationChannelLang;

class CommunicationChannelLang extends Component
{
        public static function deleteById($id){
            $arr_lang = ScCommunicationChannelLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static function findFirstByIdAndLang($id,$lang_code){
            return ScCommunicationChannelLang::findFirst(array (
                "communication_channel_id = :ID: AND communication_channel_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }

}



