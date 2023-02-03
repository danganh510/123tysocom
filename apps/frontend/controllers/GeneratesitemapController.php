<?php

namespace Bincg\Frontend\Controllers;

use Bincg\Repositories\Article;
use Bincg\Repositories\Career;
use Bincg\Repositories\Location;
use Bincg\Repositories\Type;

class GeneratesitemapController extends ControllerBase
{
    const SITEMAP_URL_SITE  = 'https://www.bincg.com';
    const CHANGEFREQ = 'daily';
    const PRIORITY_DEFAULT = '0.5120';
    const FILE_RESULT = "sitemap.xml";
    public function indexAction()
    {
        $this->view->disable();
        $token = $this->request->get('ctoken');
        if($token!=$this->globalVariable->cronToken){ echo "Invalid token!"; return;}
        $result = array();
        $repoLocation = new Location();
        $listLocation = $repoLocation->findAllLocationDuplicate();
        $result = $this->getRouterIndex($result,$listLocation);
        $result = $this->getRouterServices($result,$listLocation);
        $result = $this->getRouterAboutus($result,$listLocation);
        $result = $this->getRouterContactus($result,$listLocation);
        $result = $this->getRouterCorporatesocialresponsibility($result,$listLocation);
        $result = $this->getRouterCareers($result,$listLocation);
        $result = $this->getRouterNews($result,$listLocation);
        $result = $this->getRouterSearch($result,$listLocation);


        $br = '';
        $header_sitemap = '<?xml version="1.0" encoding="UTF-8"?>'.$br.
            '<urlset xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd http://www.w3.org/TR/xhtml11/xhtml11_schema.html http://www.w3.org/2002/08/xhtml/xhtml1-strict.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/TR/xhtml11/xhtml11_schema.html">'.$br;
        $footer_sitemap = '</urlset>';
        $time = date('c',$this->my->localTime(time()));
        $sitemapFileName = self::FILE_RESULT;
        $text_result = $header_sitemap;
        foreach($result as $item) {
            $text_result .= str_replace('|||LASTMOD|||',$time,$item);
        }
        $text_result .= $footer_sitemap;
        file_put_contents($sitemapFileName, $text_result);
    }
    private function replaceTxtUrl($url,$priority=self::PRIORITY_DEFAULT){
        $br = '';
        $tab = '';
        $txt_item_url = '<url>'.$br.$tab.'<loc>'.self::SITEMAP_URL_SITE.'/'.$url.'</loc>'.$br.$tab.'<lastmod>|||LASTMOD|||</lastmod>'.$br.$tab.'<changefreq>'.self::CHANGEFREQ.'</changefreq>'.$br.$tab.'<priority>'.$priority.'</priority>'.$br.'</url>'.$br;
        return $txt_item_url;
    }
    private function replaceTxtUrlHasXhtml($url,$hreflang,$priority=self::PRIORITY_DEFAULT){
        $br = '';
        $tab = '';
        $txt_item_url = '<url>'.$br.$tab.'
            <loc>'.self::SITEMAP_URL_SITE.'/'.$url.'</loc>'
            .$br.$tab.
            '<xhtml:link rel="alternate" hreflang="'.$hreflang.'" href="'.self::SITEMAP_URL_SITE.'/'.$url.'"/>'.$br.$tab.'
            <lastmod>|||LASTMOD|||</lastmod>'
            .$br.$tab.
            '<changefreq>'.self::CHANGEFREQ.'</changefreq>'
            .$br.$tab.
            '<priority>'.$priority.'</priority>'
            .$br.'</url>'
            .$br;
        return $txt_item_url;
    }
    private function getRouterByArray($result,$arr_controller,$listLocation,$priority=self::PRIORITY_DEFAULT){
        foreach ($arr_controller as $controller){
            $url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/'.$controller;
            array_push($result, $this->replaceTxtUrl($url,$priority));
        }
        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                foreach ($arr_controller as $controller){
                    $url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/'.$controller;
                    array_push($result, $this->replaceTxtUrlHasXhtml($url,$location->getLocationLangCode(),$priority));
                }
            }
        }
        return $result;
    }
    //1.0000 and 0.8000
    private function getRouterIndex($result,$listLocation){
        $url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage;
        $domain_url_site = $this->replaceTxtUrl($url,'1.0000');
        $domain_url_site = str_replace('/</loc>','</loc>',$domain_url_site);
        array_push($result, $domain_url_site);
        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                $url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode();
                array_push($result, $this->replaceTxtUrlHasXhtml($url,$location->getLocationLangCode(),'0.8000'));
            }
        }
        return $result;
    }
    private function getRouterServices($result,$listLocation){
        $priority = '0.8000';
        $arr_controller = array(
            'services'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation,$priority);
        $repoArticle = new Article();
        $articles = $repoArticle->getByTypeAndOrder($this->globalVariable->typeServicesId,$this->globalVariable->defaultLanguage);
        foreach ($articles as $item){
            $item_url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/services/'.$item->getArticleKeyword();
            array_push($result, $this->replaceTxtUrl($item_url,$priority));
        }
        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                $articles = $repoArticle->getByTypeAndOrder($this->globalVariable->typeServicesId,$location->getLocationLangCode());
                foreach ($articles as $item){
                    $item_url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/services/'.$item->getArticleKeyword();
                    array_push($result, $this->replaceTxtUrlHasXhtml($item_url,$location->getLocationLangCode(),$priority));
                }
            }
        }
        return $result;
    }
    private function getRouterContactus($result,$listLocation){
        $priority = '0.7200';
        $arr_controller = array(
            'contact-us'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation,$priority);
        return $result;
    }

    private function getRouterSearch($result,$listLocation){
        $arr_controller = array(
            'search'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation);
        return $result;
    }
    private function getRouterCareers($result,$listLocation){
        $priority = '0.6400';
        $arr_controller = array(
            'careers'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation,$priority);
        $repoArticle = new Article();
        $articles = $repoArticle->getByTypeAndOrder($this->globalVariable->typeCareersId,$this->globalVariable->defaultLanguage);
        foreach ($articles as $item){
            $item_url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/careers/'.$item->getArticleKeyword();
            array_push($result, $this->replaceTxtUrl($item_url,$priority));
        }

        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                $articles = $repoArticle->getByTypeAndOrder($this->globalVariable->typeCareersId,$location->getLocationLangCode());
                foreach ($articles as $item){
                    $item_url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/careers/'.$item->getArticleKeyword();
                    array_push($result, $this->replaceTxtUrlHasXhtml($item_url,$location->getLocationLangCode(),$priority));
                }
            }
        }

        $repoCareer = new Career();
        $careers = $repoCareer->getAllByInsertTime($this->globalVariable->defaultLanguage);
        foreach($careers as $career) {
            $item_url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/careers/'.$career->getCareerKeyword();
            array_push($result, $this->replaceTxtUrl($item_url,$priority));
        }

        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                $careers = $repoCareer->getAllByInsertTime($location->getLocationLangCode());
                foreach ($careers as $item){
                    $item_url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/careers/'.$item->getCareerKeyword();
                    array_push($result, $this->replaceTxtUrlHasXhtml($item_url,$location->getLocationLangCode(),$priority));
                }
            }
        }


        return $result;
    }

    private function getRouterAboutus($result,$listLocation){
        $priority = '0.8000';
        $arr_controller = array(
            'about-us'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation,$priority);
        return $result;
    }
    private function getRouterCorporatesocialresponsibility($result,$listLocation){
        $priority = '0.6400';
        $arr_controller = array(
            'corporate-social-responsibility'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation,$priority);
        return $result;
    }
    //0.5120
    private function getRouterNews($result,$listLocation){
        $arr_controller = array(
            'news'
        );
        $result = $this->getRouterByArray($result,$arr_controller,$listLocation);
        $repoType = new Type();
        $repoArticle = new Article();
        $types = $repoType->getTypeByParent($this->globalVariable->typeNewsId,$this->globalVariable->defaultLanguage);

        foreach ($types as $type){
            $item_url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/news/'.$type->getTypeKeyword();
            array_push($result, $this->replaceTxtUrl($item_url));
            $articles = $repoArticle->getByTypeAndInserttime($type->getTypeId(),$this->globalVariable->defaultLanguage);
            foreach ($articles as $item){
                $item_url = $this->globalVariable->defaultLocation.'/'.$this->globalVariable->defaultLanguage.'/news/'.$item->getArticleKeyword();
                array_push($result, $this->replaceTxtUrl($item_url));
            }
        }

        foreach ($listLocation as $location){
            if($location->getLocationCountryCode() != $this->globalVariable->defaultLocation){
                $types = $repoType->getTypeByParent($this->globalVariable->typeNewsId,$location->getLocationLangCode());
                foreach ($types as $type){
                    $item_url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/news/'.$type->getTypeKeyword();
                    array_push($result, $this->replaceTxtUrlHasXhtml($item_url,$location->getLocationLangCode()));
                    $articles = $repoArticle->getByTypeAndInserttime($type->getTypeId(),$location->getLocationLangCode());
                    foreach ($articles as $item){
                        $item_url = $location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/news/'.$item->getArticleKeyword();
                        array_push($result, $this->replaceTxtUrlHasXhtml($item_url,$location->getLocationLangCode()));
                    }
                }
            }
        }
        return $result;
    }
}