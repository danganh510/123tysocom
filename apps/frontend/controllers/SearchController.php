<?php
namespace Bincg\Frontend\Controllers;

use Bincg\Repositories\AzureSearch;
use Bincg\Repositories\Banner;
use Bincg\Repositories\SearchAzure;
use Bincg\Repositories\Suggest;
use Bincg\Repositories\Type;
use Bincg\Repositories\Page;
use Bincg\Utils\Validator;

class SearchController extends ControllerBase
{
    public function indexAction()
    {
        if (defined('PROGRAMMABLE_SEARCH_ENGINE') && PROGRAMMABLE_SEARCH_ENGINE) {
            $keyword = trim(strip_tags($this->request->get("q")));
            if(strlen($keyword) > 0) {
                $re_keyword = SearchAzure::replaceword(urldecode($keyword));
                if(strlen($re_keyword)>2){
                    Suggest::update($re_keyword,$this->lang_code);
                }
            }
            $this->view->setVars(array(
                'keyword' => $keyword,
                'parent_keyword' => 'search',
            ));
            $page = new Page();
            $page->AutoGenMetaPage('search',defined('txt_search') ? txt_search : '',$this->lang_code,' '.$keyword);
        } else {
            $parent_keyword = 'search';
            $keyword = trim(strip_tags($this->request->get("keyword")));
            $type_id = $this->request->get("slType");

            $list_search = array();
            $type = new Type();
            $select_type = $type->getTypeLang("",$type_id,$this->lang_code);
            $selectedCareer ="";
            if($type_id=="career_".$this->globalVariable->typeCareersId)
            {
                $selectedCareer ="selected='selected'";
            }
            $select_type .= "<option ".$selectedCareer." value='"."career_".$this->globalVariable->typeCareersId."'>".(defined('txt_careers') ? txt_careers : '')."</option>";
            $validator = new Validator();
            $current_page = $this->request->getQuery('page', 'int');
            $current_page =isset($current_page)?$current_page:1;
            $check = false;
            $page = new Page();
            $page->AutoGenMetaPage('search',defined('txt_search') ? txt_search : '',$this->lang_code);

            if ((isset($current_page))&&($validator->validInt($current_page) == false || $current_page < 1)) {
                $current_page = 1;
                $check = true;
            }
            $itemsPerPage = 9;
            $total_search_session = 0;
            $totalItems = 0;
            if ($this->session->has('total_search')) {
                $total_search_session = $this->session->get('total_search');
                $this->session->remove('total_search');
            }
            if(strlen($keyword) > 0) {
                if ($total_search_session > 0 && $current_page > $total_search_session) {
                    $current_page = $total_search_session;
                    $check = true;
                }
                $re_keyword = SearchAzure::replaceword($keyword);
                //Azure Search Start
                $searchKeyWords = AzureSearch::getAzureSearch($keyword, $this->lang_code, $type_id, $current_page, $itemsPerPage);
                if (isset($searchKeyWords['status']) && $searchKeyWords['status'] == 'success') {
                    $totalItems = isset($searchKeyWords['result']['@odata.count'])?$searchKeyWords['result']['@odata.count']:0;
                    $list_search = isset($searchKeyWords['result']['value'])?$searchKeyWords['result']['value']:array();
                } else {
                    $this->view->message = isset($searchKeyWords['message'])?$searchKeyWords['message']:'';
                }
                //Azure Search End
                if(count($list_search) > 0 ){
                    Suggest::update($re_keyword,$this->lang_code);
                }
            }
            $urlPage = '?';
            if (strlen($keyword) > 0) {
                if ($urlPage != "?") $urlPage .= "&";
                $urlPage .= http_build_query(array("keyword" => $keyword));
            }
            if (!empty($type_id)) {
                if ($urlPage != "?") $urlPage .= "&";
                $urlPage .= http_build_query(array("slType" => $type_id));
            }
            if($check) {
                if ($urlPage != '?') $urlPage .= '&';
                $urlPage .= 'page=' . $current_page;
                $this->response->redirect($this->lang_url.'/'.$parent_keyword . $urlPage . '');
                return;
            }
            if ($urlPage != "?") $urlPage .= "&";
            $urlPage.= 'page=(:num)';
            $paginator = new \Paginator($totalItems, $itemsPerPage, $current_page, $urlPage);
            $this->session->set('total_search', $paginator->getNumPages());

            if($this->isMobile){
                $paginator->setMaxPagesToShow(3);
            }else{
                $paginator->setMaxPagesToShow(6);
            }
            $repoBanner = new Banner();
            $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
            $this->view->setVars(array(
                'select_type' => $select_type,
                'htmlPaginator' => $paginator->toHtmlFrontend(),
                'list_search' => $list_search,
                'totalItems' => $totalItems,
                'parent_keyword' => $parent_keyword,
                'keyword' => $keyword,
                'banners'  => $banners,
            ));
        }
    }

    public function suggestAction()
    {
        $this->view->disable();
        $searchword =$this->request->get("searchword");
        if(strlen($searchword) > 2) {
            $arrsuggest = Suggest::search($searchword, 10,$this->lang_code);
            echo json_encode($arrsuggest);
        }else {
            echo "";
        }
    }
}