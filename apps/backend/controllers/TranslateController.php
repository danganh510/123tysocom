<?php

namespace Bincg\Backend\Controllers;

use Bincg\Models\BinAlbumLang;
use Bincg\Models\BinArticleLang;
use Bincg\Models\BinBannerLang;
use Bincg\Models\BinCareerLang;
use Bincg\Models\BinCommunicationChannelLang;
use Bincg\Models\BinConfig;
use Bincg\Models\BinImageLang;
use Bincg\Models\BinOfficeLang;
use Bincg\Models\BinPage;
use Bincg\Models\BinPageLang;
use Bincg\Models\BinTemplateEmailLang;
use Bincg\Models\BinTranslateDetail;
use Bincg\Models\BinTypeLang;
use Bincg\Repositories\Activity;
use Bincg\Repositories\Album;
use Bincg\Repositories\Article;
use Bincg\Repositories\Banner;
use Bincg\Repositories\Career;
use Bincg\Repositories\CommunicationChannel;
use Bincg\Repositories\Config;
use Bincg\Repositories\EmailTemplate;
use Bincg\Repositories\Image;
use Bincg\Repositories\Language;
use Bincg\Google\GoogleTranslate;
use Bincg\Repositories\Office;
use Bincg\Repositories\Page;
use Bincg\Repositories\TranslateDetail;
use Bincg\Repositories\Type;

class TranslateController extends ControllerBase
{
    protected $googleTranslate;

    public function initialize()
    {
        parent::initialize();
        $this->googleTranslate = new GoogleTranslate();
    }

    public function indexAction()
    {

        //$this->googleTranslate->deletelistGlossaries();

        $data['listTableLang'] = array(
            "bin_article_lang",
            "bin_banner_lang",
            "bin_page_lang",
            "bin_communication_channel_lang",
            "bin_type_lang",
            "bin_config",
            "bin_template_email_lang",
            "bin_album_lang",
            "bin_career_lang",
            "bin_image_lang",
            "bin_office_lang"
        );
        $data['slLanguage'] = 'vi';
        /*if(in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', "::1"))){
            $limit = 1;
        }*/
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }

        if ($this->session->has('msg_result_all')) {
            $msg_result_all = $this->session->get('msg_result_all');
            $this->session->remove('msg_result_all');
            $this->view->msg_result_all = $msg_result_all;
        }

        $repoTranslateDetail = new TranslateDetail();
        $this->view->history_translates = $repoTranslateDetail->getAllTranslateDetail();
        $messages = array();

