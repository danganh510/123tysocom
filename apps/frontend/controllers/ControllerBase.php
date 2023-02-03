<?php

namespace Bincg\Frontend\Controllers;
use Bincg\Models\BinLanguage;
use Bincg\Models\BinLocation;
use Bincg\Models\BinNewspaper;
use Bincg\Repositories\Article;
use Bincg\Repositories\CommunicationChannel;
use Bincg\Repositories\Config;
use Bincg\Repositories\Device;
use Bincg\Repositories\Language;
use Bincg\Repositories\Location;
use Bincg\Repositories\Type;
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
    protected $location_code;
    protected $lang_code;
    protected $lang_url;
    protected $lang_url_slashed;
    /** @var BinLanguage[] $languages */
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
        $locationCode = $this->dispatcher->getParam('location');
        $languageCode = $this->dispatcher->getParam('language');
        $defaultLocationCode = $this->globalVariable->defaultLocation;
        $defaultLanguageCode = $this->globalVariable->defaultLanguage;
        $redirectUrl = substr($this->request->getURI(), strlen($this->url->getBaseUri()));
        if(strpos($redirectUrl,'fbclid')!==false) {
            $redirectUrl = '';
        }
        $redirectUrlArrayDetect = array('cron/update-sitemap?ctoken='.$this->globalVariable->cronToken);
        //Homepage - detect ip
        if (!$this->session->has('ssIpInfo') || !isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') {
            $ipInfo = $this->my->ssIpInfo();
            if ($ipInfo != null) {
                if ($locationCode == null && $languageCode == null && $redirectUrl == '') {
                    if ($ipInfo->countryCode != $defaultLocationCode) {
                        $location = BinLocation::findFirstByCountryCode($ipInfo->countryCode);
                        if ($location) {
                            $this->view->disable();
                            $this->response->redirect($location->getLocationCountryCode() . '/' . $location->getLocationLangCode());
                            return;
                        }
                    }
                    $locationCode = $defaultLocationCode;
                    $languageCode = $defaultLanguageCode;
                    $this->view->disable();
                    $this->response->redirect($locationCode . '/' . $languageCode);
                    return;
                }
            }
        }
        if($this->session->has('ssIpInfo')) {
            $ip_info = unserialize($this->session->get('ssIpInfo'));
            $countryCode = $ip_info->countryCode;
            $this->view->ipCountryCode = $this->ipCountryCode = $countryCode;
        } else {
            $this->view->ipCountryCode = $this->ipCountryCode = '';
        }
        if ($languageCode == null) {
            if ($locationCode != null) {
                //if url has param location but not param lang => if param('location') in location table
                $location = BinLocation::findFirstByCountryCode($locationCode);
                if ($location) {
                    $this->view->disable();
                    $this->response->redirect($location->getLocationCountryCode().'/'.$location->getLocationLangCode());
                    return;
                }else{
                    $this->view->disable();
                    $this->response->redirect($defaultLocationCode.'/'.$defaultLanguageCode);
                    return;
                }
            }
            else {
                $locationCode = $defaultLocationCode;
                $languageCode = $defaultLanguageCode;
                $location = BinLocation::findFirstByCountryCode($this->ipCountryCode);
                if(in_array($redirectUrl,$redirectUrlArrayDetect)){
                    if($location){
                        $this->response->redirect($location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/'.$redirectUrl);
                    }else{
                        if($redirectUrl==''){
                            $this->response->redirect($defaultLocationCode.'/'.$defaultLanguageCode);
                        }else{
                            $this->response->redirect($defaultLocationCode.'/'.$defaultLanguageCode.'/'.$redirectUrl);
                        }
                    }
                }
                elseif($redirectUrl != ''){
                    if($location){
                        $this->response->redirect($location->getLocationCountryCode().'/'.$location->getLocationLangCode().'/notfound');
                    }else{
                        $this->response->redirect($defaultLocationCode.'/'.$defaultLanguageCode.'/notfound');
                    }
                }
            }
        }
        else {
            if ($locationCode == null) {
                $locationCode = $defaultLocationCode;
            }
            if ($locationCode != $defaultLocationCode) {
                $location = BinLocation::findFirstByCountryCodeAndLang($locationCode, $languageCode);
                if (!$location) {
                    //Not found
                    $this->view->disable();
                    if ($locationCode != $defaultLocationCode) {
                        $location = BinLocation::findFirstByCountryCode($locationCode);
                        if ($location) {
                            $this->response->redirect($location->getLocationCountryCode() . '/' . $location->getLocationLangCode() . '/notfound');
                            return;
                        }
                    }
                    $this->response->redirect($defaultLocationCode . '/' . $defaultLanguageCode . '/notfound');
                    return;
                }
            }
        }
        $this->view->location_code = $this->location_code = $locationCode;
        $this->view->lang_code = $this->lang_code = $languageCode;
        $this->view->lang_url = $this->lang_url = $locationCode.'/'.$languageCode;
        $this->view->lang_url_slashed = $this->lang_url_slashed = '/'.$locationCode.'/'.$languageCode;
        $languageRepo = new Language();
        $this->view->languages = $this->languages = $languageRepo->getActiveLanguagesByLocation($locationCode);
        $current_url = str_replace($locationCode.'/'.$languageCode.'/', '', $this->request->getURI());
        $current_url = str_replace($locationCode.'/'.$languageCode, '',$current_url);
        if ($current_url == '/')  {
            $current_url = '';
        }
        $this->view->current_url = $current_url;

        $lang_country_code = BinLanguage::getCountryByCode($this->lang_code);
        $lang_country_code = (!empty($lang_country_code))?$lang_country_code:$this->globalVariable->defaultCountry;
        $str_time_language =  $this->lang_code.'_'.strtoupper($lang_country_code).'.UTF-8';//vi_VN.UTF8
        setlocale (LC_TIME, $str_time_language);
        Config::getCache($this->lang_code);
        $this->view->locationByLoAndLa = $this->locationByLoAndLa = Location::findFirstByCountryCodeAndLang($this->location_code,$this->lang_code);
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
        $communicationChannel = $this->request->getPost('communication_chanel', array('string', 'trim'));
        $selectCommunicationChannel = CommunicationChannel::getCommunicationChannel($communicationChannel,$this->lang_code);
        $this->view->selectCommunicationChannel = $selectCommunicationChannel;
        $footerNewspapers = BinNewspaper::findAllNewspaper(6);
        $this->view->footerNewspapers = $footerNewspapers;
    }
}
