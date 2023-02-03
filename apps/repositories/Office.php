<?php

namespace Bincg\Repositories;

use Bincg\Models\BinOffice;
use Phalcon\Mvc\User\Component;

class Office extends Component {
    public function getAllOffice($lang='en', $limit = null){
        $result = array();
        $para = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT o.*, ol.* FROM \Bincg\Models\BinOffice o 
                INNER JOIN \Bincg\Models\BinOfficeLang ol 
                ON ol.office_id = o.office_id AND ol.office_lang_code = :LANG: 
                WHERE o.office_active = 'Y'
                ORDER BY o.office_order ASC
                ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinOffice(),array_merge($item->o->toArray(), $item->ol->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinOffice 
                WHERE office_active = 'Y' 
                ORDER BY office_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }

    public static function getByID($id)
    {
        return BinOffice::findFirst(array(
            'office_id = :office_id:',
            'bind' => array('office_id' => $id)
        ));
    }

    public static function getCoordinates () {
        $offices = self::getAll();
        $result = array();
        foreach ($offices as $office) {
            $temp= array(
                'id' => $office->getOfficeId(),
                'lat' => $office->getOfficePositionX(),
                'lng' => $office->getOfficePositionY(),
            );
            $result[] = $temp;
        }
        return $result;
    }
    public static function getAll () {
        return BinOffice::find(array(
            'office_active' => 'Y',
        ));
    }

    public static function getNameById ($id){
        $office = BinOffice::findFirstById($id);
        return isset($office) ? $office->getOfficeName() : "";
    }

    public static function findFirstByCountryCode($country_code){
        return BinOffice::findFirst(array(
            'office_country_code = :country_code:',
            'bind' => array('country_code' => $country_code)
        ));
    }
    public function getAllOfficeByCareerId($ar_id, $lang='en', $limit = null){
        $result = array();
        $para = array('ARID'=>$ar_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT o.*, ol.* FROM \Bincg\Models\BinOffice o 
                INNER JOIN \Bincg\Models\BinOfficeLang ol 
                ON ol.office_id = o.office_id AND ol.office_lang_code = :LANG: 
                WHERE o.office_active = 'Y' AND o.office_id IN(
                    SELECT co.co_office_id FROM Bincg\Models\BinCareerOffice co 
                    WHERE co.co_career_id = :ARID: 
                )
                ORDER BY o.office_order ASC
                ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinOffice(),array_merge($item->o->toArray(), $item->ol->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinOffice 
                WHERE office_active = 'Y' AND office_id IN(
                    SELECT co_office_id FROM Bincg\Models\BinCareerOffice 
                    WHERE co_career_id = :ARID: 
                )
                ORDER BY office_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getAllOfficeHasCareer($lang='en', $limit = null){
        $result = array();
        $para = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT o.*, ol.* FROM \Bincg\Models\BinOffice o 
                INNER JOIN \Bincg\Models\BinOfficeLang ol 
                ON ol.office_id = o.office_id AND ol.office_lang_code = :LANG: 
                WHERE o.office_active = 'Y' AND o.office_id IN(
                    SELECT co.co_office_id FROM Bincg\Models\BinCareerOffice co 
                )
                ORDER BY o.office_order ASC
                ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinOffice(),array_merge($item->o->toArray(), $item->ol->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinOffice 
                WHERE office_active = 'Y' AND office_id IN(
                    SELECT co_office_id FROM Bincg\Models\BinCareerOffice 
                )
                ORDER BY office_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getAllImageByCareerInOffices($career_id){
        $result = array();
        $para = array('CAREER_ID' => $career_id);
        $sql = "SELECT * FROM \Bincg\Models\BinOfficeImage 
                WHERE image_active = 'Y' AND image_office_id IN (
                    SELECT co_office_id FROM \Bincg\Models\BinCareerOffice 
                    WHERE co_career_id = :CAREER_ID: 
                )";
        $lists = $this->modelsManager->executeQuery($sql,$para);
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
    public  static function _substr($_str, $length, $minword = 3)
    {
        $str = self::replaceword($_str);
        $sub = '';
        $len = 0;
        foreach (explode(' ', $str) as $word)
        {
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);
            if (strlen($word) > $minword && strlen($sub) >= $length)
            {
                break;
            }
        }
        return $sub . (($len < strlen($str)) ? '...' : '');
    }
    public static function replaceword($words){
        while (strpos($words, '  ') !== false) {
            $words = str_replace('  ', ' ', $words);
        }
        return $words;
    }
    public static function findAll()
    {
        return BinOffice::find(array(
            'office_active = "Y" ',
            "order" => "office_order ASC",
        ));
    }

    public static function getOfficeCombobox($offices)
    {
        $office = self::findAll();
        $output = '';
        foreach ($office as $value)
        {
            $selected = '';
            if(in_array($value->getOfficeId(), $offices))
            {
                $selected = 'selected';
            }
            $output.= "<option ".$selected." value='".$value->getOfficeId()."'>".$value->getOfficeName()."</option>";

        }
        return $output;
    }

    public function getAllActiveOfficeTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinOffice as o
                WHERE o.office_active = 'Y' AND o.office_id NOT IN 
                 (SELECT ol.office_id FROM Bincg\Models\BinOfficeLang as ol WHERE ol.office_lang_code =
                :lang_code:)";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }

}
