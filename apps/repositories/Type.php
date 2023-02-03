<?php

namespace Score\Repositories;

use Score\Models\ScType;
use Phalcon\Mvc\User\Component;
class Type extends Component
{
    public function checkKeyword($type_keyword, $type_parent_id, $type_id)
    {
        return ScType::findFirst(
            [
                'type_keyword = :typekeyword: AND type_parent_id = :typeparentid: AND type_id != :typeid:',
                'bind' => array('typekeyword' => $type_keyword, 'typeparentid' => $type_parent_id, 'typeid' => $type_id),
            ]
        );
    }

    public function getComboboxTypeMulti($str = "", $parent = 0, $inputslc)
    {
        $sql = 'SELECT type_id, type_parent_id, type_name FROM Score\Models\ScType WHERE type_parent_id = :PARENTID: Order By type_order ASC';
        $data = $this->modelsManager->executeQuery($sql,
            array(
                "PARENTID" => $parent
            ));
        $type_id = explode(',', $inputslc);
        $output = "";
        foreach ($data as $key => $value) {
            $selected = "";
            if (in_array($value->type_id, $type_id)) {
                $selected = "selected='selected'";
            }
            $output .= "<option " . $selected . " value='" . $value->type_id . "'>" . $str . $value->type_name . "</option>";
            $output .= $this->getComboboxTypeMulti($str . "----", $value->type_id, $inputslc);

        }
        return $output;
    }

