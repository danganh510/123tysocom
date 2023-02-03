<?php

namespace Score\Backend\Controllers;

use Score\Models\ScArticle;
use Score\Models\ScArticleLang;
use Score\Models\ScLanguage;
use Score\Repositories\Activity;
use Score\Repositories\Article;
use Score\Repositories\ArticleLang;
use Score\Repositories\Language;
use Score\Repositories\Type;
use Score\Repositories\AzureSearch;
use Score\Repositories\SearchAzure;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class ArticleController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_article = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $msg_result = array();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
        }
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }
        $type = new Type();
        $type_search = isset($data["para"]["type_id"]) ? $data["para"]["type_id"] : 0;
        $select_type = $type->getComboboxType("", 0, $type_search);
        $paginator = new PaginatorModel(
            [
                'data' => $list_article,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'select_type' => $select_type,
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,

        ));

    }

    public function updateDocsLang($article, $lang)
    {
        $search = new SearchAzure();
        $url = $search->getUrl($article);
        $valueDataLang = array(SearchAzure::AR_TYPE => $article->getArticleTypeId(),
            SearchAzure::AR_NAME => $article->getArticleName(),
            SearchAzure::AR_TITLE => $article->getArticleTitle(),
            SearchAzure::AR_URL => $url,
            SearchAzure::AR_META_KEYWORD => $article->getArticleMetaKeyword(),
            SearchAzure::AR_META_DESCRIPTION => $article->getArticleMetaDescription(),
            SearchAzure::AR_CONTENT => SearchAzure::substrContent(trim(strip_tags($article->getArticleContent()))),
            SearchAzure::AR_KEY_ID => $lang . '_' . $article->getArticleId(),
            SearchAzure::LANG => $lang);
        return AzureSearch::updateIndexesDocs(array($valueDataLang));
    }

    public function createAction()
    {
        $data = array('id' => -1, 'active' => 'Y', 'is_home' => 'N', 'is_header' => 'N', 'is_horizontal' => 'N', 'is_footer' => 'N', 'full_style' => 'N', 'order' => 1, 'type_id' => 0, 'service_id' => 0, 'insert_time' => $this->my->formatDateTime($this->globalVariable->curTime, false));
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'type_id' => $this->request->getPost("slcType"),
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'email_support' => $this->request->getPost("txtEmailSupport", array('string', 'trim')),
                'icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'icon_large' => $this->request->getPost("txtIconLarge", array('string', 'trim')),
                'icon_large_mobile' => $this->request->getPost("txtIconLargeMobile", array('string', 'trim')),
                'logo' => $this->request->getPost("txtLogo", array('string', 'trim')),
                'logo_active' => $this->request->getPost("txtLogoActive", array('string', 'trim')),
                'meta_image' => $this->request->getPost("txtMetaImage", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'meta_keyword' => $this->request->getPost("txtMetakeyword", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetadescription", array('string', 'trim')),
                'summary' => $this->request->getPost("txtSummary"),
                'content' => $this->request->getPost("txtContent"),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
                'is_home' => $this->request->getPost("radIsHomeActive"),
                'is_header' => $this->request->getPost("radIsHeaderActive"),
                'is_horizontal' => $this->request->getPost("radIsHorizontalActive"),
                'is_footer' => $this->request->getPost("radIsFooterActive"),
                'full_style' => $this->request->getPost("radFullStyleActive"),
                'insert_time' => $this->request->getPost("txtInsertTime", array('string', 'trim')),
            );
            if (empty($data["type_id"])) {
                $messages["type_id"] = "Type field is required.";
            }
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["insert_time"])) {
                $messages["insert_time"] = "Insert time field is required.";
            }
            if (empty($data["title"])) {
                $messages["title"] = "Title field is required.";
            }
            if (empty($data["meta_keyword"])) {
                $messages["meta_keyword"] = "Meta Keyword field is required.";
            }
            if (empty($data["meta_description"])) {
                $messages["meta_description"] = "Meta description field is required.";
            }
            if (empty($data['keyword'])) {
                $messages['keyword'] = 'Keyword field is required.';
            }
