<?php

use Score\Frontend\Controllers\ControllerBase;
use Score\Repositories\Article;
use Score\Repositories\Type;

class InformationController extends ControllerBase
{
    public function indexAction()
    {
        $type_id = $this->globalVariable->typeInformationId;
        $parent_keyword = 'info';
        $ar_key = $this->dispatcher->getParam("info-ar-key");
        $repoType = new Type();
        $repoArticle = new Article();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $article = $repoArticle->getByKeyAndType($ar_key, $type_id , $this->lang_code);
        if(!$article){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $this->tag->setTitle($article->getArticleTitle());
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'article'           => $article,
            'keyword'           => $ar_key,
            'meta_key'          => $article->getArticleMetaKeyword(),
            'meta_descript'     => $article->getArticleMetaDescription(),
            'meta_social_image' => $article->getArticleMetaImage(),
            'menu_bread'        => $article->getArticleName(),
            'ar_time'           => $article->getArticleInsertTime(),
            'ar_content'        => $article->getArticleContent($this->lang_url_slashed),
        ]);
    }
}