<?php

namespace Score\Frontend\Controllers;

use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\Career;
use Score\Repositories\Page;

class IndexController extends ControllerBase
{
    public function indexAction()
    {
        $page = new Page();
        $page->AutoGenMetaPage("index", defined('txt_brand_name') ? txt_brand_name : '', $this->lang_code);
        $page->generateStylePage('index');
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);

        $listMatch = file_get_contents("http://18.143.100.236:81/get-list-match?time=live");
        $listMatch = json_decode($listMatch, true);
        $viewPath = 'index/tournament';
        $params = array(
            'listMatch' => $listMatch,
        );
        $html_tournament = $this->load_view_as_variable($viewPath, $params);
        if ($this->request->isAjax()) {
            die(json_encode($html_tournament));
        }


        $this->view->setVars([
            'banners'           => $banners,
            'html_tournament'  => $html_tournament,
            'checkCareer'       => false,
        ]);
    }
}
