<?php

namespace Score\Frontend\Controllers;
use Score\Models\ScLanguage;
use Score\Models\ScNewspaper;
use Score\Repositories\Article;
use Score\Repositories\CommunicationChannel;
use Score\Repositories\Config;
use Score\Repositories\Device;
use Score\Repositories\Language;
use Score\Repositories\Location;
use Score\Repositories\Type;
use Phalcon\Mvc\Controller;
/**
 * @property \GlobalVariable globalVariable
 * @property \My my
 */
class ControllerBase extends Controller
{
	protected $auth;
    protected $currentUrl;
    protected $isMobile;
    protected $isMobileOnly;
    protected $ipCountryCode;

    protected $lang_code;
    protected $lang_url;
    protected $lang_url_slashed;
    /** @var ScLanguage[] $languages */
    protected $languages = array();
    protected $languagesByLocation = array();
    protected $locationByLoAndLa;
	public function initialize()
    {
		//current user
        $this->auth = $this->session->get('auth');
        $detect = new Device();
        $this->view->isMobile = $this->isMobile = $detect->isMobile() || $detect->isTablet();
        $this->view->isMobileOnly = $this->isMobileOnly = $detect->isMobile();
        //Location & Language
        $languageCode = $this->dispatcher->getParam('language');
        $defaultLocationCode = $this->globalVariable->defaultLocation;
        $defaultLanguageCode = $this->globalVariable->defaultLanguage;
        $redirectUrl = substr($this->request->getURI(), strlen($this->url->getBaseUri()));
        if(strpos($redirectUrl,'fbclid')!==false) {
            $redirectUrl = '';
        }
        $redirectUrlArrayDetect = array('cron/update-sitemap?ctoken='.$this->globalVariable->cronToken);
        //Homepage - detect ip
     
        if($this->session->has('ssIpInfo')) {
            $ip_info = unserialize($this->session->get('ssIpInfo'));
            $countryCode = $ip_info->countryCode;
            $this->view->ipCountryCode = $this->ipCountryCode = $countryCode;
        } else {
            $this->view->ipCountryCode = $this->ipCountryCode = '';
        }
      
      
        $this->view->lang_code = $this->lang_code = $languageCode;
        $this->view->lang_url = $languageCode;
        $this->view->lang_url_slashed = $languageCode;
        $languageRepo = new Language();
        $this->view->languages = $this->languages;
        $current_url = str_replace($languageCode.'/', '', $this->request->getURI());
        $current_url = str_replace($languageCode, '',$current_url);
        if ($current_url == '/')  {
            $current_url = '';
        }
        $this->view->current_url = $current_url;

        $lang_country_code = ScLanguage::getCountryByCode($this->lang_code);
        $lang_country_code = (!empty($lang_country_code))?$lang_country_code:$this->globalVariable->defaultCountry;
        $str_time_language =  $this->lang_code.'_'.strtoupper($lang_country_code).'.UTF-8';//vi_VN.UTF8
        setlocale (LC_TIME, $str_time_language);
        Config::getCache($this->lang_code);
        $repoType = new Type();
        $repoArticle = new Article();
        $menu_services = $repoArticle->getByTypeAndOrder($this->globalVariable->typeServicesId,$this->lang_code);
        $menu_abouts = $repoArticle->getByTypeAndOrder($this->globalVariable->typeAboutUsId,$this->lang_code);
        $menu_corporate_social_responsibility = $repoArticle->getByTypeAndOrder($this->globalVariable->typeCorporateSocialResponsibilityId,$this->lang_code);
        $news_subtypes = $repoType->getTypeByParent($this->globalVariable->typeNewsId,$this->lang_code);
        $this->view->menu_services = $menu_services;
        $this->view->menu_abouts = $menu_abouts;
        $this->view->menu_corporate_social_responsibility = $menu_corporate_social_responsibility;
        $this->view->news_subtypes = $news_subtypes;
     
    }
}
