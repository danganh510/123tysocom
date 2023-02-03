<?php

namespace Bincg\Repositories;

use Bincg\Google\GoogleTranslate;
use Bincg\Models\BinArticleLang;
use Bincg\Models\BinConfig;
use Bincg\Models\BinPageLang;
use Bincg\Models\BinTypeLang;
use Phalcon\Mvc\User\Component;

class Cron extends Component
{
    public function Article($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = "''";
        }
        $sql_ar = "Select * From Bincg\Models\BinArticle  Where article_active = 'Y' AND article_id NOT IN ($string_data) limit 3 ";
        $list_article = $this->modelsManager->executeQuery($sql_ar);
        //translate Article
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_article);
        $googleTranslate = new GoogleTranslate();
        $message_error = "";
        /**
         * @var \Bincg\Models\BinArticle[] $list_article
         */
        foreach ($list_article as $list) {
            $id = $list->getArticleId();
            $name = $list->getArticleName();
            $title = $list->getArticleTitle();
            $keyword = $list->getArticleMetaKeyword();
            $des = $list->getArticleMetaDescription();
            $content = $list->getArticleContent();
            $summary = $list->getArticleSummary();

            $number_lang = strlen($content) > strlen($summary)?strlen($content):strlen($summary);
            $is_check = true;
            // caculator time delay
            $delay_time = $this->process_delay_time($number_lang);
            $message_error .= "=========".$id."-".$name."==========<br>";
            $article_name = $googleTranslate->translate($name, $code_lang);

            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep(0);
            }
            $article_title = $googleTranslate->translate($title, $code_lang);
            if ($article_title["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Title =" . $article_title["errorcode"] . " - " . $article_title["errormessage"] . $article_title["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep(0);
            }
            $article_keyword = $googleTranslate->translate($keyword, $code_lang);
            if ($article_keyword["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= "Keyword =" . $article_keyword["errorcode"] . " - " . $article_keyword["errormessage"] . $article_keyword["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep(0);
            }
            $article_des = $googleTranslate->translate($des, $code_lang);
            if ($article_des["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_des["errorcode"] . " - " . $article_des["errormessage"] . $article_des["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_content = $googleTranslate->translate($content, $code_lang);
            if ($article_content["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Content  =" . $article_content["errorcode"] . " - " . $article_content["errormessage"] . $article_content["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_summary = $googleTranslate->translate($summary, $code_lang);
            if ($article_summary["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Summary  =" . $article_summary["errorcode"] . " - " . $article_summary["errormessage"] . $article_summary["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);

            }
            if ($is_check) {
                ArticleLang::deleteByIdAndLang($id, $code_lang);
                $article_lang = new BinArticleLang();
                $article_lang->setArticleId($id);
                $article_lang->setArticleLangCode($code_lang);
                $article_lang->setArticleName($article_name["data"]);
                $article_lang->setArticleTitle($article_title["data"]);
                $article_lang->setArticleMetaKeyword($article_keyword["data"]);
                $article_lang->setArticleMetaDescription($article_des["data"]);
                $article_lang->setArticleContent($article_content["data"]);
                $article_lang->setArticleSummary($article_summary["data"]);
                $save = $article_lang->save();
                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }

        return $result;
    }
    public function Type($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = "''";
        }
        $sql_type = "Select * From Bincg\Models\BinType  Where type_active = 'Y' AND type_id NOT IN ($string_data) limit 5";
        $list_type = $this->modelsManager->executeQuery($sql_type);
        //translate type
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_type);
        $googleTranslate = new GoogleTranslate();
        $message_error="";
        /**
         * @var \Bincg\Models\BinType[] $list_type
         */
        foreach ($list_type as $list) {
            $id = $list->getTypeId();
            $name = $list->getTypeName();
            $title = $list->getTypeTitle();
            $keyword = $list->getTypeMetaKeyword();
            $des = $list->getTypeMetaDescription();
            if ($name == NULL) {
                $name = "";
            }
            if ($title == NULL)
            {
                $title = "";
            }
            if($keyword == NULL)
            {
                $keyword = "";
            }
            if ($des == NULL)
            {
                $des = "";
            }
            $is_check = true;
            $message_error = "=========".$id."========";
            $sum_lang = $name . $title . $keyword . $des ;
            $strlang = strlen($sum_lang);

            // caculator time delay
            $delay_time = 0;
            $article_name = $googleTranslate->translate($name, $code_lang);
            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep($delay_time);
            }

            $article_title = $googleTranslate->translate($title, $code_lang);
            if ($article_title["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Title =" . $article_title["errorcode"] . " - " . $article_title["errormessage"] . $article_title["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);

            }
            $article_keyword = $googleTranslate->translate($keyword, $code_lang);
            if ($article_keyword["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Keyword =" . $article_keyword["errorcode"] . " - " . $article_keyword["errormessage"] . $article_keyword["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }

            $article_des = $googleTranslate->translate($des, $code_lang);
            if ($article_des["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_des["errorcode"] . " - " . $article_des["errormessage"] . $article_des["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }

            if ($is_check) {
                TypeLang::deleteByIdAndLang($id, $code_lang);
                $article_lang = new BinTypeLang();
                $article_lang->setTypeId($id);
                $article_lang->setTypeLangCode($code_lang);
                $article_lang->setTypeName($article_name["data"]);
                $article_lang->setTypeTitle($article_title["data"]);
                $article_lang->setTypeMetaKeyword($article_keyword["data"]);
                $article_lang->setTypeMetaDescription($article_des["data"]);
                $save = $article_lang->save();

                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }

        return $result;
    }
    public function Page($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = "''";
        }
        $sql_type = "Select * From Bincg\Models\BinPage  Where page_id NOT IN ($string_data) limit 5";
        $list_type = $this->modelsManager->executeQuery($sql_type);
        //translate type
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_type);
        $googleTranslate = new GoogleTranslate();
        $message_error="";
        /**
         * @var \Bincg\Models\BinPage[] $list_type
         */
        foreach ($list_type as $list) {
            $id = $list->getPageId();
            $name = $list->getPageName();
            $title = $list->getPageTitle();
            $keyword = $list->getPageMetaKeyword();
            $des = $list->getPageMetaDescription();
            $content = $list->getPageContent();
            $style = $list->getPageStyle();
            if ($name == NULL) {
                $name = "";
            }
            if ($title == NULL)
            {
                $title = "";
            }
            if($keyword == NULL)
            {
                $keyword = "";
            }
            if ($des == NULL)
            {
                $des = "";
            }
            if ($content == NULL)
            {
                $content = "";
            }
            if ($style == NULL)
            {
                $style = "";
            }
            $is_check = true;
            $message_error = "=========".$id."========";
            $sum_lang = $name . $title . $keyword . $des ;
            $strlang = strlen($sum_lang);
            // caculator time delay
            $delay_time = 0;
            $article_name = $googleTranslate->translate($name, $code_lang);
            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep($delay_time);
            }
            $article_title = $googleTranslate->translate($title, $code_lang);
            if ($article_title["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Title =" . $article_title["errorcode"] . " - " . $article_title["errormessage"] . $article_title["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);

            }
            $article_keyword = $googleTranslate->translate($keyword, $code_lang);
            if ($article_keyword["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Keyword =" . $article_keyword["errorcode"] . " - " . $article_keyword["errormessage"] . $article_keyword["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_des = $googleTranslate->translate($des, $code_lang);
            if ($article_des["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_des["errorcode"] . " - " . $article_des["errormessage"] . $article_des["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_style = $googleTranslate->translate($style, $code_lang);
            if ($article_style["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_style["errorcode"] . " - " . $article_style["errormessage"] . $article_style["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_content = $googleTranslate->translate($content, $code_lang);
            if ($article_content["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_content["errorcode"] . " - " . $article_content["errormessage"] . $article_content["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }

            if ($is_check) {
                PageLang::deleteByIdAndLang($id, $code_lang);
                $article_lang = new BinPageLang();
                $article_lang->setPageId($id);
                $article_lang->setPageLangCode($code_lang);
                $article_lang->setPageName($article_name["data"]);
                $article_lang->setPageTitle($article_title["data"]);
                $article_lang->setPageMetaKeyword($article_keyword["data"]);
                $article_lang->setPageMetaDescription($article_des["data"]);
                $save = $article_lang->save();
                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }
        return $result;
    }
    public function Config($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = array();
        }
        $arr_data = explode(",",$string_data);
        $sql_type = "Select * From Bincg\Models\BinConfig  Where config_key NOT IN ( '" . implode( "', '" , $arr_data ) . "' )  And config_language = 'en' limit 15";
        $list_type = $this->modelsManager->executeQuery($sql_type);

        //translate type
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_type);
        $googleTranslate = new GoogleTranslate();
        $message_error="";
        /**
         * @var \Bincg\Models\BinConfig[] $list_type
         */
        foreach ($list_type as $list) {
            $id = $list->getConfigKey();
            $name = $list->getConfigContent();
            if ($name == NULL) {
                $name = "";
            }
            $is_check = true;
            $message_error = "=========".$id."========";
            $sum_lang = $name;

            // caculator time delay
            $delay_time = 0;
            $article_name = $googleTranslate->translate($name,$code_lang);
            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep($delay_time);
            }
            if ($is_check) {
                Config::deleteByIdAndLang($id,$code_lang);
                $article_lang = new BinConfig();
                $article_lang->setConfigKey($id);
                $article_lang->setConfigLanguage($code_lang);
                $article_lang->setConfigContent($article_name["data"]);
                $save = $article_lang->save();
                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }
        Config::deleteCache();
        return $result;
    }
    public function Album($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = "''";
        }
        $sql_type = "Select * From Bincg\Models\BinAlbum  Where album_id NOT IN ($string_data) limit 5";
        $list_type = $this->modelsManager->executeQuery($sql_type);
        //translate type
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_type);
        $googleTranslate = new GoogleTranslate();
        $message_error="";
        /**
         * @var \Bincg\Models\BinAlbum[] $list_type
         */
        foreach ($list_type as $list) {
            $id = $list->getAlbumId();
            $name = $list->getAlbumName();
            $title = $list->get();
            $keyword = $list->getPageMetaKeyword();
            $des = $list->getPageMetaDescription();
            if ($name == NULL) {
                $name = "";
            }
            if ($title == NULL)
            {
                $title = "";
            }
            if($keyword == NULL)
            {
                $keyword = "";
            }
            if ($des == NULL)
            {
                $des = "";
            }
            $is_check = true;
            $message_error = "=========".$id."========";
            $sum_lang = $name . $title . $keyword . $des ;
            $strlang = strlen($sum_lang);
            // caculator time delay
            $delay_time = 0;
            $article_name = $googleTranslate->translate($name, $code_lang);
            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep($delay_time);
            }
            $article_title = $googleTranslate->translate($title, $code_lang);
            if ($article_title["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Title =" . $article_title["errorcode"] . " - " . $article_title["errormessage"] . $article_title["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);

            }
            $article_keyword = $googleTranslate->translate($keyword, $code_lang);
            if ($article_keyword["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Keyword =" . $article_keyword["errorcode"] . " - " . $article_keyword["errormessage"] . $article_keyword["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            $article_des = $googleTranslate->translate($des, $code_lang);
            if ($article_des["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $message_error .= " Des  =" . $article_des["errorcode"] . " - " . $article_des["errormessage"] . $article_des["responsecode"] . "<br>";
                $is_check = false;
            } else {
                sleep($delay_time);
            }
            if ($is_check) {
                PageLang::deleteByIdAndLang($id, $code_lang);
                $article_lang = new BinPageLang();
                $article_lang->setPageId($id);
                $article_lang->setPageLangCode($code_lang);
                $article_lang->setPageName($article_name["data"]);
                $article_lang->setPageTitle($article_title["data"]);
                $article_lang->setPageMetaKeyword($article_keyword["data"]);
                $article_lang->setPageMetaDescription($article_des["data"]);
                $save = $article_lang->save();
                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }
        return $result;
    }
    public function Country($string_data,$code_lang)
    {
        if (empty($string_data)) {
            $string_data = array();
        }
        $arr_data = explode(",",$string_data);
        $sql_type = "Select * From General\Models\Country  Where country_iso_alpha2 NOT IN ( '" . implode( "', '" , $arr_data ) . "' )  And country_active = 'Y' limit 15";
        $list_type = $this->modelsManager->executeQuery($sql_type);
        //translate type
        $article_success = array();
        $total_error = 0;
        $total_ar = count($list_type);
        $message_error="";
        /**
         * @var \General\Models\Country[] $list_type
         */
        foreach ($list_type as $list) {
            $id = $list->getCountryIsoAlpha2();
            $name = $list->getCountryName();

            if ($name == NULL) {
                $name = "";
            }
            $is_check = true;
            $message_error = "=========".$id."========";
            $sum_lang = $name;
            $strlang = strlen($sum_lang);
            // caculator time delay
            $delay_time = 0;
            $article_name = GoogleTranslate::translate($name,$code_lang);
            if ($article_name["status"] == "fail") {
                if ($is_check) {
                    $total_error++;
                }
                $is_check = false;
                $message_error .= " Name =" . $article_name["errorcode"] . " - " . $article_name["errormessage"] . $article_name["responsecode"] . "<br>";
            } else {
                sleep($delay_time);
            }
            if ($is_check) {
                CountryLangRepo::deleteByIdAndLang($id,$code_lang);
                $article_lang = new CountryLang();
                $article_lang->setCountryIsoAlpha2($id);
                $article_lang->setCountryLangCode($code_lang);
                $article_lang->setCountryName($article_name["data"]);
                $save = $article_lang->save();
                if ($save) {
                    $article_success[] = $id;
                }
            }
        }
        $result = array();
        if($total_error == $total_ar && $total_ar > 0)
        {
            $result['status']="error";
            $result['message']= $message_error;
        }
        else
        {
            $result['status']="success";
            $result['message']= $article_success;
        }
        return $result;
    }

    public function process_delay_time($string_lenght)
    {
        $result = 0;
        if($string_lenght >= 25000)
        {
            $result = 6;
        }
        else if (15000 <= $string_lenght && $string_lenght < 25000)
        {
            $result = 5;
        }
        else if (10000 <= $string_lenght && $string_lenght < 15000 )
        {
            $result = 2;

        }
        else if ($string_lenght < 10000 )
        {
            $result = 1;
        }
        return  $result ;
    }
}




