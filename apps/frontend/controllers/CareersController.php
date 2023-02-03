<?php
namespace Bincg\Frontend\Controllers;

use Bincg\Repositories\Article;
use Bincg\Repositories\Banner;
use Bincg\Repositories\Career;
use Bincg\Repositories\Office;
use Bincg\Repositories\Page;
use Bincg\Repositories\Type;

class CareersController extends ControllerBase
{
    public function indexAction()
    {
        $page = new Page();
        $page->AutoGenMetaPage("careers",defined('txt_careers') ? txt_careers : '',$this->lang_code);
        $page->generateStylePage('careers');
        $parent_keyword = 'careers';
        $type_id = $this->globalVariable->typeCareersId;
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $repoArticle = new Article();
        $general_articles = $repoArticle->getByTypeAndOrder($type_id,$this->lang_code,3);
        $repoCareer = new Career();
        $articles = $repoCareer->getAllByInsertTime($this->lang_code);
        $repoOffice = new Office();
        $offices = $repoOffice->getAllOffice($this->lang_code);
        $this->view->setVars([
            'parent_keyword'  => $parent_keyword,
            'banners'  => $banners,
            'articles'  => $articles,
            'general_articles'  => $general_articles,
            'offices'  => $offices,
        ]);
    }
    public function detailAction()
    {
        $page = new Page();
        $page->generateParentBread("careers",defined('txt_careers') ? txt_careers : '', $this->lang_code);
        $page->generateStylePage('careers');
        $type_id = $this->globalVariable->typeCareersId;
        $parent_keyword = 'careers';
        $ar_keyword = $this->dispatcher->getParam("ar-key");
        $repoArticle = new Article();
        $repoCareer = new Career();
        $article = $repoArticle->getByKeyAndType($ar_keyword,$type_id,$this->lang_code);
        $career = $repoCareer->getByKey($ar_keyword,$this->lang_code);
        if (!$article && !$career) {
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $related_careers = $repoCareer->getRelatedByKeyAndOrder($ar_keyword,$this->lang_code,5);
        $related_articles = $repoArticle->getRelatedByKeyAndType($ar_keyword,$type_id,$this->lang_code,3);
        $offices = array();
        $office_images = array();
        if($article){
            $this->tag->setTitle($article->getArticleTitle());
            $this->view->setVars([
                'checkCareer'       => false,
                'meta_key'          => $article->getArticleMetaKeyword(),
                'meta_descript'     => $article->getArticleMetaDescription(),
                'meta_social_image' => $article->getArticleMetaImage(),
                'menu_bread'        => $article->getArticleName(),
                'ar_time'           => $article->getArticleInsertTime(),
                'ar_content'        => $article->getArticleContent($this->lang_url_slashed)
            ]);
        }
        if($career){
            $repoOffice = new Office();
            $offices = $repoOffice->getAllOfficeByCareerId($career->getCareerId(),$this->lang_code);
            $office_images = $repoOffice->getAllImageByCareerInOffices($career->getCareerId());
            $this->tag->setTitle($career->getCareerTitle());
            $this->view->setVars([
                'checkCareer'       => true,
                'meta_key'          => $career->getCareerMetaKeyword(),
                'meta_descript'     => $career->getCareerMetaDescription(),
                'meta_social_image' => $career->getCareerMetaImage(),
                'menu_bread'        => $career->getCareerName(),
                'ar_time'           => $career->getCareerInsertTime(),
                'ar_content'        => $career->getCareerContent($this->lang_url_slashed),
            ]);
        }
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'keyword'           => $ar_keyword,
            'article'           => $article,
            'career'            => $career,
            'related_careers'   => $related_careers,
            'related_articles'  => $related_articles,
            'offices'           => $offices,
            'office_images'     => $office_images,
        ]);
    }
    public function searchAction()
    {
        $page = new Page();
        $page->generateParentBread("careers",defined('txt_careers') ? txt_careers : '', $this->lang_code);
        $page->generateStylePage('careers');
        $page->AutoGenMetaPage('search-career',defined('txt_search') ? txt_search : 'Search',$this->lang_code);
        $parent_keyword = 'careers';
        $careers = array();
        $formData = array(
            'office' => '',
            'keyword' => ''
        );
        if ($this->request->isPost()) {
            $data['office'] = $this->request->getPost('office');
            $data['keyword'] = $this->request->getPost('keyword', array('string', 'trim'));
            $formData = $data;
            $careers = Career::search($data['office'], $data['keyword'], $this->lang_code);
        }
        $repoOffice = new Office();
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $this->view->setVars(array(
            'careers' => $careers,
            'offices' => $repoOffice->getAllOffice($this->lang_code),
            'formData' => $formData,
            'banners'  => $banners,
            'parent_keyword'  => $parent_keyword,
        ));
    }
}