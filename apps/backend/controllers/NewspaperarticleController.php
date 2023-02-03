<?php

namespace Score\Backend\Controllers;
use Score\Models\ScLanguage;
use Score\Repositories\Activity;
use Score\Repositories\Language;
use Score\Utils\Validator;

use Score\Repositories\Newspaper;
use Score\Models\ScNewspaperArticle;
use Score\Models\ScNewspaperArticleLang;
use Score\Repositories\NewspaperArticle;
use Score\Repositories\NewspaperArticleLang;

use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class NewspaperarticleController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_newspaper_article = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
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
        $paginator = new PaginatorModel(
            [
                'data'  => $list_newspaper_article,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $newspaper_id = isset($data["para"]["newspaper_id"]) ? $data["para"]["newspaper_id"]:0;

        $select_newspaper = Newspaper::getNewspaperCombobox($newspaper_id);
        $this->view->setVars(array(
            'article_list' => $paginator->getPaginate(),
            'select_newspaper' => $select_newspaper,
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $data = array('id' => -1, 'newspaper' => '', 'article_order' => 1, 'active' => 'Y');
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'newspaper' => $this->request->getPost("slcNewspaper"),
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'link' => $this->request->getPost("txtLink", array('string', 'trim')),
                'icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder"),
                'active' => $this->request->getPost("radioActive"),
                'insert_time' => $this->globalVariable->curTime,
                'update_time' => $this->globalVariable->curTime,
            );

            if (empty($data["newspaper"])) {
                $messages["newspaper"] = "Newspaper field is required.";
            }
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["link"])) {
                $messages["link"] = "Link field is required.";
            }
            if (empty($data['order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $NewspaperArticle = new ScNewspaperArticle();
                $NewspaperArticle->setArticleNewspaperId($data['newspaper']);
                $NewspaperArticle->setArticleName(($data["name"]));
                $NewspaperArticle->setArticleLink(($data["link"]));
                $NewspaperArticle->setArticleIcon($data["icon"]);
                $NewspaperArticle->setArticleOrder($data["order"]);
                $NewspaperArticle->setArticleActive($data["active"]);
                $NewspaperArticle->setArticleInsertTime($data['insert_time']);
                $NewspaperArticle->setArticleUpdateTime($data['update_time']);
                $result = $NewspaperArticle->save();

                if ($result === true){
                    $message = 'Create the Newspaper Article ID: '.$NewspaperArticle->getArticleId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('content_newspaper_article' => array($NewspaperArticle->getArticleId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $message =  "We can't store your info now: <br/>";
                    foreach ($NewspaperArticle->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-newspaper-article");
            }
        }
        $select_newspaper = Newspaper::getNewspaperCombobox($data['newspaper']);

        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'select_newspaper' => $select_newspaper,
            'messages' => $messages
        ]);
    }

    public function editAction()
    {
        $article_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($article_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $article_model = NewspaperArticle::findFirstById($article_id);
        if(empty($article_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array();
        $save_mode = '';
        $lang_current = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        if($this->request->isPost()) {
            if(!isset($_POST['save'])){
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode =  $_POST['save'] ;
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if($save_mode == ScLanguage::GENERAL) {
                $data_post['article_newspaper_id'] = $this->request->getPost('slcNewspaper');
                $data_post['article_link'] = $this->request->getPost('txtLink', array('string', 'trim'));
                $data_post['article_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data_post['article_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                $data_post['article_active'] = $this->request->getPost('radioActive');
                $data_post['article_update_time'] = $this->globalVariable->curTime;
                if (empty($data_post["article_newspaper_id"])) {
                    $messages["newspaper"] = "Newspaper field is required.";
                }
                if (empty($data_post["article_link"])) {
                    $messages["link"] = "Link field is required.";
                }
                if (empty($data_post['article_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post["article_order"])) {
                    $messages["order"] = "Order is not valid ";
                }
            } else {
                $data_post['article_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                if (empty($data_post["article_name"])) {
                    $messages["name"] = "Name field is required.";
                }
            }
            if(empty($messages)) {
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $data_old = array(
                            'article_newspaper_id' => $article_model->getArticleNewspaperId(),
                            'article_link' => $article_model->getArticleLink(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                        );
                        $article_model->setArticleNewspaperId($data_post['article_newspaper_id']);
                        $article_model->setArticleLink($data_post['article_link']);
                        $article_model->setArticleIcon($data_post['article_icon']);
                        $article_model->setArticleOrder($data_post['article_order']);
                        $article_model->setArticleActive($data_post['article_active']);
                        $result = $article_model->update();
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'article_newspaper_id' => $article_model->getArticleNewspaperId(),
                            'article_link' => $article_model->getArticleLink(),
                            'article_icon' => $article_model->getArticleIcon(),
                            'article_order' => $article_model->getArticleOrder(),
                            'article_active' => $article_model->getArticleActive(),
                        );
                        break;

                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'article_name' => $article_model->getArticleName(),
                        );
                        $article_model->setArticleName(html_entity_decode($data_post['article_name']));
                        $result = $article_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'article_name' => $article_model->getArticleName(),
                        );
                        break;
                    default:
                        $data_newspaper_article_lang = NewspaperArticleLang::findFirstByIdLang($article_id, $save_mode);
                        if (!$data_newspaper_article_lang) {
                            $data_newspaper_article_lang = new ScNewspaperArticleLang();
                            $data_newspaper_article_lang->setArticleId($article_id);
                            $data_newspaper_article_lang->setArticleLangCode($save_mode);
                        }
                        $data_old = $data_newspaper_article_lang->toArray();
                        $data_newspaper_article_lang->setArticleName(html_entity_decode($data_post['article_name']));

                        $result = $data_newspaper_article_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'article_name' => $data_newspaper_article_lang->getArticleName(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " update Newspaper article success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('content_newspaper_article_lang' => array($article_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update Newspaper Article fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'article_name' => ($save_mode ===$this->globalVariable->defaultLanguage) ? $data_post['article_name'] : $article_model->getArticleName(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_article_lang = NewspaperArticleLang::findById($article_id);
        foreach ($arr_article_lang as $article_lang){
            $item = array(
                'article_id'=>$article_lang->getArticleId(),
                'article_name'=>($save_mode === $article_lang->getArticleLangCode()) ? $data_post['article_name'] : $article_lang->getArticleName(),
            );
            $arr_translate[$article_lang->getArticleLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])){
            $item = array(
                'article_id'=> -1,
                'article_name'=> $data_post['article_name'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'id'=> $article_model->getArticleId(),
            'newspaper' => ($save_mode === ScLanguage::GENERAL) ? $data_post['article_newspaper_id'] : $article_model->getArticleNewspaperId(),
            'icon' => ($save_mode === ScLanguage::GENERAL) ? $data_post['article_icon'] : $article_model->getArticleIcon(),
            'link' => ($save_mode === ScLanguage::GENERAL) ? $data_post['article_link'] : $article_model->getArticleLink(),
            'order' => ($save_mode === ScLanguage::GENERAL) ? $data_post['article_order'] : $article_model->getArticleOrder(),
            'active' => ($save_mode === ScLanguage::GENERAL) ? $data_post['article_active'] : $article_model->getArticleActive(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $select_newspaper = Newspaper::getNewspaperCombobox($formData['newspaper']);

        $messages["status"] = "border-red";
        $this->view->setVars([
            'select_newspaper' => $select_newspaper,
            'formData' => $formData,
            'messages' => $messages
        ]);
    }
    public function deleteAction()
    {
        $list_newspaper_article = $this->request->get('item');
        $arrArticle = array();
        $msg_delete = array('error' => '', 'success' => '');
        if($list_newspaper_article) {
            foreach ($list_newspaper_article as $article_id) {
                $article_model = ScNewspaperArticle::findFirst($article_id);
                if($article_model) {
                    $old_newspaperArticle_data = array(
                        'name' => $article_model->getArticleName(),
                        'newspaper' => $article_model->getArticleNewspaperId(),
                        'icon' => $article_model->getArticleIcon(),
                        'link' => $article_model->getArticleLink(),
                        'order' => $article_model->getArticleOrder(),
                        'active' => $article_model->getArticleActive()
                    );
                    $new_newspaperArticle_data = array();
                    $arrArticle[$article_id] = array($old_newspaperArticle_data, $new_newspaperArticle_data);
                    $article_model->delete();
                    NewspaperArticleLang::deleteById($article_id);
                }
            }
        }
        if (count($arrArticle) > 0) {
            // delete success
            $message = 'Delete '. count($arrArticle) .' Page success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('content_newspaper_article' => $arrArticle));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/dashboard/list-newspaper-article');
    }
    private function getParameter(){
        $sql = "SELECT * FROM Score\Models\ScNewspaperArticle WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $newspaper = $this->request->get("slNewspaper");
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (article_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (article_name like CONCAT('%',:keyword:,'%') OR article_link like CONCAT('%',:keyword:,'%') )";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if(!empty($newspaper)){
            if($validator->validInt($newspaper)==false)  {
                $this->response->redirect("/notfound");
            }
            $sql.=" AND article_newspaper_id = :newspaper_id:";
            $arrParameter["newspaper_id"] = $newspaper;
            $this->dispatcher->setParam("slNewspaper", $newspaper);
        }
        $sql .= " ORDER BY article_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}