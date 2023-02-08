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
      //  $this->auth = $this->session->get('auth');
        $detect = new Device();
        $this->view->isMobile = $this->isMobile = $detect->isMobile() || $detect->isTablet();
        $this->view->isMobileOnly = $this->isMobileOnly = $detect->isMobile();
        //Location & Language
        $languageCode = $this->dispatcher->getParam('language');
        $defaultLanguageCode = $this->globalVariable->defaultLanguage;
        $redirectUrl = substr($this->request->getURI(), strlen($this->url->getBaseUri()));
        if(strpos($redirectUrl,'fbclid')!==false) {
            $redirectUrl = '';
        }
        //Homepage - detect ip
        if (!$this->session->has('ssIpInfo') || !isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == '') {
            $ipInfo = $this->my->ssIpInfo();
            if ($ipInfo != null) {
                if ( $languageCode == null && $redirectUrl == '') {
                    $languageCode = $defaultLanguageCode;
                    $this->view->disable();
                    $this->response->redirect( '/' . $languageCode);
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
        Config::getCache($this->lang_code);
 
     
    }
    public function load_view_as_variable($viewPath, $params = array())
    {
        ob_start();
        $this->view->partial($viewPath, $params);
        $view = ob_get_contents();
        ob_end_clean();

        return $view;
    }
}
