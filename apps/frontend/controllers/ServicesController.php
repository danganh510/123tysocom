<?php
namespace Bincg\Frontend\Controllers;

use Bincg\Repositories\Article;
use Bincg\Repositories\Banner;
use Bincg\Repositories\Page;
use Bincg\Repositories\Type;

class ServicesController extends ControllerBase
{
    public function indexAction()
    {
        $page = new Page();
        $page->AutoGenMetaPage("services",defined('txt_what_we_do') ? txt_what_we_do : '', $this->lang_code);
        $page->generateStylePage('services');
        $parent_keyword = 'services';
        $type_id = $this->globalVariable->typeServicesId;
        $repoType = new Type();
        $repoArticle = new Article();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $articles = $repoArticle->getByTypeAndOrder($type_id,$this->lang_code);
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'articles'       => $articles,
            'banners'        => $banners
        ]);
    }
    public function detailAction()
    {
        $page = new Page();
        $page->generateParentBread("services",defined('txt_what_we_do') ? txt_what_we_do : '', $this->lang_code);
        $page->generateStylePage('services');
        $type_id = $this->globalVariable->typeServicesId;
        $parent_keyword = 'services';
        $repoType = new Type();
        $repoArticle = new Article();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $ar_keyword = $this->dispatcher->getParam("ar-key");
        $article = $repoArticle->getByKeyAndType($ar_keyword,$type_id,$this->lang_code);
        if (!$article) {
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByKeyWord($ar_keyword, $this->lang_code);
        $this->tag->setTitle($article->getArticleTitle());
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'keyword'           => $ar_keyword,
            'article'           => $article,
            'banners'           => $banners,
            'meta_key'          => $article->getArticleMetaKeyword(),
            'meta_descript'     => $article->getArticleMetaDescription(),
            'meta_social_image' => $article->getArticleMetaImage(),
            'menu_bread'        => $article->getArticleName(),
            'ar_time'           => $article->getArticleInsertTime(),
            'ar_content'        => $article->getArticleContent($this->lang_url_slashed)
        ]);
    }
}