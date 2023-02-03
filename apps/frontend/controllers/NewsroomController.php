<?php
namespace Score\Frontend\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Page;
use Score\Repositories\Type;
use Score\Utils\Validator;

class NewsroomController extends ControllerBase
{

    public function indexAction()
    {
        $page = new Page();
        $page->generateParentBread("newsroom",defined('txt_news_room') ? txt_news_room : '', $this->lang_code);
        $page->generateStylePage('newsroom');
        $type_id = $this->globalVariable->typeNewsroom;
        $parent_keyword = 'newsroom';
        $repoType = new Type();
        $type = $repoType->getTypeById($type_id,$this->lang_code);
        if(!$type){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $type_child = $repoType->getTypeById($type_id,$this->lang_code);

        $type_child_id = $type_child->getTypeId();
        $arrParameter = array();
        if ($this->lang_code && $this->lang_code != $this->globalVariable->defaultLanguage) {
            $count_sql = "SELECT COUNT(*) AS count ";
            $table_sql = " FROM \Score\Models\ScArticle n  
                        INNER JOIN \Score\Models\ScArticleLang nl ON nl.article_id = n.article_id AND nl.article_lang_code = :LANG: 
                        WHERE n.article_active = 'Y' AND FIND_IN_SET ($type_child_id, n.article_type_id) 
                        ORDER BY n.article_insert_time DESC 
	                  ";
            $select_sql = " SELECT nl.article_name,nl.article_summary,nl.article_icon,nl.article_meta_image,n.article_icon_large,n.article_icon_large_mobile,n.article_insert_time,n.article_keyword ";
            $arrParameter = array("LANG" => $this->lang_code);
        }else{
            $count_sql = "SELECT COUNT(*) AS count ";
            $table_sql = " FROM \Score\Models\ScArticle n  
                      WHERE n.article_active = 'Y' AND FIND_IN_SET ($type_child_id, n.article_type_id)
                      ORDER BY n.article_insert_time DESC 
	                  ";
            $select_sql = " SELECT n.article_name,n.article_summary,n.article_icon,n.article_meta_image,n.article_icon_large,n.article_icon_large_mobile,n.article_insert_time,n.article_keyword ";
        }
        $count_query = $this->modelsManager->executeQuery($count_sql.$table_sql,$arrParameter);
        $validator = new Validator();
        $current_page = $this->request->getQuery('page');
        $current_page =isset($current_page)?$current_page:1;
        $totalItems = $count_query[0]->count;
        $check = false;
        if ((isset($current_page))&&($validator->validInt($current_page) == false || $current_page < 1)) {
            $current_page = 1;
            $check = true;
        }

        $itemsPerPage = 6;
        $urlPage = '?';
        if ($urlPage != '?') $urlPage .= '&';
        $urlPattern = $urlPage.'page=(:num)';
        $paginator = new \Paginator($totalItems, $itemsPerPage, $current_page, $urlPattern);
        $offset = ($current_page-1) * $itemsPerPage;
        $limit_sql = " LIMIT ".$offset.",".$itemsPerPage;
        $articles = $this->modelsManager->executeQuery($select_sql.$table_sql.$limit_sql,$arrParameter);
        if(($paginator->getNumPages() > 0)&&($current_page > $paginator->getNumPages())){
            $current_page = $paginator->getNumPages();
            $check = true;
        }

        if($check) {
            $urlPage .= 'page=' . $current_page;
            $this->response->redirect($this->lang_url.'/'.$parent_keyword.'/'.$parent_keyword.$urlPage . '');
            return;
        }
        if($this->isMobile){
            $paginator->setMaxPagesToShow(5);
        }else{
            $paginator->setMaxPagesToShow(6);
        }
        $this->tag->setTitle($type_child->getTypeTitle());
        $this->view->setVars([
            'parent_keyword'    => $parent_keyword,
            'type_child'        => $type_child,
            'articles'          => $articles,
            'htmlPaginator'     => $paginator->toHtmlFrontend(),
            'meta_key'          => $type_child->getTypeMetaKeyword(),
            'meta_descript'     => $type_child->getTypeMetaDescription(),
            'menu_bread'        => $type_child->getTypeName(),
        ]);

    }
    public function detailAction()
    {
        $page = new Page();
        $page->generateParentBread("newsroom",defined('txt_news_room') ? txt_news_room : '', $this->lang_code);
        $page->generateStylePage('newsroom');
        $type_id = $this->globalVariable->typeNewsroom;
        $repoType = new Type();

        $repoArticle = new Article();
        $ar_keyword = $this->dispatcher->getParam("ar-key");
        $article = $repoArticle->getByKeyAndType($ar_keyword, $type_id, $this->lang_code);

        if (!$article) {
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }

        $type_child = $repoType->getTypeById($article->getArticleTypeId(),$this->lang_code);
        if(!$type_child){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }

        $this->tag->setTitle(html_entity_decode($article->getArticleTitle(),ENT_QUOTES));
        $this->view->setVars([
            'parent_keyword'    => 'newsroom',
            'article'           => $article,
            'meta_key'          => $article->getArticleMetaKeyword(),
            'meta_descript'     => $article->getArticleMetaDescription(),
            'meta_social_image' => $article->getArticleMetaImage(),
            'menu_bread'        => $article->getArticleName(),
            'ar_time'           => $article->getArticleInsertTime(),
            'ar_content'        => $article->getArticleContent($this->lang_url_slashed),
            'related_articles'  => $repoArticle->getRelatedByKeyAndType($ar_keyword,$type_child->getTypeId(),$this->lang_code,6),
        ]);
    }
}