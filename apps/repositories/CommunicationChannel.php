<?php

namespace Bincg\Repositories;

use Phalcon\Di;
use Phalcon\Mvc\User\Component;
use Bincg\Models\BinCommunicationChannel;

class CommunicationChannel extends Component
{
    public static function getById($communication_channel_id){
        return BinCommunicationChannel::findFirst(array(
            'communication_channel_id = :communication_channel_id:',
            'bind' => array('communication_channel_id' => $communication_channel_id),
        ));
    }

    public function getType ($inputslc)
    {
        $data = BinCommunicationChannel::allTypes();
        return is_array($data) ?$this->my->getComboBox($data,$inputslc):"";
    }

    public static function checkName ($name, $type, $id)
    {
        return BinCommunicationChannel::findFirst(
            [
                'communication_channel_name = :name: AND communication_channel_type = :type: AND communication_channel_id != :id:',
                'bind' => array('name' => $name, 'type' => $type, 'id' => $id),
            ]
        );
    }

    public static function getAllCommunicationChannel($lang = 'en')
    {
        $globalVariable = Di::getDefault()->get('globalVariable');
        $modelsManager = Di::getDefault()->get('modelsManager');
        $result = array();
        $para = array();
        $listCommunicationChannel = array();
        if ($lang && $lang != $globalVariable->defaultLanguage) {
            $sql = "SELECT cc.*, ccl.* FROM Bincg\Models\BinCommunicationChannel cc
                INNER JOIN Bincg\Models\BinCommunicationChannelLang ccl
                ON cc.communication_channel_id = ccl.communication_channel_id AND ccl.communication_channel_lang_code = :LANG: 
                WHERE cc.communication_channel_active = 'Y' 
                ORDER BY cc.communication_channel_order ASC 
                ";
            $para['LANG'] = $lang;
            $lists = $modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0) {
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinCommunicationChannel(),array_merge($item->cc->toArray(), $item->ccl->toArray()));
                }
            }
        } else {
            $sql = "SELECT * FROM Bincg\Models\BinCommunicationChannel
                WHERE communication_channel_active = 'Y' 
                ORDER BY communication_channel_order ASC";
            $lists = $modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        foreach ($result as $data) {
            $ar = array(
                "communication_channel_id" => $data->getCommunicationChannelId(),
                "communication_channel_name" => $data->getCommunicationChannelName(),
                "communication_channel_icon" => $data->getCommunicationChannelIcon(),
                "communication_channel_type" => $data->getCommunicationChannelType(),
                "communication_channel_order" => $data->getCommunicationChannelOrder(),
                "communication_channel_active" => $data->getCommunicationChannelActive(),
            );
            $listCommunicationChannel[] = $ar;
        }
        return $listCommunicationChannel;
    }

    public static function getCommunicationChannel($inputslc,$lang_code)
    {
        $repoCommunication = new CommunicationChannel();
        $data = $repoCommunication->getAllCommunicationChannel($lang_code);
        $output = '';
        foreach ($data as  $value)
        {
            $selected = '';
            if($value['communication_channel_id'] == $inputslc)
            {
                $selected = 'selected';
            }
            $output.= "<option data-communication-channel-type='".$value['communication_channel_type']."' 
            data-img-src='".$value['communication_channel_icon']."' ".$selected." value='".$value['communication_channel_id']."'>".$value['communication_channel_name']."</option>";
        }
        return $output;
    }
    public static function getCommunicationNameByID ($id) {
        $result = BinCommunicationChannel::findFirstById($id);
        return $result ? $result->getCommunicationChannelName() : '';
    }

    public function getAllActiveCommunicationChannelTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinCommunicationChannel as b
                WHERE b.communication_channel_active = 'Y' AND b.communication_channel_id NOT IN 
                 (SELECT bl.communication_channel_id FROM Bincg\Models\BinCommunicationChannelLang as bl WHERE bl.communication_channel_lang_code =
                :lang_code:)
                ORDER BY b.communication_channel_order ASC ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}



