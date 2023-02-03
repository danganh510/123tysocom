<?php
namespace Bincg\Backend\Controllers;

use Bincg\Models\BinArticle;
use Bincg\Models\BinType;
use Bincg\Models\BinTypeLang;
use Bincg\Repositories\Activity;
use Bincg\Repositories\Article;
use Bincg\Repositories\Language;
use Bincg\Repositories\Type;
use Bincg\Repositories\TypeLang;
use Bincg\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Bincg\Models\BinLanguage;
class TypeController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_type = $this->modelsManager->executeQuery($data['sql'],$data['para']);
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
        $type = new Type();
        $type_parent_id = isset($data["para"]["type_parent_id"])?$data["para"]["type_parent_id"]:0;
        $select_type = $type->getComboboxType("",0,$type_parent_id);
        $paginator = new PaginatorModel(

            [
                'data'  => $list_type,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );//echo "<pre>";var_dump($paginator);exit;
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'select_type' => $select_type,
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $data = array('id' => -1, 'active' => 'Y', 'parent_id' => 0, 'order' => 1);
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'parent_id' => $this->request->getPost("slcType"),
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'meta_keyword' => $this->request->getPost("txtMetakeyword", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetadescription", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
            );
            $type = new Type();
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
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
            if (empty($data["keyword"])) {
                $messages["keyword"] = "Keyword field is required.";
            } else if ($type->checkKeyword($data["keyword"], $data['parent_id'], -1)) {
                $messages["keyword"] = "Keyword is exists!";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_type = new BinType();
                $new_type->setTypeParentId($data["parent_id"]);
                $new_type->setTypeName($data["name"]);
                $new_type->setTypeTitle($data["title"]);
                $new_type->setTypeKeyword($data["keyword"]);
                $new_type->setTypeMetaKeyword($data["meta_keyword"]);
                $new_type->setTypeMetaDescription($data["meta_description"]);
                $new_type->setTypeOrder($data["order"]);
                $new_type->setTypeActive($data["active"]);
                $result = $new_type->save();
                $message = "We can't store your info now: \n";
                $data_log = json_encode(array());
                if ($result === true){
                    $message    = 'Create the type with ID: '.$new_type->getTypeId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data   = array();
                    $new_data   = $data;
                    $data_log   = json_encode(array('content_type' => array($new_type->getTypeId() => array($old_data, $new_data))));
                }else{
                    foreach ($new_type->getMessages() as $msg) {
                         $message.= $msg."\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-type");
            }
        }
        $type = new Type();
        $select_type = $type->getComboboxType("",0, $data["parent_id"]);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_type' => $select_type,
        ]);
    }
    public function editAction()
    {
        $id_type = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id_type)){
            $this->response->redirect('notfound');
            return ;
        }
        $type_model = BinType::findFirstById($id_type);
        if(empty($type_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data = array(
            'type_id' => -1,
            'type_parent_id' => '',
            'type_name' => '',
            'type_title' => '',
            'type_keyword' => '',
            'type_meta_keyword' => '',
            'type_meta_description' => '',
            'type_active' => '',
            'type_order' => '',
        );
        $save_mode = '';
        $lang_current = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        if($this->request->isPost()) {
            if(!isset($_POST['save'])){
                $this->view->disable();
                $this->response->redirect("notfound");
                return;
            }
            $save_mode = $_POST['save'];
            $data_old = array();
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if($save_mode != BinLanguage::GENERAL){
                $data['type_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data['type_title'] = $this->request->get("txtTitle", array('string', 'trim'));
                $data['type_meta_keyword'] = $this->request->get("txtMetaKeyword", array('string', 'trim'));
                $data['type_meta_description'] = $this->request->get("txtMetaDescription", array('string', 'trim'));
                if (empty($data['type_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data['type_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data['type_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data['type_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            }else {
                $data['type_parent_id'] = $this->request->getPost('txtType');
                $data['type_keyword'] = $this->request->get("txtKeyword", array('string', 'trim'));
                $data['type_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                $data['type_active'] = $this->request->getPost('radActive');
                $type = new Type();
                if (empty($data['type_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } else if ($type->checkKeyword($data['type_keyword'], $data['type_parent_id'], $id_type)) {
                    $messages['keyword'] = 'Keyword is exists.';
                }
                if (empty($data['type_order'])) {
                    $messages['order'] = 'Order field is required.';
                } else if (!is_numeric($data['type_order'])) {
                    $messages['order'] = 'Order is not valid.';
                }
            }
            if (empty($messages)) {
                switch ($save_mode) {
                    case BinLanguage::GENERAL:
                        $data_old = array(
                            'type_parent_id' => $type_model->getTypeParentId(),
                            'type_keyword' => $type_model->getTypeKeyword(),
                            'type_order' => $type_model->getTypeOrder(),
                            'type_active' => $type_model->getTypeActive(),
                        );
                        $type_model->setTypeParentId($data['type_parent_id']);
                        $type_model->setTypeKeyword($data['type_keyword']);
                        $type_model->setTypeOrder($data['type_order']);
                        $type_model->setTypeActive($data['type_active']);
                        $result = $type_model->update();
                        $info = "General";
                        $data_new = array(
                            'type_parent_id' => $type_model->getTypeParentId(),
                            'type_keyword' => $type_model->getTypeKeyword(),
                            'type_order' => $type_model->getTypeOrder(),
                            'type_active' => $type_model->getTypeActive(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage:
                        $data_old = array(
                            'type_name' => $type_model->getTypeName(),
                            'type_title' => $type_model->getTypeTitle(),
                            'type_meta_keyword' => $type_model->getTypeMetaKeyword(),
                            'type_meta_description' => $type_model->getTypeMetaDescription(),
                        );
                        $type_model->setTypeName($data['type_name']);
                        $type_model->setTypeTitle($data['type_title']);
                        $type_model->setTypeMetaKeyword($data['type_meta_keyword']);
                        $type_model->setTypeMetaDescription($data['type_meta_description']);
                        $result = $type_model->update();

                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'type_name' => $type_model->getTypeName(),
                            'type_title' => $type_model->getTypeTitle(),
                            'type_meta_keyword' => $type_model->getTypeMetaKeyword(),
                            'type_meta_description' => $type_model->getTypeMetaDescription(),
                        );
                        break;
                    default:
                        $type_lang = TypeLang::findFirstByIdLang($id_type, $save_mode);
                        if (!$type_lang) {
                            $type_lang = new BinTypeLang();
                            $type_lang->setTypeId($id_type);
                            $type_lang->setTypeLangCode($save_mode);
                        }
                        $data_old = $type_model->toArray();
                        $type_lang->setTypeName($data['type_name']);
                        $type_lang->setTypeTitle($data['type_title']);
                        $type_lang->setTypeMetaKeyword($data['type_meta_keyword']);
                        $type_lang->setTypeMetaDescription($data['type_meta_description']);
                        $result = $type_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'type_lang_code' => $type_lang->getTypeLangCode(),
                            'type_name' => $type_lang->getTypeName(),
                            'type_title' => $type_lang->getTypeTitle(),
                            'type_meta_keyword' => $type_lang->getTypeMetaKeyword(),
                            'type_meta_description' => $type_lang->getTypeMetaDescription(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " update type success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('content_type' => array($id_type => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update type fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'type_id' =>$type_model->getTypeId(),
            'type_name'=>($save_mode === $this->globalVariable->defaultLanguage)?$data['type_name']:$type_model->getTypeName(),
            'type_title' => ($save_mode === $this->globalVariable->defaultLanguage)?$data['type_title']:$type_model->getTypeTitle(),
            'type_meta_keyword'=>($save_mode === $this->globalVariable->defaultLanguage)?$data['type_meta_keyword']:$type_model->getTypeMetaKeyword(),
            'type_meta_description' => ($save_mode === $this->globalVariable->defaultLanguage)?$data['type_meta_description']:$type_model->getTypeMetaDescription(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;
        $arr_type_lang = BinTypeLang::findById($id_type);
        foreach ($arr_type_lang as $type_lang){
            $item = array(
                'type_id'=> $type_lang->getTypeId(),
                'type_name'=>($save_mode === $type_lang->getTypeLangCode())?$data['type_name']:$type_lang->getTypeName(),
                'type_title' => ($save_mode === $type_lang->getTypeLangCode())?$data['type_title']:$type_lang->getTypeTitle(),
                'type_meta_keyword'=>($save_mode === $type_lang->getTypeLangCode())?$data['type_meta_keyword']:$type_lang->getTypeMetaKeyword(),
                'type_meta_description' => ($save_mode === $type_lang->getTypeLangCode())?$data['type_meta_description']:$type_lang->getTypeMetaDescription(),
            );
            $arr_translate[$type_lang->getTypeLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'type_id'=> -1,
                'type_name'=> $data['type_name'],
                'type_title' => $data['type_title'],
                'type_meta_keyword'=> $data['type_meta_keyword'],
                'type_meta_description' => $data['type_meta_description'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'type_id'=>$type_model->getTypeId(),
            'type_parent_id' => ($save_mode === BinLanguage::GENERAL )?$data['type_parent_id']:$type_model->getTypeParentId(),
            'type_keyword' => ($save_mode === BinLanguage::GENERAL)?$data['type_keyword']:$type_model->getTypeKeyword(),
            'type_order' => ($save_mode === BinLanguage::GENERAL)?$data['type_order']:$type_model->getTypeOrder(),
            'type_active' => ($save_mode === BinLanguage::GENERAL)?$data['type_active']:$type_model->getTypeActive(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $type = new Type();
        $select_type = $type->getComboboxType("",0,$formData['type_parent_id']);
        $messages['status'] = 'border-red';
        $this->view->setVars(array(
            'formData' => $formData,
            'messages' => $messages,
            'select_type' => $select_type,
        ));
    }
    public function deleteAction()
    {
        $list_type = $this->request->get('item');
        $Binnet_type = array();
        $msg_delete = array('error' => '', 'success' => '');
        if($list_type) {
            foreach ($list_type as $type_id) {
                $type = BinType::findFirstById($type_id);

                if( $type ) {
                    $childItem = Type::getFirstChild($type->getTypeId());
                    $articleItem = Article::getFirstByType($type->getTypeId());
                    $table_names = array();
                    $message_temp = "Can't delete the Type Name = ".$type->getTypeName().". Because It's exist in";

                    if($childItem){
                        $table_names[] = " Type";
                    }
                    if($articleItem){
                        $table_names[] = " Article";
                    }
                    if(empty($table_names)){
                        $old_type_data = $type->toArray();
                        $new_type_data = array();
                        $Binnet_type[$type_id] = array($old_type_data, $new_type_data);
                        $type->delete();
                        TypeLang::deleteById($type_id);
                    }
                    else {
                        $msg_delete['error'] .= $message_temp. implode(", ",$table_names)."<br>";
                    }
                }
            }
        }
        if (count($Binnet_type) > 0 ) {
            // delete success
            $message = 'Delete '. count($Binnet_type) .' type success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('content_type' => $Binnet_type));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/dashboard/list-type');
    }

    private function getParameter(){
        $sql = "SELECT * FROM Bincg\Models\BinType WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $type = $this->request->get("slType");
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (type_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (type_name like CONCAT('%',:keyword:,'%') OR type_keyword like CONCAT('%',:keyword:,'%') )";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $validator = new Validator();
        if(!empty($type)){
            if($validator->validInt($type)==false)  {
                $this->response->redirect("/notfound");
            }
            $sql.=" AND type_parent_id = :type_parent_id:";
            $arrParameter["type_parent_id"] = $type;
            $this->dispatcher->setParam("slType", $type);
        }
        $sql.=" ORDER BY type_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}