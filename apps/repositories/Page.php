<?php

namespace Score\Repositories;

use Score\Models\ScPage;
use Phalcon\Mvc\User\Component;

class Page extends Component
{
    public static function getByID($page_id) {
        return ScPage::findFirst(array(
            'page_id = :page_id:',
            'bind' => array('page_id' => $page_id)
        ));
    }
    public static function checkKeyword($keyword, $page_id)
    {
        $result = ScPage::findFirst(array(
            "page_keyword = :keyword: AND page_id != :pageID:",
            "bind" => array("keyword" => $keyword,
                "pageID" => $page_id)
        ));
        if ($result) {
            return false;
        }
        return true;
    }
    public function findFirstPageByPageKeyword($page_keyword,$lang='en')
    {
        $page = false;
        if ($lang && $lang != $this->globalVariable->defaultLanguage) {
            $sql = "SELECT p.*, pl.* FROM \Score\Models\ScPageLang pl  
                    INNER JOIN \Score\Models\ScPage p  
                        ON pl.page_id = p.page_id AND pl.page_lang_code = '$lang' 
                    WHERE p.page_keyword = '$page_keyword' 
                ";
            $lists = $this->modelsManager->executeQuery($sql)->getFirst();
            if($lists && sizeof($lists)>0){
                $page = \Phalcon\Mvc\Model::cloneResult(
                    new ScPage(),
                    [
                        "page_id" => $lists->p->getPageId(),
                        "page_name" => $lists->pl->getPageName(),
                        "page_title" => $lists->pl->getPageTitle(),
                        "page_keyword" => $lists->p->getPageKeyword(),
                        "page_meta_keyword" => $lists->pl->getPageMetaKeyWord(),
                        "page_meta_description" => $lists->pl->getPageMetaDescription(),
                        "page_content" => $lists->pl->getPageContent(),
                        "page_meta_image" => $lists->pl->getPageMetaImage(),
                    ]
                );
            }
        }else{
            $page = ScPage::findFirst(array(
                'page_keyword = :page_keyword:',
                'bind' => array('page_keyword' => $page_keyword)
            ));
        }
        return $page;
    }
    public function AutoGenMetaPage($page_keyword, $value_default, $lang='en', $more_value = null)
    {
        $page_object = $this->findFirstPageByPageKeyword($page_keyword,$lang);
        if($page_object){
            $this->tag->setTitle($page_object->getPageTitle().$more_value);
            $this->view->meta_key = $page_object->getPageMetaKeyword().$more_value;
            $this->view->meta_descript = $page_object->getPageMetaDescription().$more_value;
            $this->view->menu_bread  =  $page_object->getPageName().$more_value;
            $this->view->page_content = $page_object->getPageContent();
            $this->view->meta_social_image = $page_object->getPageMetaImage();
        }
        else {
            $this->tag->setTitle($value_default.$more_value);
            $this->view->meta_key = $value_default.$more_value;
            $this->view->meta_descript = $value_default.$more_value;
            $this->view->menu_bread = $value_default.$more_value;
            $this->view->page_content = '';
            $this->view->meta_social_image = '';
        }
    }    
    public function generateParentBread($page_keyword,$default,$lang){
        $page_object = $this->findFirstPageByPageKeyword($page_keyword,$lang);
        if(!$page_object){
            $this->view->parent_bread = $default;
        }else{
            $this->view->parent_bread = $page_object->getPageName();
        }
    }
    public function generateStylePage($page_keyword){
        $page_object = $this->findFirstPageByPageKeyword($page_keyword);
        if(!$page_object) {
            $this->view->page_style = '';
        }
        else{
            $this->view->page_style = $page_object->getPageStyle();
        }
    }
    public function getAllActivePageTranslate ($limit = null, $lang_code){
        $result = array();
        $sql = "SELECT * FROM Score\Models\ScPage as a
                WHERE a.page_id NOT IN 
                 (SELECT al.page_id FROM Score\Models\ScPageLang as al WHERE al.page_lang_code =
                :lang_code:) ";
        if (isset($limit) && is_numeric($limit) && $limit > 0) {
            $sql .= ' LIMIT '.$limit;
        }
        $lists = $this->modelsManager->executeQuery($sql,array('lang_code' => $lang_code));
        if(sizeof($lists)>0) $result = $lists;
        return $result;
    }
}