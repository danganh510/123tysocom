<?php

namespace Score\Backend\Controllers;
use Score\Repositories\PageLang;
use Score\Models\ScLanguage;
use Score\Models\ScPage;
use Score\Models\ScPageLang;
use Score\Repositories\Activity;
use Score\Repositories\Language;
use Score\Repositories\Page;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class PageController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_page = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
                'data'  => $list_page,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $data = array('id' => -1);
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => trim($this->request->getPost("txtName")),
                'title' => trim($this->request->getPost("txtTitle")),
                'keyword' => trim($this->request->getPost("txtKeyword")),
                'meta_image' => trim($this->request->getPost("txtMetaImage")),
                'meta_description' => trim($this->request->getPost("txtMetades")),
                'meta_keyword' => trim($this->request->getPost("txtMetakey")),
                'style' => $this->request->getPost("txtStyle"),
                'content' => $this->request->getPost("txtContent"),
            );

            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["title"])) {
                $messages["title"] = "Title field is required.";
            }
            if (empty($data["keyword"])) {
                $messages["keyword"] = "Keyword field is required.";
            }elseif (!Page::checkKeyword($data["keyword"], -1)){
                $messages["keyword"] = "Keyword is exists!";
            }
            if (empty($data["meta_description"])) {
                $messages["meta_description"] = "Meta description field is required.";
            }
            if (empty($data["meta_keyword"])) {
                $messages["meta_keyword"] = "Meta keyword field is required.";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_page = new ScPage();
                $new_page->setPageName(html_entity_decode($data["name"]));
                $new_page->setPageTitle(html_entity_decode($data["title"]));
                $new_page->setPageKeyword($data["keyword"]);
                $new_page->setPageMetaImage($data["meta_image"]);
                $new_page->setPageMetaDescription($data["meta_description"]);
                $new_page->setPageMetaKeyword($data["meta_keyword"]);
                $new_page->setPageStyle($data["style"]);
                $new_page->setPageContent($data["content"]);
                $result = $new_page->save();

                if ($result === true){
                    $message = 'Create the page ID: '.$new_page->getPageId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('career_page' => array($new_page->getPageId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $message =  "We can't store your info now: <br/>";
                    foreach ($new_page->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-page");
            }else {
                $data["name"] = htmlentities($data["name"]);
                $data["title"] = htmlentities($data["title"]);
            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages
        ]);
    }
    public function editAction()
    {
        $page_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($page_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $page_model = ScPage::findFirstById($page_id);
        if(empty($page_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array (
            'page_id' => -1,
            'page_name' => '',
            'page_title' => '',
            'page_keyword' => '',
            'page_meta_image' => '',
            'page_meta_keyword' => '',
            'page_meta_description' => '',
            'page_style' => '',
            'page_content' => '',
        ) ;
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
            $data_old = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if($save_mode != ScLanguage::GENERAL) {
                $data_post['page_name'] = trim($this->request->getPost('txtName'));
                $data_post['page_title'] = trim($this->request->getPost('txtTitle'));
                $data_post['page_meta_keyword'] = trim($this->request->getPost('txtMetaKey'));
                $data_post['page_meta_description'] = trim($this->request->getPost('txtMetaDesc'));
                $data_post['page_meta_image'] = $this->request->getPost('txtMetaImage');
                $data_post['page_content'] = $this->request->getPost('txtContent');
                if(empty($data_post['page_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if(empty($data_post['page_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if(empty($data_post['page_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if(empty($data_post['page_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data_post['page_keyword'] = $this->request->getPost('txtKeyword', array('string', 'trim'));
                $data_post['page_style'] = $this->request->getPost('txtStyle');
                if(empty($data_post['page_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } else if (!Page::checkKeyword($data_post['page_keyword'],$page_id)) {
                    $messages["keyword"] = "Keyword is exists.";
                }
            }
            if(empty($messages)) {
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $data_old = array(
                            'page_keyword' => $page_model->getPageKeyword(),
                            'page_style' => $page_model->getPageStyle(),
                        );
                        $page_model->setPageKeyword($data_post['page_keyword']);
                        $page_model->setPageStyle($data_post['page_style']);
                        $result = $page_model->update();
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'page_keyword' => $page_model->getPageKeyword(),
                            'page_meta_image' => $page_model->getPageMetaImage(),
                            'page_style' => $page_model->getPageStyle(),
                        );
                        break;

                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'page_name' => $page_model->getPageName(),
                            'page_title' => $page_model->getPageTitle(),
                            'page_meta_keyword' => $page_model->getPageMetaKeyword(),
                            'page_meta_description' => $page_model->getPageMetaDescription(),
                            'page_meta_image' => $page_model->getPageMetaImage(),
                            'page_content' => $page_model->getPageContent(),
                        );
                        $page_model->setPageName(html_entity_decode($data_post['page_name']));
                        $page_model->setPageTitle(html_entity_decode($data_post['page_title']));
                        $page_model->setPageMetaKeyword($data_post['page_meta_keyword']);
                        $page_model->setPageMetaDescription($data_post['page_meta_description']);
                        $page_model->setPageMetaImage($data_post['page_meta_image']);
                        $page_model->setPageContent($data_post['page_content']);
                        $result = $page_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'page_name' => $page_model->getPageName(),
                            'page_title' => $page_model->getPageTitle(),
                            'page_meta_keyword' => $page_model->getPageMetaKeyword(),
                            'page_meta_description' => $page_model->getPageMetaDescription(),
                            'page_content' => $page_model->getPageContent(),
                        );
                        break;
                    default:
                        $data_page_lang = PageLang::findFirstByIdAndLang($page_id, $save_mode);
                        if (!$data_page_lang) {
                            $data_page_lang = new ScPageLang();
                            $data_page_lang->setPageId($page_id);
                            $data_page_lang->setPageLangCode($save_mode);
                        }
                        $data_old = $data_page_lang->toArray();
                        $data_page_lang->setPageName(html_entity_decode($data_post['page_name']));
                        $data_page_lang->setPageTitle(html_entity_decode($data_post['page_title']));
                        $data_page_lang->setPageMetaKeyword($data_post['page_meta_keyword']);
                        $data_page_lang->setPageMetaDescription($data_post['page_meta_description']);
                        $data_page_lang->setPageMetaImage($data_post['page_meta_image']);
                        $data_page_lang->setPageContent($data_post['page_content']);
                        $result = $data_page_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'page_lang_code' => $data_page_lang->getPageLangCode(),
                            'page_name' => $data_page_lang->getPageName(),
                            'page_title' => $data_page_lang->getPageTitle(),
                            'page_meta_keyword' => $data_page_lang->getPageMetaKeyword(),
                            'page_meta_description' => $data_page_lang->getPageMetaDescription(),
                            'page_meta_image' => $data_page_lang->getPageMetaImage(),
                            'page_content' => $data_page_lang->getPageContent(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " update page success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('content_page_lang' => array($page_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update page fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $page_model = Page::getByID($page_model->getPageId());
        $item = array(
            'page_id' =>$page_model->getPageId(),
            'page_name'=>($save_mode === $this->globalVariable->defaultLanguage)?htmlentities($data_post['page_name']):htmlentities($page_model->getPageName()),
            'page_title' => ($save_mode ===$this->globalVariable->defaultLanguage)?htmlentities($data_post['page_title']):htmlentities($page_model->getPageTitle()),
            'page_meta_keyword'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['page_meta_keyword']:$page_model->getPageMetaKeyword(),
            'page_meta_description' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['page_meta_description']:$page_model->getPageMetaDescription(),
            'page_meta_image'=>($save_mode ===$this->globalVariable->defaultLanguage)
                ?$data_post['page_meta_image']:$page_model->getPageMetaImage(),
            'page_content' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['page_content']:$page_model->getPageContent(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_page_lang = ScPageLang::findById($page_id);
        foreach ($arr_page_lang as $page_lang){
            $item = array(
                'page_id'=>$page_lang->getPageId(),
                'page_name'=>($save_mode === $page_lang->getPageLangCode())?htmlentities($data_post['page_name']):htmlentities($page_lang->getPageName()),
                'page_title' => ($save_mode === $page_lang->getPageLangCode())?htmlentities($data_post['page_title']):htmlentities($page_lang->getPageTitle()),
                'page_meta_keyword'=>($save_mode === $page_lang->getPageLangCode())?$data_post['page_meta_keyword']:$page_lang->getPageMetaKeyword(),
                'page_meta_description' => ($save_mode === $page_lang->getPageLangCode())?$data_post['page_meta_description']:$page_lang->getPageMetaDescription(),
                'page_meta_image' => ($save_mode === $page_lang->getPageLangCode())
                    ?$data_post['page_meta_image']:$page_lang->getPageMetaImage(),
                'page_content' => ($save_mode === $page_lang->getPageLangCode())?$data_post['page_content']:$page_lang->getPageContent(),
            );
            $arr_translate[$page_lang->getPageLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'page_id'=> -1,
                'page_name'=> htmlentities($data_post['page_name']),
                'page_title' => htmlentities($data_post['page_title']),
                'page_meta_keyword' => $data_post['page_meta_keyword'],
                'page_meta_description' => $data_post['page_meta_description'],
                'page_meta_image' => $data_post['page_meta_image'],
                'page_content' => $data_post['page_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'page_id'=>$page_model->getPageId(),
            'page_keyword' => ($save_mode ===ScLanguage::GENERAL)?$data_post['page_keyword']:$page_model->getPageKeyword(),
            'page_style' => ($save_mode ===ScLanguage::GENERAL)?$data_post['page_style']:$page_model->getPageStyle(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages
        ]);
    }
    public function deleteAction()
    {
        $list_page = $this->request->get('item');
        $career_page = array();
        $msg_delete = array('error' => '', 'success' => '');
        if($list_page) {
            foreach ($list_page as $page_id) {
                $page_model = Page::getByID($page_id);
                if($page_model) {
                    $old_type_data = array(
                        'name' => $page_model->getPageName(),
                        'title' => $page_model->getPageTitle(),
                        'keyword' => $page_model->getPageKeyword(),
                        'meta_image' => $page_model->getPageMetaImage(),
                        'meta_description' => $page_model->getPageMetaDescription(),
                        'meta_keyword' => $page_model->getPageMetaKeyword(),
                        'style' => $page_model->getPageStyle(),
                        'content' => $page_model->getPageContent()
                    );
                    $new_type_data = array();
                    $career_page[$page_id] = array($old_type_data, $new_type_data);
                    $page_model->delete();
                    PageLang::deleteById($page_id);
                }
            }
        }
        if (count($career_page) > 0) {
            // delete success
            $message = 'Delete '. count($career_page) .' Page success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('career_page' => $career_page));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/dashboard/list-page');
    }
    private function getParameter(){
        $sql = "SELECT * FROM Score\Models\ScPage WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (page_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (page_name like CONCAT('%',:keyword:,'%') OR page_title like CONCAT('%',:keyword:,'%') )";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql .= " ORDER BY page_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}