    public function getComboboxType($str = "", $parent = 0, $inputslc)
    {
        $sql = 'SELECT type_id, type_parent_id, type_name FROM Score\Models\ScType WHERE type_parent_id = :PARENTID: Order By type_order ASC';
        $data = $this->modelsManager->executeQuery($sql,
            array(
                "PARENTID" => $parent
            ));
        $output = "";
        foreach ($data as $key => $value) {
            $selected = "";
            if ($value->type_id == $inputslc) {
                $selected = "selected='selected'";
            }
            $output .= "<option " . $selected . " value='" . $value->type_id . "'>" . $str . $value->type_name . "</option>";
            $output .= $this->getComboboxType($str . "----", $value->type_id, $inputslc);

        }
        return $output;
    }
    public static function getNameByID($id)
    {
        $result = ScType::findFirstById($id);
        return $result ? $result->getTypeName() : '';
    }
    public static function getFirstChild ($type_parent_id)
    {
        return  $result = ScType::findFirst(array(
            "type_parent_id = :type_PARENTID:",
            "bind" => array("type_PARENTID" => $type_parent_id)
        ));
    }
    public function getTypeById($id, $lang){
        $result = false;
        $para = array('ID'=>$id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.*, tl.* FROM \Score\Models\ScType t 
                    INNER JOIN \Score\Models\ScTypeLang tl 
                    ON t.type_id = tl.type_id AND tl.type_lang_code = :LANG: 
                    WHERE t.type_active = 'Y' AND t.type_id = :ID: 
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new ScType(),array_merge($lists->t->toArray(), $lists->tl->toArray()));
            }
        }else{
            $sql = "SELECT * FROM Score\Models\ScType 
                WHERE type_active = 'Y' AND type_id = :ID: 
                ";
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if($lists) $result = $lists;
        }
        return $result;
    }
    /*
     * return ScType
     * */
    public function getTypeByKeyword($keyword, $lang){
        $result = false;
        $para = array('KEYWORD'=>$keyword);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.*, tl.* FROM \Score\Models\ScType t 
                    INNER JOIN \Score\Models\ScTypeLang tl 
                    ON t.type_id = tl.type_id AND tl.type_lang_code = :LANG: 
                    WHERE t.type_active = 'Y' AND t.type_keyword = :KEYWORD: 
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new ScType(),array_merge($lists->t->toArray(), $lists->tl->toArray()));
            }
        }else{
            $sql = "SELECT * FROM Score\Models\ScType 
                WHERE type_active = 'Y' AND type_keyword = :KEYWORD: 
                ";
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if($lists) $result = $lists;
        }
        return $result;
    }
    public function getTypeByParent ($parent, $lang, $limit = null)
    {
        $result = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.*, tl.* FROM Score\Models\ScType t 
                    INNER JOIN Score\Models\ScTypeLang tl 
                        ON t.type_id = tl.type_id AND tl.type_lang_code = :LANG:  
                    WHERE t.type_active = 'Y' AND t.type_parent_id = :PARENTID:  
                    ORDER BY t.type_order ASC 
                ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= " LIMIT ".$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,array("LANG" => $lang,"PARENTID" => $parent));
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ScType(),array_merge($item->t->toArray(), $item->tl->toArray()));
                }}
        }else{
            $sql = "SELECT * FROM \Score\Models\ScType 
                WHERE type_active = 'Y' AND type_parent_id = :PARENTID: 
                ORDER BY type_order ASC 
                ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= " LIMIT ".$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,array("PARENTID" => $parent));
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getRelatedTypeByKeywordAndParent($keyword,$type_id,$lang,$limit = null) {
        $result = array();
        $para = array('KEYWORD'=>$keyword,'TYPEID'=>$type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT t.*, tl.* FROM \Score\Models\ScType t 
                    INNER JOIN \Score\Models\ScTypeLang tl 
                        ON t.type_id = tl.type_id AND tl.type_lang_code = :LANG: 
                    WHERE t.type_active = 'Y' AND t.type_keyword != :KEYWORD: AND t.type_parent_id = :TYPEID:  ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new ScType(),
                        array_merge($item->t->toArray(), $item->tl->toArray())
                    );
                }
            }
        }else{
            $sql = "SELECT * FROM Score\Models\ScType 
                WHERE type_active = 'Y' AND type_keyword != :KEYWORD: AND type_parent_id = :TYPEID: ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getTypeLang ($str = "", $inputslc, $lang)
    {
        $type_news_id = $this->globalVariable->typeNewsId;
        $type_careers_id = $this->globalVariable->typeCareersId;
        $arr_para = array('TYPENEWSID'=>$type_news_id,'TYPECAREERSID'=>$type_careers_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = 'SELECT  tl.type_name,t.type_id,t.type_parent_id 
                    FROM Score\Models\ScType t
                    INNER JOIN Score\Models\ScTypeLang tl ON t.type_id = tl.type_id 
                    WHERE t.type_active = "Y" AND t.type_id != :TYPENEWSID: AND t.type_id != :TYPECAREERSID: AND type_lang_code = :LANG: 
                    ORDER BY type_order ASC';
            $arr_para["LANG"] = $lang;
        }
        else {
            $sql = 'SELECT type_id, type_parent_id, type_name FROM Score\Models\ScType WHERE type_active = "Y" AND type_id != :TYPENEWSID: AND type_id != :TYPECAREERSID: ORDER BY type_order ASC';
        }
        $data = $this->modelsManager->executeQuery($sql,$arr_para);
        $output="";
        foreach ($data as $key => $value)
        {
            if($value->type_parent_id!=$type_news_id) {
                $selected = "";
                if ($value->type_id == $inputslc) {
                    $selected = "selected='selected'";
                }
                $output .= "<option " . $selected . " value='" . $value->type_id . "'>" . $str . $value->type_name . "</option>";
            }
        }
        $output.= $this->getTypeNewsLang($str,$inputslc,$lang);
        return $output;
    }
    public function getTypeNewsLang ($str = "", $inputslc, $lang)
    {
        $type_news_id = $this->globalVariable->typeNewsId;
        $arr_para = array('TYPENEWSID'=>$type_news_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = 'SELECT  tl.type_name,t.type_id,t.type_parent_id 
                    FROM Score\Models\ScType t
                    INNER JOIN Score\Models\ScTypeLang tl ON t.type_id = tl.type_id 
                    WHERE t.type_active = "Y" AND t.type_parent_id = :TYPENEWSID:  AND tl.type_lang_code = :LANG: 
                    ORDER BY t.type_order ASC';
            $arr_para["LANG"] = $lang;
        }
        else {
            $sql = 'SELECT type_id, type_parent_id, type_name FROM Score\Models\ScType WHERE type_active = "Y" AND type_parent_id = :TYPENEWSID: ORDER BY type_order ASC';
        }
        $data = $this->modelsManager->executeQuery($sql,$arr_para);
        $output ="";
        $output .= '<optgroup label="News">';
        foreach ($data as $key => $value)
        {
            $selectedService ="";
            if($value->type_id == $inputslc)
            {
                $selectedService ="selected='selected'";
            }
            $output.= "<option ".$selectedService." value='".$value->type_id."'>".$str.$value->type_name."</option>";
        }
        $output .= '</optgroup>';
        return $output;
    }
    public function getAllActiveTypeTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Score\Models\ScType as b
                WHERE b.type_active = 'Y' AND b.type_id NOT IN 
                 (SELECT bl.type_id FROM Score\Models\ScTypeLang as bl WHERE bl.type_lang_code =
                :lang_code:)
                ORDER BY b.type_order ASC ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}