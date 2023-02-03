<?php

namespace Bincg\Repositories;

use Bincg\Models\BinArticle;
use Phalcon\Mvc\User\Component;
class Article extends Component
{
    public static function checkKeyword($article_keyword, $article_type_id, $article_id)
    {
        return BinArticle::findFirst(
            [
                'article_keyword = :articlekeyword: AND article_type_id = :articletypeid:  AND article_id != :articleid:',
                'bind' => array('articlekeyword' => $article_keyword, 'articletypeid' => $article_type_id, 'articleid' => $article_id),
            ]
        );
    }
    public static function checkCodeCountry($article_country_code,$article_type_id, $article_id){
        return BinArticle::findFirst(array('article_country_code = :ARTICLECOUNTRYCODE: AND article_type_id = :ARTICLETYPEID: AND article_id !=:ARTICLEID:','bind' => array('ARTICLECOUNTRYCODE' => $article_country_code, 'ARTICLETYPEID' =>$article_type_id, 'ARTICLEID' => $article_id),));
    }
    public static function getByID($article_id)
    {
        return BinArticle::findFirst(array(
            'article_id = :article_id:',
            'bind' => array('article_id' => $article_id)
        ));
    }
    public static function getFirstByType ($type_id)
    {
        $result = BinArticle::findFirst(array(
            "article_type_id = :typeID:",
            "bind" => array("typeID" => $type_id)
        ));
        return $result;
    }
    public static function getNameByKeyword($keyword){
        $result = BinArticle::findFirst(array(
            "article_keyword = :keyword:",
            "bind" => array("keyword" => $keyword)
        ));
        return $result?$result->getArticleName():'';
    }
    public function getByTypeAndOrder ($type_id, $lang, $limit = null){
        $result = array();
        $para = array('TYPEID'=>$type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPEID: 
                    ORDER BY a.article_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_type_id = :TYPEID: 
                ORDER BY article_order ASC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    /**
     * @return BinArticle
     */
    public function getByTypeAndInsertTime ($type_id, $lang, $limit = null){
        $result = array();
        $para = array('TYPEID'=>$type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPEID: 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_type_id = :TYPEID: 
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByTypeAndArrIdAndInsertTime ($type_id, $arr_id, $lang, $limit = null){
        $result = array();
        $para = array('TYPEID'=>$type_id,'ARRID'=>$arr_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPEID: AND !FIND_IN_SET (a.article_id,:ARRID:) 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_type_id = :TYPEID: AND !FIND_IN_SET (article_id,:ARRID:) 
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByArrTypeAndInsertTime ($type_id, $lang, $limit = null){
        $result = array();
        $para = array('TYPEID'=>$type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id IN(
                        SELECT t.type_id FROM Bincg\Models\BinType t 
                        WHERE t.type_active = 'Y' AND t.type_parent_id = :TYPEID: 
                    ) 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_type_id IN(
                    SELECT type_id FROM Bincg\Models\BinType 
                    WHERE type_active = 'Y' AND type_parent_id = :TYPEID: 
                ) 
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByArrTypeAndInsertTimeIsHomeY ($type_id, $lang, $limit = null){
        $result = array();
        $para = array('TYPEID'=>$type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_is_home = 'Y' AND a.article_type_id IN(
                        SELECT t.type_id FROM Bincg\Models\BinType t 
                        WHERE t.type_active = 'Y' AND t.type_parent_id = :TYPEID: 
                    ) 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_is_home = 'Y' AND article_type_id IN(
                    SELECT type_id FROM Bincg\Models\BinType 
                    WHERE type_active = 'Y' AND type_parent_id = :TYPEID: 
                ) 
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByKeyAndType($keyword, $type_id, $lang){
        $result = false;
        $para = array('keyword'=>$keyword, 'TYPEID' => $type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPEID: AND a.article_keyword = :keyword:                    
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists && sizeof($lists)>0){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new BinArticle(),array_merge($lists->a->toArray(), $lists->al->toArray()));
            }
        }else{
            $sql = 'SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = "Y" AND article_keyword = :keyword: AND article_type_id = :TYPEID: 
                ';
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getByKey($keyword, $lang){
        $result = false;
        $para = array('keyword'=>$keyword);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_keyword = :keyword:                    
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists && sizeof($lists)>0){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new BinArticle(),array_merge($lists->a->toArray(), $lists->al->toArray()));
            }
        }else{
            $sql = 'SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = "Y" AND article_keyword = :keyword: 
                ';
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getRelatedByKeyAndType($keyword, $type_id, $lang, $limit = null){
        $result = array();
        $para = array('KEYWORD'=>$keyword, 'TYPEID' => $type_id);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_type_id = :TYPEID: AND a.article_keyword != :KEYWORD: 
                    ORDER BY a.article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinArticle(),array_merge($item->a->toArray(), $item->al->toArray()));
                }
            }
        }else{
            $sql = "SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = 'Y' AND article_keyword != :KEYWORD: AND article_type_id = :TYPEID:
                ORDER BY article_insert_time DESC ";
            if (isset($limit) && is_numeric($limit) && $limit > 0) {
                $sql .= ' LIMIT '.$limit;
            }
            $lists = $this->modelsManager->executeQuery($sql,$para);
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }

    public function getByName($name, $lang){
        $result = false;
        $para = array('CONTACTNAME'=>$name);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_name = :CONTACTNAME: 
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists && sizeof($lists)>0){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new BinArticle(),array_merge($lists->a->toArray(), $lists->al->toArray()));
            }
        }else{
            $sql = 'SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = "Y" AND article_name = :CONTACTNAME: 
                ';
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public static function getServiceCheckbox($type_id,$inputslc,$lang_code)
    {
        $repoServiceArticle = new Article();
        $data = $repoServiceArticle->getByTypeAndOrder($type_id,$lang_code);
        $output = '';
        foreach ($data as  $value)
        {
            $selected = '';
            if(in_array($value->getArticleName(), $inputslc))
            {
                $selected = 'checked';
            }
            $output.= "<div class=\"col-lg-4\"><div class=\"list-custom-control\">
                       <div class=\"custom-control custom-checkbox\">
                       <input type=\"checkbox\" class=\"custom-control-input\" name=\"list_service[]\" id=\"";
            $output.= $value->getArticleId().str_replace(' ','',$value->getArticleName());
            $output.= "\" value=\"";
            $output.= $value->getArticleName();
            $output.= "\" ";
            $output.= $selected;
            $output.="><label class=\"custom-control-label\" for=\"";
            $output.= $value->getArticleId().str_replace(' ','',$value->getArticleName());
            $output.="\">";
            $output.= $value->getArticleName();
            $output.= "</label></div></div></div>";
        }
        return $output;
    }
    public function getAllActiveArticleTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinArticle as a
                WHERE a.article_active = 'Y' AND a.article_id NOT IN 
                 (SELECT al.article_id FROM Bincg\Models\BinArticleLang as al WHERE al.article_lang_code =
                :lang_code:)
                ORDER BY article_order ASC ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }

    public function getFirstListArticleByOrder($lang)
    {
        $result = false;
        $para = array('CONTACTNAME'=>$name);
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT a.*, al.* FROM \Bincg\Models\BinArticle a 
                    INNER JOIN \Bincg\Models\BinArticleLang al 
                        ON a.article_id = al.article_id AND al.article_lang_code = :LANG: 
                    WHERE a.article_active = 'Y' AND a.article_name = :CONTACTNAME: 
                    ";
            $para['LANG'] = $lang;
            $lists = $this->modelsManager->executeQuery($sql, $para)->getFirst();
            if($lists && sizeof($lists)>0){
                $result = \Phalcon\Mvc\Model::cloneResult(
                    new BinArticle(),array_merge($lists->a->toArray(), $lists->al->toArray()));
            }
        }else{
            $sql = 'SELECT * FROM Bincg\Models\BinArticle 
                WHERE article_active = "Y" AND article_order = :CONTACTNAME: 
                ';
            $lists = $this->modelsManager->executeQuery($sql,$para)->getFirst();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
}