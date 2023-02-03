<?php

namespace Bincg\Backend\Controllers;



use Bincg\Models\BinCommunicationChannel;
use Bincg\Models\BinCommunicationChannelLang;
use Bincg\Models\BinLanguage;
use Bincg\Repositories\Activity;
use Bincg\Repositories\CommunicationChannel;
use Bincg\Repositories\CommunicationChannelLang;
use Bincg\Repositories\Language;
use Bincg\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class CommunicationchannelController extends ControllerBase
{

    public function indexAction()
    {
        $msg_delete = array();
        if ($this->session->has('msg_delete')) {
            $msg_delete = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
        }

        $current_page = $this->request->getQuery('page', 'int');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch', 'trim');
        $type = $this->request->get('slcType');
        $sql = "SELECT * FROM  Bincg\Models\BinCommunicationChannel WHERE 1";
        $arrParameter = array();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (communication_channel_id = :keyword:) ";
            } else {
                $sql .= " AND (communication_channel_name like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = str_replace("'", "&#39;", $keyword);
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($type)) {
            $sql .= " AND (communication_channel_type = :type: )";
            $arrParameter['type'] = $type;
            $this->dispatcher->setParam("slcType", $type);
        }
        $sql .= " ORDER BY communication_channel_id DESC";
        $list_communication_channel = $this->modelsManager->executeQuery($sql, $arrParameter);
        $paginator = new PaginatorModel(
            [
                'data' => $list_communication_channel,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $communication_channel = new CommunicationChannel();
        $select_type = $communication_channel->getType($type);
        $this->view->setVars(array(
            'list_communication_channel' => $paginator->getPaginate(),
            'select_type' => $select_type,
            'msg_delete'  => $msg_delete
        ));
    }

    public function createAction()
    {
        $data = array(
            'communication_channel_id' => -1,
            'communication_channel_name' => "",
            'communication_channel_type' => "",
            'communication_channel_icon' => "",
            'communication_channel_active' => 'Y',
            'communication_channel_order' => 1
        );
        if ($this->request->isPost()) {
            $data = array(
                'communication_channel_id' => -1,
                'communication_channel_type' => $this->request->getPost('slcType'),
                'communication_channel_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'communication_channel_icon' => $this->request->getPost('txtIcon', array('string', 'trim')),
                'communication_channel_active' => $this->request->getPost('radActive'),
                'communication_channel_order' => $this->request->getPost('txtOrder', array('trim')),
            );
            $messages = array();
            if (empty($data['communication_channel_name'])) {
                $messages['name'] = 'Name field is required.';
            } else {
                if (!empty($data['communication_channel_type'])) {
                    $checkName = CommunicationChannel::checkName($data['communication_channel_name'], $data['communication_channel_type'], -1);
                    if (!empty($checkName)) {
                        $messages['name'] = 'Name is exists.';
                    }
                }
            }
            if (empty($data['communication_channel_type'])) {
                $messages["type"] = 'Type field is required.';
            }
            if (empty($data['communication_channel_order'])) {
                $messages["order"] = "Order field is required.";
            } else {
                if (!is_numeric($data["communication_channel_order"])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_content_communication_channel = new BinCommunicationChannel();
                $new_content_communication_channel->setCommunicationChannelType($data['communication_channel_type']);
                $new_content_communication_channel->setCommunicationChannelName($data['communication_channel_name']);
                $new_content_communication_channel->setCommunicationChannelIcon($data['communication_channel_icon']);
                $new_content_communication_channel->setCommunicationChannelActive($data['communication_channel_active']);
                $new_content_communication_channel->setCommunicationChannelOrder($data['communication_channel_order']);
                $result = $new_content_communication_channel->save();
                if ($result === false) {
                    $message = "Create Communication Channel Fail !";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $activity = new Activity();
                    $status = 'success';
                    $message = 'Create Communication Channel Success<br>';

                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $message = '';
                    $data_log = json_encode(array(
                        'bin_communication_channel' => array(
                            $new_content_communication_channel->getCommunicationChannelId() => array(
                                $old_data,
                                $new_data
                            )
                        )
                    ));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message,
                        $data_log);
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-communication-channel");
            }
        }
        $messages["status"] = "border-red";
        $type = new CommunicationChannel();
        $select_type = $type->getType($data['communication_channel_type']);
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }
    public function editAction()
    {
        $communication_channel_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($communication_channel_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $communication_channel_model = BinCommunicationChannel::findFirstById($communication_channel_id);
        if(!$communication_channel_model)
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array (
            'communication_channel_id' => -1,
            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName(),
            'communication_channel_icon' => '',
            'communication_channel_order' => '',
            'communication_channel_active' => ''
        ) ;
        $save_mode = '';
        $lang_default = $this->globalVariable->defaultLanguage;
        $lang_current = $lang_default;
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
            if($save_mode != BinLanguage::GENERAL) {
                $data_post['communication_channel_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                if(empty($data_post['communication_channel_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }else{
                    $check_name = CommunicationChannel::checkName($data_post['communication_channel_name'],$data_post['communication_channel_type'],$communication_channel_id);
                    if (!empty($check_name)){
                        $messages[$save_mode]['name'] = 'Name is exists.';
                    }
                }
            } else {
                $data_post['communication_channel_type'] = $this->request->getPost('slcType');
                $data_post['communication_channel_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data_post['communication_channel_active'] = $this->request->getPost('radActive');
                $data_post['communication_channel_order'] = $this->request->getPost('txtOrder', array('string','trim'));

                if (empty($data_post['communication_channel_type'])) {
                    $messages["type"] = 'Type field is required.';
                }else{
                    $check_type = CommunicationChannel::checkName($data_post['communication_channel_name'],$data_post['communication_channel_type'],$communication_channel_id);
                    if (!empty($check_type)){
                        $messages["type"] = 'Type is exists.';
                    }
                }
                if (empty($data_post['communication_channel_order'])) {
                    $messages["order"] = "Order field is required.";
                } else if (!is_numeric($data_post['communication_channel_order'])) {
                    $messages["order"] = "Order is not valid ";
                }
            }
            if(empty($messages)) {
                switch ($save_mode) {
                    case BinLanguage::GENERAL:
                        $data_old = array(
                            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
                            'communication_channel_icon' => $communication_channel_model->getCommunicationChannelIcon(),
                            'communication_channel_active' => $communication_channel_model->getCommunicationChannelActive(),
                            'communication_channel_order' => $communication_channel_model->getCommunicationChannelOrder(),
                        );
                        $communication_channel_model->setCommunicationChannelType($data_post['communication_channel_type']);
                        $communication_channel_model->setCommunicationChannelIcon($data_post['communication_channel_icon']);
                        $communication_channel_model->setCommunicationChannelActive($data_post['communication_channel_active']);
                        $communication_channel_model->setCommunicationChannelOrder($data_post['communication_channel_order']);
                        $result = $communication_channel_model->update();
                        $info = BinLanguage::GENERAL;
                        $data_new = array(
                            'communication_channel_type' => $communication_channel_model->getCommunicationChannelType(),
                            'communication_channel_icon' => $communication_channel_model->getCommunicationChannelIcon(),
                            'communication_channel_active' => $communication_channel_model->getCommunicationChannelActive(),
                            'communication_channel_order' => $communication_channel_model->getCommunicationChannelOrder(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName()
                        );
                        $communication_channel_model->setCommunicationChannelName($data_post['communication_channel_name']);

                        $result = $communication_channel_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'communication_channel_name' => $communication_channel_model->getCommunicationChannelName()
                        );
                        break;
                    default:
                        $data_content_communication_channel_lang = CommunicationChannelLang::findFirstByIdAndLang($communication_channel_id, $save_mode);
                        if (empty($data_content_communication_channel_lang)) {
                            $data_content_communication_channel_lang = new BinCommunicationChannelLang();
                            $data_content_communication_channel_lang->setCommunicationChannelId($communication_channel_id);
                            $data_content_communication_channel_lang->setCommunicationChannelLangCode($save_mode);
                        }else {
                            $data_old = array(
                                'communication_channel_name' => $data_content_communication_channel_lang->getCommunicationChannelName(),
                                'communication_channel_lang_code' => $data_content_communication_channel_lang->getCommunicationChannelLangCode(),
                            );
                        }
                        $data_content_communication_channel_lang->setCommunicationChannelName($data_post['communication_channel_name']);
                        $result = $data_content_communication_channel_lang->save();
                        $info = $arr_language[$save_mode];

                        $data_new = array(
                            'communication_channel_name' => $data_content_communication_channel_lang->getCommunicationChannelName(),
                            'communication_channel_lang_code' => $data_content_communication_channel_lang->getCommunicationChannelLangCode(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Communication Channel success<br>"),
                        'typeMessage' => 'success'
                    );
                    $message = '';
                    $data_log = json_encode(array('bin_communication_channel_lang' => array($communication_channel_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update Communication Channel fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'communication_channel_id' =>$communication_channel_model->getCommunicationChannelId(),
            'communication_channel_name'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['communication_channel_name']:$communication_channel_model->getCommunicationChannelName()
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_communication_channel_lang = BinCommunicationChannelLang::findById($communication_channel_id);
        foreach ($arr_communication_channel_lang as $communication_channel_lang){
            $item = array(
                'communication_channel_id'=>$communication_channel_lang->getCommunicationChannelId(),
                'communication_channel_name'=>($save_mode === $communication_channel_lang->getCommunicationChannelLangCode())?$data_post['communication_channel_name']:$communication_channel_lang->getCommunicationChannelName()
            );
            $arr_translate[$communication_channel_lang->getCommunicationChannelLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'communication_channel_id'=> -1,
                'communication_channel_name'=> $data_post['communication_channel_name']
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'communication_channel_id'=>$communication_channel_model->getCommunicationChannelId(),
            'communication_channel_type' => ($save_mode ===BinLanguage::GENERAL)?$data_post['communication_channel_type']:$communication_channel_model->getCommunicationChannelType(),
            'communication_channel_icon' => ($save_mode ===BinLanguage::GENERAL)?$data_post['communication_channel_icon']:$communication_channel_model->getCommunicationChannelIcon(),
            'communication_channel_active' => ($save_mode ===BinLanguage::GENERAL)?$data_post['communication_channel_active']:$communication_channel_model->getCommunicationChannelActive(),
            'communication_channel_order' => ($save_mode ===BinLanguage::GENERAL)?$data_post['communication_channel_order']:$communication_channel_model->getCommunicationChannelOrder(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $messages["status"] = "border-red";
        $type = new CommunicationChannel();
        $select_type = $type->getType($data_post['communication_channel_type']);
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type
        ]);
    }
    public function deleteAction()
    {
        $activity = new Activity();
        $message = '';
        $list_communication_channel = $this->request->get('item');
        $bin_communication_channel = array();
        $msg_delete = array('success' => '');
        if ($list_communication_channel) {
            foreach ($list_communication_channel as $communication_channel_id) {
                $communication_channel = CommunicationChannel::getByID($communication_channel_id);
                if ($communication_channel) {
                    $old_communication_channel_data = $communication_channel->toArray();
                    $new_communication_channel_data = array();
                    $bin_communication_channel[$communication_channel_id] = array($old_communication_channel_data, $new_communication_channel_data);
                    $communication_channel->delete();
                    CommunicationChannelLang::deleteById($communication_channel_id);
                }
            }
        }
        if (count($bin_communication_channel) > 0) {
            // delete success
            $message .= 'Delete ' . count($bin_communication_channel) . ' communication chanel success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('bin_communication_channel' => $bin_communication_channel));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        $this->response->redirect('/dashboard/list-communication-channel');
        return;
    }
}

