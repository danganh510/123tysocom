<?php

namespace Bincg\Repositories;

use Bincg\Models\BinBanner;
use Bincg\Models\BinType;
use Phalcon\Mvc\User\Component;

class Banner extends Component
{
    const CONTROLLER = 'controller';
    const TYPE = 'type';
    const ARTICLE = 'article';

    public static function getArrayController() {
        return array(
            self::CONTROLLER.'_index' => 'Home page',
            self::CONTROLLER.'_aboutus' => 'About Us',
            self::CONTROLLER.'_services' => 'What We Do',
            self::CONTROLLER.'_corporatesocialresponsibility' => 'Corporate Social Responsibility',
            self::CONTROLLER.'_careers' => 'Careers',
            self::CONTROLLER.'_search' => 'Search',
            self::CONTROLLER.'_contactus' => 'Contact Us',
        );
    }

    public function getArrayType() {
        return array();
    }

    public function getControllerCombobox($controller_search){
        $arrController = self::getArrayController();
        $string = "<optgroup label=\"General\">";
        foreach($arrController as $controller => $title){
            $seleted = "";
            if($controller == $controller_search) {
                $seleted = "selected='selected'";
            }
            $string.="<option ".$seleted." value='".$controller."'>".$title."</option>";
        }
        $string .= '</optgroup>';
        $string .= $this->getArticleGroup($controller_search);
        return $string;
    }  

    public function getArticle ($str = "", $type = 0, $inputslc)
    {
        $sql = "SELECT article_keyword, article_type_id, article_name FROM Bincg\Models\BinArticle 
                WHERE article_type_id = :typeId: AND article_active = 'Y'
                Order By article_order ASC";
        $data = $this->modelsManager->executeQuery($sql,
            array(
                "typeId" => $type
            ));
        $output="";
        foreach ($data as $key => $value)
        {
            $selected ="";
            if(self::ARTICLE.'_'.$value->article_keyword == $inputslc)
            {
                $selected ="selected='selected'";
            }
            $output.= "<option ".$selected." value='".self::ARTICLE.'_'.$value->article_keyword."'>".$str.$value->article_name."</option>";
        }
        return $output;
    }
    public static function getValue($value,$type)
    {
        $result = '';
        $arsValue = explode('_',$value);
        if(count($arsValue) == 2 && $arsValue[0] == $type) {
            $result = $arsValue[1];
        }
        return $result;
    }

    public static function getItem($controller,$article)
    {
        if(!empty(trim($controller))){
            $result = self::CONTROLLER.'_'.$controller;
        }else{
            $result = self::ARTICLE.'_'.$article;
        }
        return $result;
    }

    public static function getNameController($controller,$article)
    {
        $result = '';
        if (!empty(trim($controller))) {
            $result = isset(self::getArrayController()[$controller]) ? self::getArrayController()[$controller] : $controller;
        } elseif (!empty(trim($article))) {
            $keyword = self::getValue($article,self::ARTICLE);
            $result = Article::getNameByKeyword($keyword );
        }
        return $result;
    }

    public function getArticleGroup($controller_search)
    {
        $result = '';
        $types = BinType::find('type_active = "Y"');
        $arTypeBanner = array(
           2,3,4
        );
        foreach ($types as $type) {
            if(in_array($type->getTypeId(), $arTypeBanner)) {
                if (Article::getFirstByType($type->getTypeId())) {
                    $name = Type::getNameByID($type->getTypeId());
                    $result .= "<optgroup label =\"$name - Article \">";
                    $result .= $this->getArticle('', $type->getTypeId(), $controller_search);
                    $result .= '</optgroup>';
                }
            }

        }
        return $result;
    }

    public function getBannerByController($controller_name,$lang='en'){
        $result = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT b.*, bl.* FROM \Bincg\Models\BinBanner b
                LEFT JOIN \Bincg\Models\BinBannerLang bl ON bl.banner_lang_code = '$lang' AND bl.banner_id = b.banner_id 
                WHERE b.banner_active = 'Y' AND b.banner_controller = '$controller_name' 
                ORDER BY b.banner_order ASC 
                ";
            $lists = $this->modelsManager->executeQuery($sql);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinBanner(),
                        [
                            "banner_id" => $item->b->getBannerId(),
                            "banner_controller" => $item->b->getBannerController(),
                            "banner_article_keyword" => $item->b->getBannerArticleKeyword(),
                            "banner_title" => $item->bl->getBannerTitle(),
                            "banner_subtitle" => $item->bl->getBannerSubTitle(),
                            "banner_content" => $item->bl->getBannerContent(),
                            "banner_link" => $item->b->getBannerLink(),
                            "banner_image" => $item->b->getBannerImage(),
                            "banner_image_mobile" => $item->b->getBannerImageMobile(),
                            "banner_order" => $item->b->getBannerOrder(),
                            "banner_active" => $item->b->getBannerActive()
                        ]
                    );
                }
            }
        }else{
            $lists = BinBanner::query()
                ->where("banner_active = 'Y'")
                ->andWhere("banner_controller = '$controller_name'")
                ->orderBy("banner_order ASC")
                ->execute();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }
    public function getBannerByKeyWord($banner_article_keyword,$lang='en'){
        $result = array();
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT b.*, bl.* FROM \Bincg\Models\BinBanner b
                LEFT JOIN \Bincg\Models\BinBannerLang bl ON bl.banner_lang_code = '$lang' AND bl.banner_id = b.banner_id 
                WHERE b.banner_active = 'Y' AND b.banner_article_keyword = '$banner_article_keyword' 
                ORDER BY b.banner_order ASC 
                ";
            $lists = $this->modelsManager->executeQuery($sql);
            if($lists && sizeof($lists)>0){
                foreach ($lists as $item){
                    $result[] = \Phalcon\Mvc\Model::cloneResult(
                        new BinBanner(),
                        [
                            "banner_id" => $item->b->getBannerId(),
                            "banner_controller" => $item->b->getBannerController(),
                            "banner_article_keyword" => $item->b->getBannerArticleKeyword(),
                            "banner_title" => $item->bl->getBannerTitle(),
                            "banner_subtitle" => $item->bl->getBannerSubTitle(),
                            "banner_content" => $item->bl->getBannerContent(),
                            "banner_link" => $item->b->getBannerLink(),
                            "banner_image" => $item->b->getBannerImage(),
                            "banner_image_mobile" => $item->b->getBannerImageMobile(),
                            "banner_order" => $item->b->getBannerOrder(),
                            "banner_active" => $item->b->getBannerActive()
                        ]
                    );
                }
            }
        }else{
            $lists = BinBanner::query()
                ->where("banner_active = 'Y'")
                ->andWhere("banner_article_keyword = '$banner_article_keyword'")
                ->orderBy("banner_order ASC")
                ->execute();
            if(sizeof($lists)>0) $result = $lists;
        }
        return $result;
    }

    public function getAllActiveBannerTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Bincg\Models\BinBanner as b
                WHERE b.banner_active = 'Y' AND b.banner_id NOT IN 
                 (SELECT bl.banner_id FROM Bincg\Models\BinBannerLang as bl WHERE bl.banner_lang_code =
                :lang_code:)
                ORDER BY b.banner_order ASC ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}