//            else {
//                if ((Article::checkKeyword($data['keyword'], $data['type_id'], -1))) {
//                    $messages["keyword"] = "Keyword is exists.";
//                }
//            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $new_type = new ScArticle();
                $new_type->setArticleTypeId(implode(',', $data['type_id']));
                $new_type->setArticleName($data["name"]);
                $new_type->setArticleEmailSupport($data['email_support']);
                $new_type->setArticleIcon($data["icon"]);
                $new_type->setArticleIconLarge($data["icon_large"]);
                $new_type->setArticleIconLargeMobile($data["icon_large_mobile"]);
                $new_type->setArticleLogo($data["logo"]);
                $new_type->setArticleLogoActive($data["logo_active"]);
                $new_type->setArticleMetaImage($data["meta_image"]);
                $new_type->setArticleKeyword($data["keyword"]);
                $new_type->setArticleTitle($data["title"]);
                $new_type->setArticleMetaKeyword($data["meta_keyword"]);
                $new_type->setArticleMetaDescription($data["meta_description"]);
                $new_type->setArticleSummary($data["summary"]);
                $new_type->setArticleContent($data["content"]);
                $new_type->setArticleOrder($data["order"]);
                $new_type->setArticleActive($data["active"]);
                $new_type->setArticleIsHome($data["is_home"]);
                $new_type->setArticleIsHeader($data["is_header"]);
                $new_type->setArticleIsHorizontal($data["is_horizontal"]);
                $new_type->setArticleIsFooter($data["is_footer"]);
                $new_type->setArticleFullStyle($data["full_style"]);
                $new_type->setArticleInsertTime($this->my->UTCTime(strtotime($data["insert_time"])));
                $new_type->setArticleUpdateTime($this->globalVariable->curTime);
                $result = $new_type->save();
                $message = "We can't store your info now: " . "<br/>";
                if ($result === true) {
                    $activity = new Activity();
                    $message = 'Create the article ID: ' . $new_type->getArticleId() . ' success<br>';
                    $status = 'success';
                    //Insert Azure Search
                    if (defined('AZURE_MODE') && AZURE_MODE) {
                        if ($new_type->getArticleActive() == 'Y') {
                            $insertAzure = $this->updateDocsLang($new_type, $this->globalVariable->defaultLanguage);
                            if (isset($insertAzure['status']) && $insertAzure['status'] == 'success') {
                                $message .= 'Create The Article Azure ID: ' . $new_type->getArticleId() . ' Success';
                                $arrLog = isset($insertAzure['result']['value']) ? $insertAzure['result']['value'] : array();
                                $messageLog = 'Insert Azure Logs success';
                                $dataLogAzure = json_encode($arrLog);
                                $activity->logActivity($this->controllerName, 'insert azure', $this->auth['id'], $messageLog, $dataLogAzure);
                            } else {
                                $status = 'error';
                                $message .= isset($insertAzure['message']) ? $insertAzure['message'] : '';
                            }
                        }
                    }
                    //End Insert Azure Search
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('content_article' => array($new_type->getArticleId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                $this->response->redirect("/dashboard/list-article");
                return;

            }
        }
        $type = new Type();
        $select_type = $type->getComboboxTypeMulti("", 0, implode(',',$data["type_id"]));
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function editAction()
    {
        $article_id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($article_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $article_model = Article::getByID($article_id);
        if (empty($article_model)) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data = array(
            'article_id' => -1,
            'article_type_id' => '',
            'article_name' => '',
            'article_email_support' => '',
            'article_icon' => '',
            'article_large' => '',
            'article_large_mobile' => '',
            'article_logo' => '',
            'article_logo_active' => '',
            'article_meta_image' => '',
            'article_keyword' => '',
            'article_title' => '',
            'article_meta_keyword' => '',
            'article_meta_description' => '',
            'article_summary' => '',
            'article_content' => '',
            'article_order' => '',
            'article_active' => '',
            'article_is_home' => '',
            'article_is_header' => '',
            'article_is_horizontal' => '',
            'article__is_footer' => '',
            'article_full_style' => '',
            'article_insert_time' => '',
        );
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $lang_current = $lang_default;
        $arr_language = Language::arrLanguages();
        if ($this->request->isPost()) {
            if (!isset($_POST['save'])) {
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode = $_POST['save'];
            $data_old = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != ScLanguage::GENERAL) {
                $data['article_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data['article_title'] = $this->request->get("txtTitle", array('string', 'trim'));
                $data['article_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data['article_meta_image'] = $this->request->getPost('txtMetaImage', array('string', 'trim'));
                $data['article_meta_keyword'] = $this->request->get("txtMetaKeyword", array('string', 'trim'));
                $data['article_meta_description'] = $this->request->get("txtMetaDescription", array('string', 'trim'));
                $data['article_summary'] = $this->request->get("txtSummary");
                $data['article_content'] = $this->request->get("txtContent");
                //var_dump($data);exit;

                if (empty($data['article_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data['article_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data['article_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data['article_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data['article_type_id'] = $this->request->getPost('txtType');
                $data['article_keyword'] = $this->request->get("txtKeyword", array('string', 'trim'));
                $data['article_email_support'] = $this->request->get('txtEmailSupport', array('string', 'trim'));
                $data['article_icon_large'] = $this->request->getPost("txtIconLarge", array('string', 'trim'));
                $data['article_icon_large_mobile'] = $this->request->getPost("txtIconLargeMobile", array('string', 'trim'));
                $data['article_logo'] = $this->request->getPost("txtLogo", array('string', 'trim'));
                $data['article_logo_active'] = $this->request->getPost("txtLogoActive", array('string', 'trim'));
                $data['article_insert_time'] = $this->request->get("txtInsertTime", array('string', 'trim'));
                $data['article_active'] = $this->request->getPost('radActive');
                $data['article_is_home'] = $this->request->getPost('radIsHomeActive');
                $data['article_is_header'] = $this->request->getPost('radIsHeaderActive');
                $data['article_is_horizontal'] = $this->request->getPost('radIsHorizontalActive');
                $data['article_is_footer'] = $this->request->getPost('radIsFooterActive');
                $data['article_full_style'] = $this->request->getPost('radFullStyleActive');
                $data['article_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                if (empty($data['article_type_id'])) {
                    $messages['type'] = 'Type is required.';
                }

                if (empty($data['article_insert_time'])) {
                    $messages['article_insert_time'] = 'Insert time field is required.';
                }
                if (empty($data['article_order'])) {
                    $messages['order'] = 'Order field is required.';
                } else if (!is_numeric($data['article_order'])) {
                    $messages['order'] = 'Order is not valid.';
                }
                if (empty($data['article_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                }
//                else {
//                    if ((Article::checkKeyword($data['article_keyword'], $data['article_type_id'], $article_id))) {
//                        $messages["keyword"] = "Keyword is exists.";
//                    }
//                }
            }
            if (empty($messages)) {
                $action = '';
                $arrLog = array();
                $messagesAzure = '';
                $messageLog = '';
                $activity = new Activity();
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $arr_article_lang = ScArticleLang::findById($article_id);
                        $data_old = array(
                            'article_type_id' => $article_model->getArticleTypeId(),
                            'article_keyword' => $article_model->getArticleKeyword(),
                            'article_email_support' => $article_model->getArticleEmailSupport(),
                            'icon_large' => $article_model->getArticleIconLarge(),
                            'icon_large_mobile' => $article_model->getArticleIconLargeMobile(),
                            'logo' => $article_model->getArticleLogo(),
                            'logo_active' => $article_model->getArticleLogoActive(),
                            'article_insert_time' => $article_model->getArticleInsertTime(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                            'article_is_home' => $article_model->getArticleIsHome(),
                            'article_is_header' => $article_model->getArticleIsHeader(),
                            'article_is_horizontal' => $article_model->getArticleIsHorizontal(),
                            'article_is_footer' => $article_model->getArticleIsFooter(),
                            'article_full_style' => $article_model->getArticleFullStyle(),
                        );
                        $article_model->setArticleTypeId(implode(',', $data['article_type_id']));
                        $article_model->setArticleKeyword($data['article_keyword']);
                        $article_model->setArticleEmailSupport($data['article_email_support']);
                        $article_model->setArticleIconLarge($data["article_icon_large"]);
                        $article_model->setArticleIconLargeMobile($data["article_icon_large_mobile"]);
                        $article_model->setArticleLogo($data["article_logo"]);
                        $article_model->setArticleLogoActive($data["article_logo_active"]);
                        $article_model->setArticleInsertTime($this->my->UTCTime(strtotime($data['article_insert_time'])));
                        $article_model->setArticleOrder($data['article_order']);
                        $article_model->setArticleActive($data['article_active']);
                        $article_model->setArticleIsHome($data['article_is_home']);
                        $article_model->setArticleIsHeader($data['article_is_header']);
                        $article_model->setArticleIsHorizontal($data['article_is_horizontal']);
                        $article_model->setArticleIsFooter($data['article_is_footer']);
                        $article_model->setArticleFullStyle($data['article_full_style']);
                        $article_model->setArticleUpdateTime($this->globalVariable->curTime);
                        $result = $article_model->update();
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'article_type_id' => $article_model->getArticleTypeId(),
                            'article_keyword' => $article_model->getArticleKeyword(),
                            'article_email_support' => $article_model->getArticleEmailSupport(),
                            'article_insert_time' => $article_model->getArticleInsertTime(),
                            'article_icon_large' => $article_model->getArticleIconLarge(),
                            'article_icon_large_mobile' => $article_model->getArticleIconLargeMobile(),
                            'article_logo' => $article_model->getArticleLogo(),
                            'article_logo_active' => $article_model->getArticleLogoActive(),
                            'article_meta_image' => $article_model->getArticleMetaImage(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                            'article_is_home' => $article_model->getArticleIsHome(),
                            'article_is_header' => $article_model->getArticleIsHeader(),
                            'article_is_horizontal' => $article_model->getArticleIsHorizontal(),
                            'article_is_footer' => $article_model->getArticleIsFooter(),
                            'article_full_style' => $article_model->getArticleFullStyle(),
                        );
                        // General Azure Search
                        if (defined('AZURE_MODE') && AZURE_MODE) {
                            if ($result && $article_model->getArticleActive() == 'Y') {
                                $updateAzureDefault = $this->updateDocsLang($article_model, $lang_default);
                                if (isset($updateAzureDefault['status']) && $updateAzureDefault['status'] == 'success') {
                                    $messagesAzure = ucfirst($info . " Update Article Azure success<br>");
                                    $arrLog[] = isset($updateAzureDefault['result']['value']) ? $updateAzureDefault['result']['value'] : array();
                                    $action = 'edit azure';
                                    $messageLog = 'Edit Azure Logs success';
                                } else {
                                    $messagesAzure = ucfirst($info . ' ' . isset($updateAzureDefault['message']) ? $updateAzureDefault['message'] : '');
                                }
                                foreach ($arr_article_lang as $article_lang) {
                                    $articleMerge = \Phalcon\Mvc\Model::cloneResult(
                                        new ScArticle(), array_merge($article_model->toArray(), $article_lang->toArray())
                                    );
                                    $updateAzureLang = $this->updateDocsLang($articleMerge, $article_lang->getArticleLangCode());
                                    if (isset($updateAzureLang['status']) && $updateAzureLang['status'] == 'success') {
                                        $arrLog[] = isset($updateAzureLang['result']['value']) ? $updateAzureLang['result']['value'] : array();
                                    }
                                }
                            } elseif ($result && $data_old['article_active'] == 'Y' && $article_model->getArticleActive() == 'N') {
                                $arIdDefault = array('ar_key_id' => $lang_default . '_' . $article_id);
                                $deleteDefault = AzureSearch::deleteIndexesDocs($arIdDefault);
                                if (isset($deleteDefault['status']) && $deleteDefault['status'] == 'success') {
                                    $messagesAzure = 'Delete article azure successfully.<br>';
                                    $arrLog[] = isset($deleteDefault['result']['value']) ? $deleteDefault['result']['value'] : array();
                                } else {
                                    $messagesAzure = isset($deleteDefault['message']) ? $deleteDefault['message'] : '' . "<br>";
                                }

                                foreach ($arr_article_lang as $article_lang) {
                                    $arIdLang = array('ar_key_id' => $article_lang->getArticleLangCode() . '_' . $article_id);
                                    $deleteLang = AzureSearch::deleteIndexesDocs($arIdLang);
                                    if (isset($deleteLang['status']) && $deleteLang['status'] == 'success') {
                                        $arrLog[] = isset($deleteLang['result']['value']) ? $deleteLang['result']['value'] : array();
                                    }
                                }

                                $action = 'delete azure';
                                $messageLog = 'Delete Azure Logs success';
                            }
                        }
                        // End General Azure Search
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'article_name' => $article_model->getArticleName(),
                            'article_title' => $article_model->getArticleTitle(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_meta_image' => $article_model->getArticleMetaImage(),
                            'article_meta_keyword' => $article_model->getArticleMetaKeyword(),
                            'article_meta_description' => $article_model->getArticleMetaDescription(),
                            'article_summary' => $article_model->getArticleSummary(),
                            'article_content' => $article_model->getArticleContent(),
                        );
                        $article_model->setArticleName($data['article_name']);
                        $article_model->setArticleTitle($data['article_title']);
                        $article_model->setArticleIcon($data['article_icon']);
                        $article_model->setArticleMetaImage($data['article_meta_image']);
                        $article_model->setArticleMetaKeyword($data['article_meta_keyword']);
                        $article_model->setArticleMetaDescription($data['article_meta_description']);
                        $article_model->setArticleSummary($data['article_summary']);
                        $article_model->setArticleContent($data['article_content']);
                        $article_model->setArticleUpdateTime($this->globalVariable->curTime);
                        $result = $article_model->update();
                        $info = $arr_language[$save_mode];
                        //Update Azure Default Search
                        if (defined('AZURE_MODE') && AZURE_MODE) {
                            if ($result && $article_model->getArticleActive() == 'Y') {
                                $updateAzureDefault = $this->updateDocsLang($article_model, $lang_default);
                                if (isset($updateAzureDefault['status']) && $updateAzureDefault['status'] == 'success') {
                                    $messagesAzure = ucfirst($info . " Update Article Azure En success<br>");
                                    $arrLog = isset($updateAzureDefault['result']['value']) ? $updateAzureDefault['result']['value'] : array();
                                    $action = 'edit azure';
                                    $messageLog = 'Edit Azure Logs success';
                                } else {
                                    $messagesAzure = ucfirst($info . ' ' . isset($updateAzureDefault['message']) ? $updateAzureDefault['message'] : '');
                                }
                            }
                        }
                        //End Update Azure Default Search
                        $data_new = array(
                            'article_name' => $article_model->getArticleName(),
                            'article_title' => $article_model->getArticleTitle(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_meta_image' => $article_model->getArticleMetaImage(),
                            'article_meta_keyword' => $article_model->getArticleMetaKeyword(),
                            'article_meta_description' => $article_model->getArticleMetaDescription(),
                            'article_summary' => $article_model->getArticleSummary(),
                            'article_content' => $article_model->getArticleContent(),
                        );
                        break;
                    default:
                        $content_article_lang = ArticleLang::findFirstByIdAndLang($article_id, $save_mode);
                        if (!$content_article_lang) {
                            $content_article_lang = new ScArticleLang();
                            $content_article_lang->setArticleId($article_id);
                            $content_article_lang->setArticleLangCode($save_mode);
                        }
                        $data_old = $content_article_lang->toArray();
                        $content_article_lang->setArticleName($data['article_name']);
                        $content_article_lang->setArticleTitle($data['article_title']);
                        $content_article_lang->setArticleIcon($data['article_icon']);
                        $content_article_lang->setArticleMetaImage($data['article_meta_image']);
                        $content_article_lang->setArticleMetaKeyword($data['article_meta_keyword']);
                        $content_article_lang->setArticleMetaDescription($data['article_meta_description']);
                        $content_article_lang->setArticleSummary($data['article_summary']);
                        $content_article_lang->setArticleContent($data['article_content']);
                        $result = $content_article_lang->save();
                        $info = $arr_language[$save_mode];
                        //Update Azure Lang Search
                        if (defined('AZURE_MODE') && AZURE_MODE) {
                            if ($result && $article_model->getArticleActive() == 'Y') {
                                $articleMerge = \Phalcon\Mvc\Model::cloneResult(
                                    new ScArticle(), array_merge($article_model->toArray(), $content_article_lang->toArray()));
                                $updateAzureLang = $this->updateDocsLang($articleMerge, $save_mode);
                                if (isset($updateAzureLang['status']) && $updateAzureLang['status'] == 'success') {
                                    $messagesAzure = ucfirst($info . " Update Article Azure Lang success<br>");
                                    $arrLog = isset($updateAzureLang['result']['value']) ? $updateAzureLang['result']['value'] : array();
                                    $action = 'edit azure';
                                    $messageLog = 'Edit Azure Logs success';
                                } else {
                                    $messagesAzure = ucfirst($info . ' ' . isset($updateAzureLang['message']) ? $updateAzureLang['message'] : '');
                                }
                            }
                        }
                        //End Update Azure Lang Search
                        $data_new = array(
                            'article_lang_code' => $content_article_lang->getArticleLangCode(),
                            'article_name' => $content_article_lang->getArticleName(),
                            'article_title' => $content_article_lang->getArticleTitle(),
                            'article_icon' => $content_article_lang->getArticleIcon(),
                            'article_meta_image' => $content_article_lang->getArticleMetaImage(),
                            'article_meta_keyword' => $content_article_lang->getArticleMetaKeyword(),
                            'article_meta_description' => $content_article_lang->getArticleMetaDescription(),
                            'article_summary' => $content_article_lang->getArticleSummary(),
                            'article_content' => $content_article_lang->getArticleContent(),
                        );
                        break;
                }
                if ($result) {
                    //Update Insert Azure Search
                    if (count($arrLog) > 0) {
                        $dataLogAzure = json_encode($arrLog);
                        $activity->logActivity($this->controllerName, $action, $this->auth['id'], $messageLog, $dataLogAzure);
                    }
                    $messages = array(
                        'message' => ucfirst($info . " Update Article success<br>") . $messagesAzure,
                        'typeMessage' => 'success'
                    );
                    //End Update Azure Search;
                    $message = '';
                    $data_log = json_encode(array('content_article' => array($article_id => array($data_old, $data_new))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $messages = array(
                        'message' => "Update article fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'article_id' => $article_model->getArticleId(),
            'article_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_name'] : $article_model->getArticleName(),
            'article_title' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_title'] : $article_model->getArticleTitle(),
            'article_icon' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_icon'] : $article_model->getArticleIcon(),
            'article_meta_image' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_meta_image'] : $article_model->getArticleMetaImage(),
            'article_meta_keyword' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_meta_keyword'] : $article_model->getArticleMetaKeyword(),
            'article_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_meta_description'] : $article_model->getArticleMetaDescription(),
            'article_summary' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_summary'] : $article_model->getArticleSummary(),
            'article_content' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data['article_content'] : $article_model->getArticleContent(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_article_lang = ScArticleLang::findById($article_id);
        foreach ($arr_article_lang as $article_lang) {
            $item = array(
                'article_id' => $article_lang->getArticleId(),
                'article_name' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_name'] : $article_lang->getArticleName(),
                'article_title' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_title'] : $article_lang->getArticleTitle(),
                'article_icon' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_icon'] : $article_lang->getArticleIcon(),
                'article_meta_image' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_meta_image'] : $article_lang->getArticleMetaImage(),
                'article_meta_keyword' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_meta_keyword'] : $article_lang->getArticleMetaKeyword(),
                'article_meta_description' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_meta_description'] : $article_lang->getArticleMetaDescription(),
                'article_summary' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_summary'] : $article_lang->getArticleSummary(),
                'article_content' => ($save_mode === $article_lang->getArticleLangCode()) ? $data['article_content'] : $article_lang->getArticleContent(),
            );
            $arr_translate[$article_lang->getArticleLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'article_id' => -1,
                'article_name' => $data['article_name'],
                'article_title' => $data['article_title'],
                'article_meta_keyword' => $data['article_meta_keyword'],
                'article_meta_description' => $data['article_meta_description'],
                'article_icon' => $data['article_icon'],
                'article_meta_image' => $data['article_meta_image'],
                'article_summary' => $data['article_summary'],
                'article_content' => $data['article_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'article_id' => $article_model->getArticleId(),
            'article_type_id' => ($save_mode === ScLanguage::GENERAL) ? implode(',', $data['article_type_id']) : $article_model->getArticleTypeId(),
            'article_keyword' => ($save_mode === ScLanguage::GENERAL) ? $data['article_keyword'] : $article_model->getArticleKeyword(),
            'article_email_support' => ($save_mode === ScLanguage::GENERAL) ? $data['article_email_support'] : $article_model->getArticleEmailSupport(),
            'article_icon_large' => ($save_mode === ScLanguage::GENERAL) ? $data['article_icon_large'] : $article_model->getArticleIconLarge(),
            'article_icon_large_mobile' => ($save_mode === ScLanguage::GENERAL) ? $data['article_icon_large_mobile'] : $article_model->getArticleIconLargeMobile(),
            'article_logo' => ($save_mode === ScLanguage::GENERAL) ? $data['article_logo'] : $article_model->getArticleLogo(),
            'article_logo_active' => ($save_mode === ScLanguage::GENERAL) ? $data['article_logo_active'] : $article_model->getArticleLogoActive(),
            'article_insert_time' => ($save_mode === ScLanguage::GENERAL) ? $data['article_insert_time'] : $this->my->formatDateTime($article_model->getArticleInsertTime(), false),
            'article_order' => ($save_mode === ScLanguage::GENERAL) ? $data['article_order'] : $article_model->getArticleOrder(),
            'article_active' => ($save_mode === ScLanguage::GENERAL) ? $data['article_active'] : $article_model->getArticleActive(),
            'article_is_home' => ($save_mode === ScLanguage::GENERAL) ? $data['article_is_home'] : $article_model->getArticleIsHome(),
            'article_is_header' => ($save_mode === ScLanguage::GENERAL) ? $data['article_is_header'] : $article_model->getArticleIsHeader(),
            'article_is_horizontal' => ($save_mode === ScLanguage::GENERAL) ? $data['article_is_horizontal'] : $article_model->getArticleIsHorizontal(),
            'article_is_footer' => ($save_mode === ScLanguage::GENERAL) ? $data['article_is_footer'] : $article_model->getArticleIsFooter(),
            'article_full_style' => ($save_mode === ScLanguage::GENERAL) ? $data['article_full_style'] : $article_model->getArticleFullStyle(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );
        $type = new Type();
        $select_type = $type->getComboboxTypeMulti("", 0, $formData['article_type_id']);

        $messages['status'] = 'border-red';
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }

    public function deleteAction()
    {
        $activity = new Activity();
        $arrLog = array();
        $list_article = $this->request->getPost('item');
        $Scnet_article = array();
        $message = '';
        $msg_delete = array('success' => '');
        if ($list_article) {
            foreach ($list_article as $article_id) {
                $article = Article::getByID($article_id);
                $arr_article_lang = ScArticleLang::findById($article_id);
                if ($article) {
                    $old_article_data = $article->toArray();
                    $new_article_data = array();
                    $Scnet_article[$article_id] = array($old_article_data, $new_article_data);
                    $article->delete();
                    ArticleLang::deleteById($article_id);
                    //Delete Docs Azure Search
                    if (defined('AZURE_MODE') && AZURE_MODE) {
                        $arIdDefault = array('ar_key_id' => $this->globalVariable->defaultLanguage . '_' . $article_id);
                        $deleteDefault = AzureSearch::deleteIndexesDocs($arIdDefault);
                        if (isset($deleteDefault['status']) && $deleteDefault['status'] == 'success') {
                            $message = 'Delete article azure successfully.<br>';
                            $arrLog[] = isset($deleteDefault['result']['value']) ? $deleteDefault['result']['value'] : array();
                        } else {
                            $message = (isset($deleteDefault['message']) ? $deleteDefault['message'] : '') . "<br>";
                        }

                        foreach ($arr_article_lang as $article_lang) {
                            $ar_id_lang = array('ar_key_id' => $article_lang->getArticleLangCode() . '_' . $article_id);
                            $deleteLang = AzureSearch::deleteIndexesDocs($ar_id_lang);
                            if (isset($deleteLang['status']) && $deleteLang['status'] == 'success') {
                                $arrLog[] = isset($deleteLang['result']['value']) ? $deleteLang['result']['value'] : array();
                            }
                        }
                    }
                    //End Delete Docs Azure Search
                }
            }
        }
        if (count($arrLog) > 0) {
            $action = 'delete azure';
            $messageLog = 'Delete Azure Logs success';
            $dataLogAzure = json_encode($arrLog);
            $activity->logActivity($this->controllerName, $action, $this->auth['id'], $messageLog, $dataLogAzure);
        }
        if (count($Scnet_article) > 0) {
            // delete success
            $message .= 'Delete ' . count($Scnet_article) . ' article success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('content_article' => $Scnet_article));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        $this->response->redirect('/dashboard/list-article');
        return;
    }

    private function getParameter()
    {
        $sql = "SELECT * FROM Score\Models\ScArticle WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $type = $this->request->get("slType");
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (article_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (article_name like CONCAT('%',:keyword:,'%') OR article_keyword like CONCAT('%',:keyword:,'%')
                OR article_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $validator = new Validator();
        if (!empty($type)) {
            if ($validator->validInt($type) == false) {
                $this->response->redirect("/notfound");
                return;
            }
            $sql .= " AND article_type_id like CONCAT('%',:type_id:,'%')";
            $arrParameter["type_id"] = $type;
            $this->dispatcher->setParam("slType", $type);
        }
        $sql .= " ORDER BY article_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    private function getParameterExport()
    {
        $sql = "SELECT * FROM Score\Models\ScArticle WHERE article_active = 'Y'";
        $keyword = trim($this->request->get("txtSearch"));
        $type = $this->request->get("slType");

        $arrParameter = array();
        $validator = new \Score\Utils\Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (article_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (article_name like CONCAT('%',:keyword:,'%') OR article_keyword like CONCAT('%',:keyword:,'%')
                OR article_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $validator = new Validator();
        if (!empty($type)) {
            if ($validator->validInt($type) == false) {
                $this->response->redirect("/notfound");
                return;
            }
            $sql .= " AND article_type_id like CONCAT('%',:type_id:,'%')";
            $arrParameter["type_id"] = $type;
            $this->dispatcher->setParam("slType", $type);
        }

        $sql .= " ORDER BY article_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    public function exportAction()
    {
        $data = $this->getParameterExport();
        $list_article = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new \Score\Utils\Validator();

        foreach ($list_article as $article) {
            $content = $article->getArticleContent();
            $blockFull = str_replace('row d-flex d-xs-block', 'row d-flex d-xs-block block-full', $content);

        }

        if ($validator->validInt($current_page) == false || $current_page < 1)
            $current_page = 1;
        $msg_result = array();
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
        }
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }

        $type_search = isset($data["para"]["type_id"]) ? $data["para"]["type_id"] : 0;


        //type
        $type = new Type();
        $select_type = $type->getComboboxType("", 0, $type_search);


        $this->view->setVars(array(
            'page' => $list_article,
            'select_type' => $select_type,
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
        ));
    }

}