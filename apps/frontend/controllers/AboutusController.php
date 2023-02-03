<?php
namespace Score\Frontend\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\Page;
use Score\Repositories\Type;

class AboutusController extends ControllerBase
{
    public function indexAction()
    {
        $page = new Page();
        $page->AutoGenMetaPage("about-us",defined('txt_who_we_are') ? txt_who_we_are : '',$this->lang_code);
        $page->generateStylePage('about-us');
        $parent_keyword = 'about-us';
        $type_id = $this->globalVariable->typeAboutUsId;
        $repoType = new Type();
        $repoArticle = new Article();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $articles = $repoArticle->getByTypeAndOrder($type_id,$this->lang_code, 1);
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $list_articles = $repoArticle->getByTypeAndOrder($type_id, $this->lang_code, 6);
        if(count($articles) > 0) {
            $this->view->ar_content = $articles[0]->getArticleContent(true);
        }
        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'articles'       => $articles,
            'banners'        => $banners,
            'list_articles' => $list_articles,
        ]);
    }

    public function detailAction()
    {
        $repoArticle = new Article();
        $keyword = $this->dispatcher->getParam("ar-key");
        $type_id = $this->globalVariable->typeAboutUsId;
        if(!$type_id){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }

        $article = $repoArticle->getByKey($keyword, $this->lang_code);

        if(!$article) {
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByKeyWord($keyword, $this->lang_code);

        $list_articles = $repoArticle->getByTypeAndOrder($type_id, $this->lang_code, 6);
        $this->tag->setTitle(html_entity_decode($article->getArticleTitle(),ENT_QUOTES));
        $this->view->setVars([
            'meta_key'          => $article->getArticleMetaKeyword(),
            'meta_descript'     => $article->getArticleMetaDescription(),
            'meta_social_image' => $article->getArticleMetaImage(),

            'keyword' => $article->getArticleKeyword(),
            'name' => $article->getArticleName(),
            'ar_content'       => $article->getArticleContent($this->lang_url_slashed, true),
            'banners'        => $banners,
            'list_articles' => $list_articles,
        ]);
    }
}