        if ($this->request->isPost()) {
            $repoArticle = new Article();
            $listArticles = $repoArticle->getAllActiveArticleTranslate(3, $data['slLanguage']);
            $repoBanner = new Banner();
            $listBanners = $repoBanner->getAllActiveBannerTranslate(10, $data['slLanguage']);
            $repoPage = new Page();
            $listPages = $repoPage->getAllActivePageTranslate(10, $data['slLanguage']);
            $repoCommunicationChannel = new CommunicationChannel();
            $listCommunicationChannels = $repoCommunicationChannel->getAllActiveCommunicationChannelTranslate(10, $data['slLanguage']);
            $repoType = new Type();
            $listTypes = $repoType->getAllActiveTypeTranslate(10, $data['slLanguage']);
            $repoConfig = new Config();
            $listConfigs = $repoConfig->getAllConfigByCodeTranslate(20,$data['slLanguage']);

            $repoEmailTemplate = new EmailTemplate();
            $listEmailTemplates = $repoEmailTemplate->getAllActiveEmailTemplateTranslate(10,$data['slLanguage']);
            $repoOffice = new Office();
            $listOffices = $repoOffice->getAllActiveOfficeTranslate(10,$data['slLanguage']);
            $repoAlbum = new Album();
            $listAlbums = $repoAlbum->getAllActiveAlbumTranslate(10,$data['slLanguage']);

            $repoCareer = new Career();
            $listCareers = $repoCareer->getAllActiveCareerTranslate(5,$data['slLanguage']);

            $repoImage = new Image();
            $listImages = $repoImage->getAllActiveImageTranslate(5,$data['slLanguage']);
            if (count($listBanners) == 0 && count($listArticles) == 0 && count($listPages) == 0 && count($listCommunicationChannels) == 0 && count($listTypes) == 0 && count($listConfigs) == 0 &&
                count($listEmailTemplates) == 0 && count($listOffices) == 0 && count($listAlbums) == 0 && count($listCareers) == 0 && count($listImages) == 0 ) {
                $msg_result_all = array(
                    'arr_translate_error' => '',
                    'arr_translate_success' => 'Translated all tables successfully'
                );
                $this->session->set('msg_result_all', $msg_result_all);
                die(json_encode(array("success" => 'true')));
            }
            $this->view->disable();
            $limitTime = 40;
            $startTime = microtime(true);
            $checkTimeout = 'false';
            $arr_translate_error = array();
            $arr_translate_success = array();
            $tran_lang_code = $data["slLanguage"];
            $this->googleTranslate->setGlossaryId('en', $tran_lang_code);
            foreach ($data['listTableLang'] as $slcTableLangItem) {

                if (count($listArticles) > 0 && $slcTableLangItem == 'bin_article_lang') {
                    $function_result = $this->translateByTableArticle($slcTableLangItem, $listArticles, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        break;
                    }
                    break;
                }
                if (count($listBanners) > 0 && $slcTableLangItem == 'bin_banner_lang') {
                    $function_result = $this->translateByTableBanner($slcTableLangItem, $listBanners, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }
                if (count($listPages) > 0 && $slcTableLangItem == 'bin_page_lang') {
                    $function_result = $this->translateByTablePage($slcTableLangItem, $listPages, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        break;
                    }
                    break;
                }
                if (count($listCommunicationChannels) > 0 && $slcTableLangItem == 'bin_communication_channel_lang') {
                    $function_result = $this->translateByTableCommunicationChannel($slcTableLangItem, $listCommunicationChannels, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }
                if (count($listTypes) > 0 && $slcTableLangItem == 'bin_type_lang') {
                    $function_result = $this->translateByTableType($slcTableLangItem, $listTypes, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }
                if (count($listConfigs) > 0 && $slcTableLangItem == 'bin_config') {
                    $function_result = $this->translateByTableConfig($slcTableLangItem, $listConfigs, $tran_lang_code, $arr_translate_error, $arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if ($runTime > $limitTime) {
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }

                if(count($listEmailTemplates) > 0 && $slcTableLangItem == 'bin_template_email_lang'){
                    $function_result = $this->translateByTableEmailTemplate($slcTableLangItem,$listEmailTemplates,$tran_lang_code,$arr_translate_error,$arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if($runTime > $limitTime){
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }

                if(count($listOffices) > 0 && $slcTableLangItem == 'bin_office_lang'){
                    $function_result = $this->translateByTableOffice($slcTableLangItem,$listOffices,$tran_lang_code,$arr_translate_error,$arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if($runTime > $limitTime){
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }

                if(count($listAlbums) > 0 && $slcTableLangItem == 'bin_album_lang'){
                    $function_result = $this->translateByTableAlbum($slcTableLangItem,$listAlbums,$tran_lang_code,$arr_translate_error,$arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if($runTime > $limitTime){
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }


                if(count($listCareers) > 0 && $slcTableLangItem == 'bin_career_lang'){
                    $function_result = $this->translateByTableCareer($slcTableLangItem,$listCareers,$tran_lang_code,$arr_translate_error,$arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if($runTime > $limitTime){
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }


                if(count($listImages) > 0 && $slcTableLangItem == 'bin_image_lang'){
                    $function_result = $this->translateByTableImage($slcTableLangItem,$listImages,$tran_lang_code,$arr_translate_error,$arr_translate_success);
                    $arr_translate_error = $function_result[0];
                    $arr_translate_success = $function_result[1];
                    $runTime = microtime(true) - $startTime;
                    if($runTime > $limitTime){
                        $checkTimeout = 'true';
                        break;
                    }
                    break;
                }

            }
            $msg_result = array(
                'arr_translate_error' => $arr_translate_error,
                'arr_translate_success' => $arr_translate_success
            );
            if (isset($msg_result['arr_translate_success']) && count($msg_result['arr_translate_success']) > 0) {
                foreach ($msg_result['arr_translate_success'] as $slcTableLangItemKey => $item_translate_success) {
                    $arr_detail_data = array();
                    foreach ($item_translate_success as $sub_item_translate_success) {
                        array_push($arr_detail_data, $sub_item_translate_success['id']);
                    }
                    $new_translate_detail_model = new BinTranslateDetail();
                    $new_translate_detail_model->setDetailUserId($this->auth['id']);
                    $new_translate_detail_model->setDetailTable($slcTableLangItemKey);
                    $new_translate_detail_model->setDetailStatus(BinTranslateDetail::STATUS_SUCCESS);
                    $new_translate_detail_model->setDetailData(json_encode($arr_detail_data));
                    $new_translate_detail_model->setDetailTotal(count($item_translate_success));
                    $new_translate_detail_model->setDetailInsertTime($this->globalVariable->curTime);
                    $new_translate_detail_model->setDetailActive('Y');
                    $new_translate_detail_model->setDetailLangCode($tran_lang_code);
                    $new_translate_detail_model->save();
                }
            }
            if (isset($msg_result['arr_translate_error']) && count($msg_result['arr_translate_error']) > 0) {
                foreach ($msg_result['arr_translate_error'] as $slcTableLangItemKey => $item_translate_error) {
                    $arr_detail_data = array();
                    foreach ($item_translate_error as $sub_item_translate_error) {
                        array_push($arr_detail_data, $sub_item_translate_error['id']);
                    }
                    $new_translate_detail_model = new BinTranslateDetail();
                    $new_translate_detail_model->setDetailUserId($this->auth['id']);
                    $new_translate_detail_model->setDetailTable($slcTableLangItemKey);
                    $new_translate_detail_model->setDetailStatus(BinTranslateDetail::STATUS_FAIL);
                    $new_translate_detail_model->setDetailData(json_encode($arr_detail_data));
                    $new_translate_detail_model->setDetailTotal(count($item_translate_error));
                    $new_translate_detail_model->setDetailInsertTime($this->globalVariable->curTime);
                    $new_translate_detail_model->setDetailActive('Y');
                    $new_translate_detail_model->setDetailLangCode($tran_lang_code);
                    $new_translate_detail_model->save();
                }
            }

//            if(isset($msg_result)){
//                $this->session->set('msg_result',$msg_result);
//            }
            if (count($messages) == 0) {
                $activity = new Activity();
                $data_log = array(
                    'Translate' => array('arr_translate_error' => $arr_translate_error, 'arr_translate_success' => $arr_translate_success)
                );
                $data_log = json_encode($data_log);
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], 'Translate to ' . $tran_lang_code, $data_log);
            }
            die(json_encode(array(
                "success" => 'false',
                'time_out' => $checkTimeout
            )));
        }
    }

    private function translateByTableArticle($slcTableLangItem, $listArticles, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listArticles as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getArticleId();
            $ar_name = $itemArticle->getArticleName();
            $ar_title = $itemArticle->getArticleTitle();
            $ar_meta_keyword = $itemArticle->getArticleMetaKeyword();
            $ar_meta_description = $itemArticle->getArticleMetaDescription();
            if (empty($ar_name) || empty($ar_title) || empty($ar_meta_keyword) || empty($ar_meta_description)) {
                $message_error .= 'Record ID = ' . $ar_id . ' (' . $slcTableLangItem . ') not enough condition';
            }
            $ar_icon = $itemArticle->getArticleIcon();
            $ar_meta_image = $itemArticle->getArticleMetaImage();
            $ar_summary = $itemArticle->getArticleSummary();
            $ar_content = $itemArticle->getArticleContent();
            if (strlen($message_error) == 0) {
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: " . $ar_name_translate["errorcode"] . " - " . $ar_name_translate["errormessage"] . "<br>";
                }
                $ar_title_translate = $this->googleTranslate->translate($ar_title, $tran_lang_code);
                if ($ar_title_translate["status"] == "fail") {
                    $message_error .= "Title: " . $ar_title_translate["errorcode"] . " - " . $ar_title_translate["errormessage"] . "<br>";
                }
                $ar_keyword_translate = $this->googleTranslate->translate($ar_meta_keyword, $tran_lang_code);
                if ($ar_keyword_translate["status"] == "fail") {
                    $message_error .= "Keyword: " . $ar_keyword_translate["errorcode"] . " - " . $ar_keyword_translate["errormessage"] . "<br>";
                }
                $ar_des_translate = $this->googleTranslate->translate($ar_meta_description, $tran_lang_code);
                if ($ar_des_translate["status"] == "fail") {
                    $message_error .= "Description: " . $ar_des_translate["errorcode"] . " - " . $ar_des_translate["errormessage"] . "<br>";
                }
                $ar_summary_translate = $this->googleTranslate->translate($ar_summary, $tran_lang_code);
                if ($ar_summary_translate["status"] == "fail") {
                    $message_error .= "Summary: " . $ar_summary_translate["errorcode"] . " - " . $ar_summary_translate["errormessage"] . "<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: " . $ar_content_translate["errorcode"] . " - " . $ar_content_translate["errormessage"] . "<br>";
                }
                $ar_icon_translate = $ar_icon;
                $ar_meta_image_translate = $ar_meta_image;
                if (strlen($message_error) == 0) {
                    $data_tran_lang = new BinArticleLang();
                    $data_tran_lang->setArticleId($ar_id);
                    $data_tran_lang->setArticleLangCode($tran_lang_code);
                    $data_tran_lang->setArticleName($ar_name_translate['data']);
                    $data_tran_lang->setArticleTitle($ar_title_translate['data']);
                    $data_tran_lang->setArticleMetaKeyword($ar_keyword_translate['data']);
                    $data_tran_lang->setArticleMetaDescription($ar_des_translate['data']);
                    $data_tran_lang->setArticleMetaImage($ar_meta_image_translate);
                    $data_tran_lang->setArticleIcon($ar_icon_translate);
                    $data_tran_lang->setArticleSummary($ar_summary_translate['data']);
                    $data_tran_lang->setArticleContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTableBanner($slcTableLangItem, $listBanners, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listBanners as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getBannerId();
            $ar_title = $itemArticle->getBannerTitle();
            $ar_subtitle = $itemArticle->getBannerSubtitle();
            $ar_content = $itemArticle->getBannerContent();
            if (strlen($message_error) == 0) {
                $ar_title_translate = $this->googleTranslate->translate($ar_title, $tran_lang_code);
                if ($ar_title_translate["status"] == "fail") {
                    $message_error .= "Title: " . $ar_title_translate["errorcode"] . " - " . $ar_title_translate["errormessage"] . "<br>";
                }
                $ar_subtitle_translate = $this->googleTranslate->translate($ar_subtitle, $tran_lang_code);
                if ($ar_subtitle_translate["status"] == "fail") {
                    $message_error .= "Sub Title: " . $ar_subtitle_translate["errorcode"] . " - " . $ar_subtitle_translate["errormessage"] . "<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: " . $ar_content_translate["errorcode"] . " - " . $ar_content_translate["errormessage"] . "<br>";
                }
                if (strlen($message_error) == 0) {
                    $data_tran_lang = new BinBannerLang();
                    $data_tran_lang->setBannerId($ar_id);
                    $data_tran_lang->setBannerLangCode($tran_lang_code);
                    $data_tran_lang->setBannerTitle($ar_title_translate['data']);
                    $data_tran_lang->setBannerSubtitle($ar_subtitle_translate['data']);
                    $data_tran_lang->setBannerContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTablePage($slcTableLangItem, $listPages, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listPages as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getPageId();
            $ar_name = $itemArticle->getPageName();
            //$ar_keyword = $itemArticle->getPageKeyword();
            $ar_title = $itemArticle->getPageTitle();
            $ar_meta_keyword = $itemArticle->getPageMetaKeyword();
            $ar_meta_description = $itemArticle->getPageMetaDescription();
            if (empty($ar_name) || empty($ar_title) || empty($ar_meta_keyword) || empty($ar_meta_description)) {
                $message_error .= 'Record ID = ' . $ar_id . ' (' . $slcTableLangItem . ') not enough condition';
            }
            $ar_content = $itemArticle->getPageContent();
            if (strlen($message_error) == 0) {
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: " . $ar_name_translate["errorcode"] . " - " . $ar_name_translate["errormessage"] . "<br>";
                }
                $ar_title_translate = $this->googleTranslate->translate($ar_title, $tran_lang_code);
                if ($ar_title_translate["status"] == "fail") {
                    $message_error .= "Title: " . $ar_title_translate["errorcode"] . " - " . $ar_title_translate["errormessage"] . "<br>";
                }
                $ar_keyword_translate = $this->googleTranslate->translate($ar_meta_keyword, $tran_lang_code);
                if ($ar_keyword_translate["status"] == "fail") {
                    $message_error .= "Keyword: " . $ar_keyword_translate["errorcode"] . " - " . $ar_keyword_translate["errormessage"] . "<br>";
                }
                $ar_des_translate = $this->googleTranslate->translate($ar_meta_description, $tran_lang_code);
                if ($ar_des_translate["status"] == "fail") {
                    $message_error .= "Description: " . $ar_des_translate["errorcode"] . " - " . $ar_des_translate["errormessage"] . "<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: " . $ar_content_translate["errorcode"] . " - " . $ar_content_translate["errormessage"] . "<br>";
                }
                if (strlen($message_error) == 0) {
                    $data_tran_lang = new BinPageLang();
                    $data_tran_lang->setPageId($ar_id);
                    $data_tran_lang->setPageLangCode($tran_lang_code);
                    $data_tran_lang->setPageName($ar_name_translate['data']);
                    $data_tran_lang->setPageTitle($ar_title_translate['data']);
                    $data_tran_lang->setPageMetaKeyword($ar_keyword_translate['data']);
                    $data_tran_lang->setPageMetaDescription($ar_des_translate['data']);
                    $data_tran_lang->setPageContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTableCommunicationChannel($slcTableLangItem, $listCommunicationChannels, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listCommunicationChannels as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getCommunicationChannelId();
            $ar_name = $itemArticle->getCommunicationChannelName();
            if (empty($ar_name)) {
                $message_error .= 'Record ID = ' . $ar_id . ' (' . $slcTableLangItem . ') not enough condition';
            }
            if (strlen($message_error) == 0) {
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: " . $ar_name_translate["errorcode"] . " - " . $ar_name_translate["errormessage"] . "<br>";
                }
                if (strlen($message_error) == 0) {
                    $data_tran_lang = new BinCommunicationChannelLang();
                    $data_tran_lang->setCommunicationChannelId($ar_id);
                    $data_tran_lang->setCommunicationChannelLangCode($tran_lang_code);
                    $data_tran_lang->setCommunicationChannelName($ar_name_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTableType($slcTableLangItem, $listTypes, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listTypes as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getTypeId();
            $ar_name = $itemArticle->getTypeName();
            $ar_title = $itemArticle->getTypeTitle();
            $ar_meta_keyword = $itemArticle->getTypeMetaKeyword();
            $ar_meta_description = $itemArticle->getTypeMetaDescription();
            if (empty($ar_name) || empty($ar_title) || empty($ar_meta_keyword) || empty($ar_meta_description)) {
                $message_error .= 'Record ID = ' . $ar_id . ' (' . $slcTableLangItem . ') not enough condition';
            }
            if (strlen($message_error) == 0) {
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: " . $ar_name_translate["errorcode"] . " - " . $ar_name_translate["errormessage"] . "<br>";
                }
                $ar_title_translate = $this->googleTranslate->translate($ar_title, $tran_lang_code);
                if ($ar_title_translate["status"] == "fail") {
                    $message_error .= "Title: " . $ar_title_translate["errorcode"] . " - " . $ar_title_translate["errormessage"] . "<br>";
                }
                $ar_keyword_translate = $this->googleTranslate->translate($ar_meta_keyword, $tran_lang_code);
                if ($ar_keyword_translate["status"] == "fail") {
                    $message_error .= "Keyword: " . $ar_keyword_translate["errorcode"] . " - " . $ar_keyword_translate["errormessage"] . "<br>";
                }
                $ar_des_translate = $this->googleTranslate->translate($ar_meta_description, $tran_lang_code);
                if ($ar_des_translate["status"] == "fail") {
                    $message_error .= "Description: " . $ar_des_translate["errorcode"] . " - " . $ar_des_translate["errormessage"] . "<br>";
                }
                if (strlen($message_error) == 0) {
                    $data_tran_lang = new BinTypeLang();
                    $data_tran_lang->setTypeId($ar_id);
                    $data_tran_lang->setTypeLangCode($tran_lang_code);
                    $data_tran_lang->setTypeName($ar_name_translate['data']);
                    $data_tran_lang->setTypeTitle($ar_title_translate['data']);
                    $data_tran_lang->setTypeMetaKeyword($ar_keyword_translate['data']);
                    $data_tran_lang->setTypeMetaDescription($ar_des_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTableConfig($slcTableLangItem, $listConfigs, $tran_lang_code, $arr_translate_error, $arr_translate_success)
    {
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listConfigs as $itemArticle) {
            $message_error = '';
            $ar_id = $itemArticle->getConfigKey();
            $ar_content = $itemArticle->getConfigContent();
            if (strlen($message_error) == 0) {
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: " . $ar_content_translate["errorcode"] . " - " . $ar_content_translate["errormessage"] . "<br>";
                }
                if (strlen($message_error) == 0) {
                    $article_lang = Config::findByLanguage($ar_id, $tran_lang_code);
                    if ($article_lang) {
                        $article_lang->delete();
                    }
                    $data_tran_lang = new BinConfig();
                    $data_tran_lang->setConfigKey($ar_id);
                    $data_tran_lang->setConfigLanguage($tran_lang_code);
                    $data_tran_lang->setConfigContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if ($result_translate) {
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success, $item_translate_success);
                    } else {
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error, $item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error, $item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error, $item_translate_error);
            }
        }
        if (count($arr_item_translate_error) > 0) {
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if (count($arr_item_translate_success) > 0) {
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }
    private function translateByTableEmailTemplate($slcTableLangItem,$listEmailTemplates,$tran_lang_code,$arr_translate_error,$arr_translate_success){
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listEmailTemplates as $itemArticle){
            $message_error = '';
            $ar_id = $itemArticle->getEmailId();
            $ar_name = $itemArticle->getEmailSubject();
            $ar_content = $itemArticle->getEmailContent();
            if(strlen($message_error) == 0){
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: ".$ar_name_translate["errorcode"] ." - ".  $ar_name_translate["errormessage"]."<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: ".$ar_content_translate["errorcode"] ." - ". $ar_content_translate["errormessage"]."<br>";
                }
                if(strlen($message_error) == 0) {
                    $data_tran_lang = new BinTemplateEmailLang();
                    $data_tran_lang->setEmailId($ar_id);
                    $data_tran_lang->setEmailLangCode($tran_lang_code);
                    $data_tran_lang->setEmailSubject($ar_name_translate['data']);
                    $data_tran_lang->setEmailContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if($result_translate){
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success,$item_translate_success);
                    }else{
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error,$item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error,$item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error,$item_translate_error);
            }
        }
        if(count($arr_item_translate_error)>0){
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if(count($arr_item_translate_success)>0){
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }
    private function translateByTableAlbum($slcTableLangItem,$listAlbums,$tran_lang_code,$arr_translate_error,$arr_translate_success){
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listAlbums as $itemArticle){
            $message_error = '';
            $ar_id = $itemArticle->getAlbumId();
            $ar_name = $itemArticle->getAlbumName();
            $ar_description = $itemArticle->getAlbumDescription();
            if(strlen($message_error) == 0){
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: ".$ar_name_translate["errorcode"] ." - ".  $ar_name_translate["errormessage"]."<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_description, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Description: ".$ar_content_translate["errorcode"] ." - ". $ar_content_translate["errormessage"]."<br>";
                }
                if(strlen($message_error) == 0) {
                    $data_tran_lang = new BinAlbumLang();
                    $data_tran_lang->setAlbumId($ar_id);
                    $data_tran_lang->setAlbumLangCode($tran_lang_code);
                    $data_tran_lang->setAlbumName($ar_name_translate['data']);
                    $data_tran_lang->setAlbumDescription($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if($result_translate){
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success,$item_translate_success);
                    }else{
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error,$item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error,$item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error,$item_translate_error);
            }
        }
        if(count($arr_item_translate_error)>0){
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if(count($arr_item_translate_success)>0){
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }
    private function translateByTableCareer($slcTableLangItem,$listCareers,$tran_lang_code,$arr_translate_error,$arr_translate_success){
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listCareers as $itemArticle){
            $message_error = '';
            $ar_id = $itemArticle->getCareerId();
            $ar_name = $itemArticle->getCareerName();
            $ar_title = $itemArticle->getCareerTitle();
            $ar_meta_keyword = $itemArticle->getCareerMetaKeyword();
            $ar_meta_description = $itemArticle->getCareerMetaDescription();
            if(empty($ar_name) || empty($ar_title) || empty($ar_meta_keyword) || empty($ar_meta_description)){
                $message_error .= 'Record ID = '.$ar_id.' ('.$slcTableLangItem.') not enough condition';
            }
            $ar_location = $itemArticle->getCareerLocation();
            $ar_icon = $itemArticle->getCareerIcon();
            $ar_meta_image = $itemArticle->getCareerMetaImage();
            $ar_summary = $itemArticle->getCareerSummary();
            $ar_content = $itemArticle->getCareerContent();
            if(strlen($message_error) == 0){
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: ".$ar_name_translate["errorcode"] ." - ".  $ar_name_translate["errormessage"]."<br>";
                }
                $ar_title_translate = $this->googleTranslate->translate($ar_title,$tran_lang_code);
                if ($ar_title_translate["status"] == "fail") {
                    $message_error .=  "Title: ".$ar_title_translate["errorcode"] ." - ".  $ar_title_translate["errormessage"]."<br>";
                }
                $ar_keyword_translate = $this->googleTranslate->translate($ar_meta_keyword,$tran_lang_code);
                if ($ar_keyword_translate["status"] == "fail") {
                    $message_error .= "Keyword: ".$ar_keyword_translate["errorcode"] ." - ".  $ar_keyword_translate["errormessage"]."<br>";
                }
                $ar_des_translate = $this->googleTranslate->translate($ar_meta_description, $tran_lang_code);
                if ($ar_des_translate["status"] == "fail") {
                    $message_error .= "Description: ".$ar_des_translate["errorcode"] ." - ". $ar_des_translate["errormessage"]."<br>";
                }
                $ar_summary_translate = $this->googleTranslate->translate($ar_summary, $tran_lang_code);
                if ($ar_summary_translate["status"] == "fail") {
                    $message_error .= "Summary: ".$ar_summary_translate["errorcode"] ." - ". $ar_summary_translate["errormessage"]."<br>";
                }
                $ar_content_translate = $this->googleTranslate->translate($ar_content, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Content: ".$ar_content_translate["errorcode"] ." - ". $ar_content_translate["errormessage"]."<br>";
                }
                $ar_icon_translate = $ar_icon;
                $ar_meta_image_translate = $ar_meta_image;
                if(strlen($message_error) == 0) {
                    $data_tran_lang = new BinCareerLang();
                    $data_tran_lang->setCareerId($ar_id);
                    $data_tran_lang->setCareerLangCode($tran_lang_code);
                    $data_tran_lang->setCareerName($ar_name_translate['data']);
                    $data_tran_lang->setCareerTitle($ar_title_translate['data']);
                    $data_tran_lang->setCareerMetaKeyword($ar_keyword_translate['data']);
                    $data_tran_lang->setCareerMetaDescription($ar_des_translate['data']);
                    $data_tran_lang->setCareerMetaImage($ar_icon_translate);
                    $data_tran_lang->setCareerLocation($ar_location);
                    $data_tran_lang->setCareerIcon($ar_meta_image_translate);
                    $data_tran_lang->setCareerSummary($ar_summary_translate['data']);
                    $data_tran_lang->setCareerContent($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if($result_translate){
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success,$item_translate_success);
                    }else{
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error,$item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error,$item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error,$item_translate_error);
            }
        }
        if(count($arr_item_translate_error)>0){
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if(count($arr_item_translate_success)>0){
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }

    private function translateByTableImage($slcTableLangItem,$listImages,$tran_lang_code,$arr_translate_error,$arr_translate_success){
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listImages as $itemArticle){
            $message_error = '';
            $ar_id = $itemArticle->getImageId();
            $ar_description = $itemArticle->getImageDescription();
            if(strlen($message_error) == 0){
                $ar_content_translate = $this->googleTranslate->translate($ar_description, $tran_lang_code);
                if ($ar_content_translate["status"] == "fail") {
                    $message_error .= "Description: ".$ar_content_translate["errorcode"] ." - ". $ar_content_translate["errormessage"]."<br>";
                }
                if(strlen($message_error) == 0) {
                    $data_tran_lang = new BinImageLang();
                    $data_tran_lang->setImageId($ar_id);
                    $data_tran_lang->setImageLangCode($tran_lang_code);
                    $data_tran_lang->setImageDescription($ar_content_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if($result_translate){
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success,$item_translate_success);
                    }else{
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error,$item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error,$item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error,$item_translate_error);
            }
        }
        if(count($arr_item_translate_error)>0){
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if(count($arr_item_translate_success)>0){
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }
    private function translateByTableOffice($slcTableLangItem,$listOffices,$tran_lang_code,$arr_translate_error,$arr_translate_success){
        $arr_item_translate_error = array();
        $arr_item_translate_success = array();
        foreach ($listOffices as $itemArticle){
            $message_error = '';
            $ar_id = $itemArticle->getOfficeId();
            $ar_name = $itemArticle->getOfficeName();
            $ar_country = $itemArticle->getOfficeCountryName();
            $ar_address = $itemArticle->getOfficeAddress();
            $ar_workingtime = $itemArticle->getOfficeWorkingTime();
            if(empty($ar_name)){
                $message_error .= 'Record ID = '.$ar_id.' ('.$slcTableLangItem.') not enough condition';
            }
            if(strlen($message_error) == 0){
                $ar_name_translate = $this->googleTranslate->translate($ar_name, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Name: ".$ar_name_translate["errorcode"] ." - ".  $ar_name_translate["errormessage"]."<br>";
                }
                $ar_country_translate = $this->googleTranslate->translate($ar_country, $tran_lang_code);
                if ($ar_name_translate["status"] == "fail") {
                    $message_error .= "Country: ".$ar_country_translate["errorcode"] ." - ".  $ar_country_translate["errormessage"]."<br>";
                }
                $ar_address_translate = $this->googleTranslate->translate($ar_address,$tran_lang_code);
                if ($ar_address_translate["status"] == "fail") {
                    $message_error .=  "Title: ".$ar_address_translate["errorcode"] ." - ".  $ar_address_translate["errormessage"]."<br>";
                }
                $ar_workingtime_translate = $this->googleTranslate->translate($ar_workingtime,$tran_lang_code);
                if ($ar_workingtime_translate["status"] == "fail") {
                    $message_error .= "Keyword: ".$ar_workingtime_translate["errorcode"] ." - ".  $ar_workingtime_translate["errormessage"]."<br>";
                }
                if(strlen($message_error) == 0) {
                    $data_tran_lang = new BinOfficeLang();
                    $data_tran_lang->setOfficeId($ar_id);
                    $data_tran_lang->setOfficeLangCode($tran_lang_code);
                    $data_tran_lang->setOfficeName($ar_name_translate['data']);
                    $data_tran_lang->setOfficeCountryName($ar_country_translate['data']);
                    $data_tran_lang->setOfficeAddress($ar_address_translate['data']);
                    $data_tran_lang->setOfficeWorkingTime($ar_workingtime_translate['data']);
                    $result_translate = $data_tran_lang->save();
                    if($result_translate){
                        $item_translate_success = array(
                            'id' => $ar_id,
                            'errortext' => ''
                        );
                        array_push($arr_item_translate_success,$item_translate_success);
                    }else{
                        $item_translate_error = array(
                            'id' => $ar_id,
                            'errortext' => 'Save error'
                        );
                        array_push($arr_item_translate_error,$item_translate_error);
                    }
                } else {
                    $item_translate_error = array(
                        'id' => $ar_id,
                        'errortext' => $message_error
                    );
                    array_push($arr_item_translate_error,$item_translate_error);
                }
            } else {
                $item_translate_error = array(
                    'id' => $ar_id,
                    'errortext' => $message_error
                );
                array_push($arr_item_translate_error,$item_translate_error);
            }
        }
        if(count($arr_item_translate_error)>0){
            $arr_translate_error[$slcTableLangItem] = $arr_item_translate_error;
        }
        if(count($arr_item_translate_success)>0){
            $arr_translate_success[$slcTableLangItem] = $arr_item_translate_success;
        }
        return array(
            $arr_translate_error, $arr_translate_success
        );
    }
}