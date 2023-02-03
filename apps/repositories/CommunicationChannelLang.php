<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinCommunicationChannelLang;

class CommunicationChannelLang extends Component
{
        public static function deleteById($id){
            $arr_lang = BinCommunicationChannelLang::findById($id);
            foreach ($arr_lang as $lang){
                $lang->delete();
            }
        }
        public static function findFirstByIdAndLang($id,$lang_code){
            return BinCommunicationChannelLang::findFirst(array (
                "communication_channel_id = :ID: AND communication_channel_lang_code = :CODE:",
                'bind' => array('ID' => $id,
                                'CODE' => $lang_code )));
        }

}



