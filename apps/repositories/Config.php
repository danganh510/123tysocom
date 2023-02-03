<?php

namespace Bincg\Repositories;

use Bincg\Models\BinConfig;
use Phalcon\Mvc\User\Component;
use Bincg\Models\BinLanguage;

class Config extends Component
{
    const FOLDER ="/../messages/";
    const FILE_CACHED_CONFIG = "cached_config.txt";
    public static function findByID($key){
        return BinConfig::findFirst(array(
                "config_key =:key:",
                'bind' => array('key' => $key))
        );
    }
    public static function findByLanguage($key,$language){
        return BinConfig::findFirst(array("config_key =:key:  AND config_language=:language:",
            'bind' => array('key' => $key,'language'=>$language)));
    }

    /**
     * @param $key
     * @return BinConfig|BinConfig[]
     */
    public static function getByID($key){
        return BinConfig::find(array(
                "config_key =:key:",
                'bind' => array('key' => $key))
        );
    }
    public static function deletedByKey($key){
        $list_config = BinConfig::find(array("config_key =:key:",
            'bind' =>  array('key' => $key)));
        foreach ($list_config as $config){
            $config->delete();
        }
        return true;
    }
    public static function getCache($language, $define = true, $lang_url = "") {
        $type_language =$language."/";
        $folder =__DIR__.self::FOLDER.$type_language;
        $cachedConfigFileName = $folder.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            $messageArray = unserialize(file_get_contents($cachedConfigFileName));
        }
        else {
            if (!is_dir($folder))  {
                mkdir($folder, 0777,TRUE);
            }
            $messages = self::getAllByLanguage($language);
            $messageArray = [];
            foreach($messages as $message) {
                $messageArray[$message->getConfigKey()] = $message->getConfigContent();
            }
            file_put_contents($cachedConfigFileName, serialize($messageArray));
        }
        if ($define == false) {
            return $messageArray;
        }
        else {
            foreach($messageArray as $key => $value) {
                if (!defined($key)) {
                    define($key, str_replace(array('|||SCRIPTBEFORE|||', '|||SCRIPTAFTER|||', '|||NOSCRIPTBEFORE|||', '|||NOSCRIPTAFTER|||'),array('<script>', '</script>', '<noscript>', '</noscript>'),$value));
                }
            }
        }
    }
    public static function getAllByLanguage($language){
        return BinConfig::find(array("config_language = :language:",
                'bind' => array('language'=>$language))
        );
    }
    public static function deleteCache(){
        $list_lang = BinLanguage::find();
        foreach ($list_lang as $lang){
            self::deleteCacheLanguage($lang->getLanguageCode());
        }
    }
    public static function deleteByLanguage($language){
        $list_config = BinConfig::find(array("config_language=:language:",
            'bind' => array('language'=>$language)));
        foreach ($list_config as $item){
            $item->delete();
        }
    }
    public static function deleteCacheLanguage($language){
        $type_language =$language."/";
        $cachedConfigFileName = __DIR__.self::FOLDER.$type_language.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            unlink($cachedConfigFileName);
        }
    }
    public static function checkKeyword($key_new)
    {
        return BinConfig::findFirst(array (
                'config_key = :keyID: ',
                'bind' => array('keyID' => $key_new),
            ));
    }

    public function getAllConfigByCodeTranslate ($limit = null,$lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinConfig as c
                WHERE c.config_language = :lang_default: AND c.config_content !='' AND  c.config_key NOT IN 
                (SELECT cl.config_key FROM Bincg\Models\BinConfig as cl WHERE cl.config_language = :lang_code: AND  cl.config_content !='' )";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array(
                                                        'lang_default'=>$this->globalVariable->defaultLanguage,
                                                        'lang_code'=>$lang_code,   ));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}




