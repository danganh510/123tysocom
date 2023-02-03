<?php

namespace Bincg\Repositories;

use Bincg\Models\BinLanguage;
use Phalcon\Mvc\User\Component;

class Language extends Component {
    public static function checkCode($language_code, $language_id)
    {
        return BinLanguage::findFirst(
            array (
                'language_code = :CODE: AND language_id != :languageid:',
                'bind' => array('CODE' => $language_code, 'languageid' => $language_id),
            ));
    }
   public static function findAllLanguageCodes() {
        $ar_lang = array();
        $list_language = BinLanguage::find('language_active ="Y"');
        if (sizeof($list_language)>0){
            foreach ($list_language as $item){
                $ar_lang[] = $item->getLanguageCode();
            }
        }
        return $ar_lang;
    }
    public static function getLanguageByCode($language_code){
        return BinLanguage::findFirst(
            array (
                'language_code = :CODE: AND language_active="Y"',
                'bind' => array('CODE' => $language_code),
            ));
    }
    public static function arrLanguages () {
        $arr_language = array();
        $languages = self::getLanguages();
        foreach ($languages as $lang){
            $arr_language[$lang->getLanguageCode()] = $lang->getLanguageName();
        }
        return $arr_language;
    }
    public static function getLanguages(){
        return BinLanguage::find(array("language_active = 'Y'",
            "order" => "language_order"));
    }
    public static function getCombo($lang_code){
        $list_language = self::getLanguages();
        $string = "";
        foreach ($list_language as $language){
            $selected = '';
            if($language->getLanguageCode() == $lang_code)
            {
                $selected = 'selected';
            }
            $string .= "<option ".$selected." value='".$language->getLanguageCode()."'>".$language->getLanguageName()."</option>";
        }
        return $string;
    }
    public function getActiveLanguagesByLocation($countryCode) {
        $sql = 'SELECT la.* FROM \Bincg\Models\BinLanguage la 
                INNER JOIN \Bincg\Models\BinLocation lo ON lo.location_lang_code = la.language_code
                WHERE lo.location_active="Y" AND la.language_active="Y" AND lo.location_country_code = "'.$countryCode.'"
                ORDER BY lo.location_order ASC';

        return $this->modelsManager->executeQuery($sql);
    }

    public static function getNameByCode($language_code)
    {
        $occ_language = BinLanguage::findFirst(array('language_code = :CODE: AND language_active="Y"', 'bind' => array('CODE' => $language_code),));
        return $occ_language ? $occ_language->getLanguageName() : '';
    }
}
