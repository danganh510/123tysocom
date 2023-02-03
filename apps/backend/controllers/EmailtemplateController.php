<?php

namespace Score\Backend\Controllers;
use Score\Models\ScLanguage;
use Score\Models\ScTemplateEmailLang;
use Score\Repositories\Activity;
use Score\Repositories\EmailTemplate;
use Score\Repositories\EmailTemplateLang;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Models\ScTemplateEmail;
class EmailtemplateController extends ControllerBase
{
    public function indexAction()
    {

        $current_page = $this->request->getQuery('page');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch',array('string', 'trim'));
        $sql = "SELECT * FROM Score\Models\ScTemplateEmail WHERE 1";
        $arrParameter = array();
        if(!empty($keyword)){
            if($validator->validInt($keyword)) {
                $sql.=" AND (email_id = :keyword:)";
            } else {
                $sql.=" AND (email_type like CONCAT('%',:keyword:,'%') OR email_subject like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql.=" ORDER BY email_id DESC";
        $list_emailtemplate = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(
            array(
                'data'  => $list_emailtemplate,
                'limit' => 20,
                'page'  => $current_page,
            )
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_del')) {
            $msg_result = $this->session->get('msg_del');
            $this->session->remove('msg_del');
            $this->view->msg_del = $msg_result;
        }
        $this->view->setVars(array(
            'list_emailtemplate' => $paginator->getPaginate(),
        ));
    }
    public function createAction()
    {
        $data = array('email_id' => -1, 'email_status' => 'Y');
        if($this->request->isPost()) {
            $data = array(
                'email_id' => -1,
                'email_type' => $this->request->getPost("txtType", array('string', 'trim')),
                'email_subject' => $this->request->getPost("txtSubject", array('string', 'trim')),
                'email_content' => $this->request->getPost("txtContent"),
                'email_status' => $this->request->getPost("radStatus"),
            );
            $messages = array();
            if (empty($data['email_type'])) {
                $messages['type'] = 'Type field is required.';
            } else {
                if (EmailTemplate::checkKeyword($data['email_type'],-1)) {
                    $messages['type'] = 'Type is exists.';
                }
            }
            if (count($messages) == 0) {
                $new_emailtemplate = new ScTemplateEmail();
                $new_emailtemplate->setEmailType($data['email_type']);
                $new_emailtemplate->setEmailSubject($data['email_subject']);
                $new_emailtemplate->setEmailContent($data['email_content']);
                $new_emailtemplate->setEmailStatus($data['email_status']);
                if ($new_emailtemplate->save() === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Email Template Success');
                    $old_data = array();
                    $new_data = $data;
                    $message = '';
                    $data_log = json_encode(array('content_email_template' => array($new_emailtemplate->getEmailId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $msg_result = array('status' => 'error', 'msg' => 'Create Email Template Fail !');
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect('/dashboard/list-emailtemplate');
            }
        }
        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $data,
            'messages' => $messages,
        ));
    }
    public function editAction()
    {
        $id_emailtemplate = $this->request->getQuery('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id_emailtemplate))
        {
            return $this->response->redirect('notfound');
        }
        $emailtemplate_model = ScTemplateEmail::findFirstById($id_emailtemplate);
        if(empty($emailtemplate_model))
        {
            return $this->response->redirect('notfound');
        }
        $arr_language = array();
        $arr_translate = array();
        $messages = array();
        $data_post = array (
            'email_type' => '',
            'email_subject' => '',
            'email_content' => '',
            'email_status' => '',
        ) ;
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $languages = ScLanguage::getLanguages();
        $lang_current = $lang_default;
        foreach ($languages as $lang){
            $arr_language[$lang->getLanguageCode()] =$lang->getLanguageName();
        }
        if($this->request->isPost()) {
            if(!isset($_POST['save'])){
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode =  $_POST['save'] ;
            $data_old = array();
            if(isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if($save_mode != 'general'){
                $data_post['email_subject'] = $this->request->get("txtSubject", array('string', 'trim'));
                $data_post['email_content'] = $this->request->get("txtContent");
            }else{
                $data_post['email_type'] = $this->request->get("txtType", array('string', 'trim'));
                $data_post['email_status'] = $this->request->getPost('radStatus');

                if (empty($data_post['email_type'])) {
                    $messages['type'] = 'Type field is required.';
                } else {
                    if (EmailTemplate::checkKeyword($data_post['email_type'],$id_emailtemplate)) {
                        $messages['type'] = 'Type is exists.';
                    }
                }
            }
            if(empty($messages)) {
                switch ($save_mode) {
                    case 'general':
                        $data_old = array(
                            'email_type' => $emailtemplate_model->getEmailType(),
                            'email_status' => $emailtemplate_model->getEmailStatus(),
                        );
                        $emailtemplate_model->setEmailType($data_post['email_type']);
                        $emailtemplate_model->setEmailStatus($data_post['email_status']);
                        $result = $emailtemplate_model->update();

                        $info = "General";
                        $data_new = array(
                            'email_type' => $emailtemplate_model->getEmailType(),
                            'email_status' => $emailtemplate_model->getEmailStatus(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'email_subject' => $emailtemplate_model->getEmailSubject(),
                            'email_content' => $emailtemplate_model->getEmailContent(),
                        );
                        $emailtemplate_model->setEmailSubject($data_post['email_subject']);
                        $emailtemplate_model->setEmailContent($data_post['email_content']);
                        $result = $emailtemplate_model->update();

                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'email_subject' => $emailtemplate_model->getEmailSubject(),
                            'email_content' => $emailtemplate_model->getEmailContent(),
                        );
                        break;
                    default:
                        $emailtemplate_lang = EmailTemplateLang::findFirstByIdAndLang($id_emailtemplate, $save_mode);
                        if (!$emailtemplate_lang) {
                            $emailtemplate_lang = new ScTemplateEmailLang();
                            $emailtemplate_lang->setEmailId($id_emailtemplate);
                            $emailtemplate_lang->setEmailLangCode($save_mode);
                        }
                        $data_old = $emailtemplate_lang->toArray();
                        $emailtemplate_lang->setEmailSubject($data_post['email_subject']);
                        $emailtemplate_lang->setEmailContent($data_post['email_content']);
                        $result = $emailtemplate_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'email_lang_code' => $emailtemplate_lang->getEmailLangCode(),
                            'email_subject' => $emailtemplate_lang->getEmailSubject(),
                            'email_content' => $emailtemplate_lang->getEmailContent(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Email Template success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('Scf_email_template' => array($id_emailtemplate => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update Email Template fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'email_subject'=>($save_mode === $this->globalVariable->defaultLanguage)?$data_post['email_subject']:$emailtemplate_model->getEmailSubject(),
            'email_content' => ($save_mode === $this->globalVariable->defaultLanguage)?$data_post['email_content']:$emailtemplate_model->getEmailContent(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_emailtemplate_lang = ScTemplateEmailLang::findById($id_emailtemplate);
        foreach ($arr_emailtemplate_lang as $emailtemplate_lang){
            $item = array(
                'email_subject'=>($save_mode === $emailtemplate_lang->getEmailLangCode())?$data_post['email_subject']:$emailtemplate_lang->getEmailSubject(),
                'email_content' => ($save_mode === $emailtemplate_lang->getEmailLangCode())?$data_post['email_content']:$emailtemplate_lang->getEmailContent(),
            );
            $arr_translate[$emailtemplate_lang->getEmailLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'email_subject'=> $data_post['email_subject'],
                'email_content' => $data_post['email_content'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'email_id'=>$emailtemplate_model->getEmailId(),
            'email_type' => ($save_mode ==='general')?$data_post['email_type']:$emailtemplate_model->getEmailType(),
            'email_status' => ($save_mode ==='general')?$data_post['email_status']:$emailtemplate_model->getEmailStatus(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );
        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $formData,
            'messages' => $messages,
        ));
    }
    public function deleteAction()
    {
        $emailtemplate_checked = $this->request->getPost("item");
        if(!empty($emailtemplate_checked))
        {
            $emailtemplate_log = array();
            foreach ($emailtemplate_checked as $emailtemplate_id)
            {
                $emailtemplate_item = ScTemplateEmail::findFirstById($emailtemplate_id);
                if($emailtemplate_item)
                {
                    $msg_result = array();
                    if ($emailtemplate_item->delete() === false) {
                        $message_delete = 'Can\'t delete the Email Template Subject = '.$emailtemplate_item->getEmailSubject();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = array(
                            'email_id' => $emailtemplate_id,
                            'email_type' => $emailtemplate_item->getEmailType(),
                            'email_subject' => $emailtemplate_item->getEmailSubject(),
                            'email_content' => $emailtemplate_item->getEmailContent(),
                            'email_status' => $emailtemplate_item->getEmailStatus()
                        );
                        $emailtemplate_log[$emailtemplate_id] = $old_data;
                        EmailTemplateLang::deleteById($emailtemplate_id);
                    }
                }
            }
            if(count($emailtemplate_log) > 0) {
                $message_delete = 'Delete '. count($emailtemplate_log) .' Email Template successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('bin_email_template' => $emailtemplate_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/dashboard/list-emailtemplate");
        }
    }
}
