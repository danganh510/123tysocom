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
        $page->AutoGenMetaPage("index",defined('txt_brand_name') ? txt_brand_name : '',$this->lang_code);
        $page->generateStylePage('index');
        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
    
        $listMatch = file_get_contents("http://crawler-score.abc/get-list-match?time=live");
        $listMatch = json_decode($listMatch,true);
 

        $this->view->setVars([
            'banners'           => $banners,
            'listMatch'  => $listMatch,
            'checkCareer'       => false,
        ]);
    }
}