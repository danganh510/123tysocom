<?php

namespace Score\Repositories;

use Phalcon\Mvc\User\Component;

/**
 * Class SearchAzure
 * @property \GlobalVariable globalVariable
 * @package Score\Repositories
 */
class SearchAzure extends Component
{
    const AR_URL  = 'ar_url';
    const AR_TYPE  = 'ar_type_id';
    const AR_NAME  = 'ar_name';
    const AR_TITLE  = 'ar_title';
    const AR_META_KEYWORD = 'ar_meta_keyword';
    const AR_META_DESCRIPTION = 'ar_meta_description';
    const AR_CONTENT = 'ar_content';
    const AR_KEY_ID = 'ar_key_id';
    const LANG = 'lang';
    const CONTENT_LIMIT = 32766;
    function __construct()  {
        set_time_limit(0);
        ini_set('memory_limit', -1);
    }

    /**
     * @param object $ar
     * @return string
     */
    public function getUrl($ar)
    {
        $globalVariable = $this->globalVariable;
        $url = '';
        switch ($ar->article_type_id)
        {
            case $globalVariable->typeInformationId:
                $url = '/info/';
                break;
            case $globalVariable->typeAboutUsId:
                $url = '/about-us#';
                break;
            case $globalVariable->typeServicesId:
                $url = '/services/';
                break;
            case $globalVariable->typeCorporateSocialResponsibilityId:
                $url = '/';
                break;
            case $globalVariable->typeNewsId:
                $url = '/news/';
                break;
            case $globalVariable->typeInternalNewsId:
                $url = '/news/';
                break;
            case $globalVariable->typePressCornerNewsId:
                $url = '/news/';
                break;
            case $globalVariable->typePublicHolidaysId:
                $url = '/news/';
                break;
            case $globalVariable->typeEconomyId:
                $url = '/news/';
                break;
            case $globalVariable->typeInvestingId:
                $url = '/news/';
                break;
            case $globalVariable->typeMediaMarketingId:
                $url = '/news/';
                break;
            case $globalVariable->typeTravelsId:
                $url = '/news/';
                break;
            case $globalVariable->typePulicationsId:
                $url = '/news/';
                break;
        }
        $url .= $ar->article_keyword;
        return $url;
    }

    public function getCache(){
        $arArray = array();
        $list_language = Language::findAllLanguageCodes();
        $listArticle = array();
        foreach ($list_language as $lang){
            $arr_para = array();
            if($lang == $this->globalVariable->defaultLanguage){
                $sql = "SELECT s.article_id, s.article_type_id, s.article_name, s.article_title, s.article_keyword, s.article_meta_keyword, s.article_meta_description,
                        s.article_content
                        FROM Score\Models\ScArticle s
                        INNER JOIN Score\Models\ScType t ON t.type_id = s.article_type_id AND t.type_active = 'Y'
                        WHERE s.article_active = 'Y'
                        ORDER BY s.article_insert_time DESC";
            }else{
                $arr_para = array("LANG" => $lang);
                $sql = "SELECT sl.article_id, s.article_type_id,sl.article_name, sl.article_title, s.article_keyword, sl.article_meta_keyword, sl.article_meta_description,
                        sl.article_content
                        FROM Score\Models\ScArticle s
                        INNER JOIN Score\Models\ScType t ON t.type_id = s.article_type_id AND t.type_active = 'Y'
                        INNER JOIN Score\Models\ScArticleLang sl ON s.article_id = sl.article_id AND sl.article_lang_code =:LANG:
                        WHERE s.article_active = 'Y'
                        ORDER BY s.article_insert_time DESC";
            }
            $listArticle[$lang] = $this->modelsManager->executeQuery($sql,$arr_para);
        }
        foreach($listArticle as $lang => $items) {
            foreach ($items as $ar){
                $url = $this->getUrl($ar);
                $item = array();
                $item[self::AR_TYPE] = trim($ar->article_type_id);
                $item[self::AR_URL] = $url;
                $item[self::AR_NAME] = trim($ar->article_name);
                $item[self::AR_TITLE] = trim($ar->article_title);
                $item[self::AR_META_KEYWORD] = trim($ar->article_meta_keyword);
                $item[self::AR_META_DESCRIPTION] = trim($ar->article_meta_description);
                $item[self::AR_CONTENT] = self::substrContent(trim(strip_tags($ar->article_content)));
                $item[self::LANG] = $lang;
                $item[self::AR_KEY_ID] = $lang.'_'.trim($ar->article_id);
                $arArray[$lang][] = $item;
            }
        }
        $listCareersPosition = array();
        foreach ($list_language as $lang){
            $arr_para = array();
            if($lang == $this->globalVariable->defaultLanguage){
                $sql = "SELECT c.career_id, c.career_name, c.career_title, c.career_keyword, c.career_meta_keyword, c.career_meta_description, c.career_content
                        FROM Score\Models\ScCareer c
                        WHERE c.career_active = 'Y' AND c.career_expired = 'N' 
                        ORDER BY c.career_order ASC ";
            }else{
                $arr_para = array("LANG" => $lang);
                $sql = "SELECT c.career_id, cl.career_name, cl.career_title, c.career_keyword, cl.career_meta_keyword, cl.career_meta_description, cl.career_content
                        FROM Score\Models\ScCareer c 
                        INNER JOIN Score\Models\ScCareerLang cl ON c.career_id = cl.career_id AND cl.career_lang_code =:LANG:
                        WHERE c.career_active = 'Y' AND c.career_expired = 'N' 
                        ORDER BY c.career_order ASC ";
            }
            $listCareersPosition[$lang] =  $this->modelsManager->executeQuery($sql,$arr_para);
        }
        foreach($listCareersPosition as $lang => $items) {
            foreach ($items as $ar){
                $url = '/careers/'.trim($ar->career_keyword);
                $item = array();
                $item[self::AR_TYPE] = 'career_'.$this->globalVariable->typeCareersId;
                $item[self::AR_URL] = $url;
                $item[self::AR_NAME] = trim($ar->career_name);
                $item[self::AR_TITLE] = trim($ar->career_title);
                $item[self::AR_META_KEYWORD] = trim($ar->career_meta_keyword);
                $item[self::AR_META_DESCRIPTION] = trim($ar->career_meta_description);
                $item[self::AR_CONTENT] = self::substrContent(trim(strip_tags($ar->career_content)));
                $item[self::LANG] = $lang;
                $item[self::AR_KEY_ID] = 'careerPosition_'.$lang.'_'.trim($ar->career_id);
                $arArray[$lang][] = $item;
            }
        }
        return $arArray;
    }

    public static function replaceword($words){
        while (strpos($words, '  ') !== false) {
            $words = str_replace('  ', ' ', $words);
        }
        return $words;
    }

    public static function replaceUrl($url){
        return (stripos($url,'http') !== false)?$url:$_SERVER['HTTP_HOST'].$url;
    }

    public static function _substr($_str, $length, $minword = 3)
    {
        $str = self::replaceword($_str);
        $sub = '';
        $len = 0;
        foreach (explode(' ', $str) as $word)
        {
            $part = (($sub != '') ? ' ' : '') . $word;
            $sub .= $part;
            $len += strlen($part);
            if (strlen($word) > $minword && strlen($sub) >= $length)
            {
                break;
            }
        }
        return $sub . (($len < strlen($str)) ? '...' : '');
    }

    public static function substrContent($value='')
    {
        $result = $value;
        if(strlen($value) > self::CONTENT_LIMIT) {
            $result = substr($value,0,self::CONTENT_LIMIT);
        }
        return $result;
    }
}



