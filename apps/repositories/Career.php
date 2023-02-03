<?php
namespace Bincg\Repositories;
use Bincg\Models\BinCareer;
use Bincg\Models\BinCareerOffice;
use Phalcon\Di;
use Phalcon\Mvc\User\Component;
class Career extends Component
{
    public function checkKeyword($keyword, $career_id)
    {
        return BinCareer::findFirst(
            [
                'career_keyword = :careerkeyword: AND  career_id != :careerid:',
                'bind' => array('careerkeyword' => $keyword, 'careerid' => $career_id),
            ]
        );
    }
    public static function getByID($career_id) {
        return BinCareer::findFirst(array(
            'career_id = :career_id:',
            'bind' => array('career_id' => $career_id)
        ));
    }

    public static function getNameByID ($id)
    {
        $article = BinCareer::findFirstById($id);
        return ($article) ? $article->getCareerName() : "";
    }

    public static function getComboBox ($career_id)
    {
        $data = BinCareer::find("career_active = 'Y'");

        $output="";
        foreach ($data as  $value)
        {
            $selected ="";
            if($value->getCareerId() == $career_id)
            {
                $selected ="selected = 'selected'";
            }
            $output.= "<option ".$selected." value=".$value->getCareerId().">".$value->getCareerName()."</option>";
        }
        return $output;
    }
    public function getAllByOrder ($lang, $limit = null){
        $result = array();
        $para = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinCareer a 
                    INNER JOIN \Bincg\Models\BinCareerLang al 
                        ON a.career_id = al.career_id AND al.career_lang_code = :LANG: 
                    WHERE a.career_active = 'Y' AND a.career_expired = 'N' 
                    ORDER BY a.career_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinCareer(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinCareer 
                WHERE career_active = 'Y' AND career_expired = 'N' 
                ORDER BY career_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getAllByInsertTime ($lang, $limit = null){
        $result = array();
        $para = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinCareer a 
                    INNER JOIN \Bincg\Models\BinCareerLang al 
                        ON a.career_id = al.career_id AND al.career_lang_code = :LANG: 
                    WHERE a.career_active = 'Y' AND a.career_expired = 'N' 
                    ORDER BY a.career_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinCareer(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinCareer 
                WHERE career_active = 'Y' AND career_expired = 'N' 
                ORDER BY career_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getRelatedByKeyAndOrder ($key, $lang, $limit = null){
        $result = array();
        $para = array('KEY'=>$key);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinCareer a 
                    INNER JOIN \Bincg\Models\BinCareerLang al 
                        ON a.career_id = al.career_id AND al.career_lang_code = :LANG: 
                    WHERE a.career_active = 'Y' AND a.career_expired = 'N' AND a.career_keyword != :KEY: 
                    ORDER BY a.career_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinCareer(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinCareer 
                WHERE career_active = 'Y' AND career_expired = 'N' AND career_keyword != :KEY: 
                ORDER BY career_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByKey($keyword, $lang){
        $result = false;
        $para = array('keyword'=>$keyword);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinCareer a 
                    INNER JOIN \Bincg\Models\BinCareerLang al 
                        ON a.career_id = al.career_id AND al.career_lang_code = :LANG: 
                    WHERE a.career_active = 'Y' AND a.career_keyword = :keyword: AND a.career_expired = 'N'                
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists && sizeof($lists)>0){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new BinCareer(),array_merge($lists->a->toArray(), $lists->al->toArray()));
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinCareer 
                WHERE career_active = 'Y' AND career_keyword = :keyword: AND career_expired = 'N'  
                ";
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getComboboxCareerById($input,$lang)
    {
        $result = array();
        $para = array();
        $output = '';
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT c.*, cl.* FROM \Bincg\Models\BinCareer c 
                    INNER JOIN \Bincg\Models\BinCareerLang cl ON c.career_id = cl.career_id AND cl.career_lang_code = :LANG: 
                    WHERE c.career_active = 'Y'
                    ORDER BY c.career_insert_time DESC 
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && count($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinCareer(),array_merge($item->c->toArray(), $item->cl->toArray()));
                }}
        }else{
            $sql = 'SELECT * FROM Bincg\Models\BinCareer 
                WHERE career_active = "Y" 
                ORDER BY career_insert_time DESC 
                ';
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(count($lists)>0) $result = $lists;
        }
        foreach ($result as $key => $value)
        {
            $selected ="";
            if($value->career_id == $input)
            {
                $selected ="selected='selected'";
            }
            $output.= "<option ".$selected." value='".$value->career_id."'>".$value->career_name."</option>";
        }
        return $output;
    }
    private static function getAllCareer($lang)
    {
        $globalVariable = Di::getDefault()->get('globalVariable');
        if ($lang && $lang != $globalVariable->defaultLanguage) {
            $para['LANG'] = $lang;
            $modelsManager = Di::getDefault()->get('modelsManager');
            $sql = "SELECT cc.*, ccl.* FROM Bincg\Models\BinCareer cc
                    INNER JOIN Bincg\Models\BinCareerLang ccl
                    ON cc.career_id = ccl.career_id AND ccl.career_lang_code = :LANG: 
                    WHERE cc.career_active = 'Y' 
                    ORDER BY cc.career_order ASC 
                    ";
            $items = $modelsManager->executeQuery($sql, $para);
            $arr = [];
            foreach ($items as $item) {
                $arr[] = array(
                    'career_id' => $item->ccl->getCareerId(),
                    'career_keyword' => $item->cc->getCareerKeyword(),
                    'career_name' => trim(strip_tags($item->ccl->getCareerName())),
                    'career_title' => trim(strip_tags($item->ccl->getCareerTitle())),
                    'career_meta_keyword' => trim(strip_tags($item->ccl->getCareerMetaKeyWord())),
                    'career_meta_description' => trim(strip_tags($item->ccl->getCareerMetaDescription())),
                    'career_content' => trim(strip_tags($item->ccl->getCareerContent()))
                );
            }
            return $arr;
        } else {
            $items = BinCareer::find([
                'conditions' => 'career_active = :value: ',
                'bind' => ['value' => 'Y']
            ]);
            if ($items)
                return $items->toArray();
            return array();
        }
    }
    private static function getAssignOffice($lang)
    {
        $careers = self::getAllCareer($lang);
        $careerOffices = self::getCareerOffice();
        for ($i = 0; $i < count($careers); $i++) {
            $careers[$i]['office_id'] = isset($careerOffices[$careers[$i]['career_id']]) ? $careerOffices[$careers[$i]['career_id']] : array();
        }
        return $careers;
    }
    private static function getCareerOffice()
    {
        $items = BinCareerOffice::find();
        $arr = array();
        foreach ($items as $item) {
            if (empty($arr[$item->getCoCareerId()]))
                $arr[$item->getCoCareerId()] = array();
            $arr[$item->getCoCareerId()][] = $item->getCoOfficeId();
        }
        return $arr;
    }
    public static function search($office, $keyword, $lang)
    {
        $arr = self::getAssignOffice($lang);
        $arr_dest = array();
        for ($i = 0; $i < count($arr); $i++) {
            if(!empty($office) && !empty($keyword)){
                if (in_array($office, $arr[$i]['office_id'])) {
                    if (stripos($arr[$i]['career_meta_keyword'], $keyword) !== false) {
                        $arr_dest[] = $arr[$i];
                    } elseif (stripos($arr[$i]['career_title'], $keyword) !== false) {
                        $arr_dest[] = $arr[$i];
                    } elseif (stripos($arr[$i]['career_meta_description'], $keyword) !== false) {
                        $arr_dest[] = $arr[$i];
                    } elseif (stripos($arr[$i]['career_content'], $keyword) !== false) {
                        $arr_dest[] = $arr[$i];
                    }
                }
            } elseif(!empty($office)){
                if (in_array($office, $arr[$i]['office_id'])) {
                    $arr_dest[] = $arr[$i];
                }
            } elseif (!empty($keyword)) {
                if (stripos($arr[$i]['career_meta_keyword'], $keyword) !== false) {
                    $arr_dest[] = $arr[$i];
                } elseif (stripos($arr[$i]['career_title'], $keyword) !== false) {
                    $arr_dest[] = $arr[$i];
                } elseif (stripos($arr[$i]['career_meta_description'], $keyword) !== false) {
                    $arr_dest[] = $arr[$i];
                } elseif (stripos($arr[$i]['career_content'], $keyword) !== false) {
                    $arr_dest[] = $arr[$i];
                }
            }
        }
        return $arr_dest;
    }
    public function getAllActiveCareerTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinCareer as c
                WHERE c.career_active = 'Y' AND c.career_id NOT IN 
                 (SELECT cl.career_id FROM Bincg\Models\BinCareerLang as cl WHERE cl.career_lang_code =
                :lang_code:)";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}