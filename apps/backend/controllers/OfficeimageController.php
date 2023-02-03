<?php

namespace Bincg\Backend\Controllers;
use Bincg\Models\BinOffice;
use Bincg\Models\BinOfficeImage;
use Bincg\Repositories\Activity;
use Bincg\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class OfficeimageController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        if ($data == null) {
            return $this->response->redirect('notfound');
        }
        $list_office_image = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
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
                'data'  => $list_office_image,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $this->view->setVars(array(
            'office_image' => $paginator->getPaginate(),
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $id = $this->request->get('office_id');
        $validator = new Validator();
        if ($validator->validInt($id) == false || $id < 1) {
            $this->response->redirect('notfound');
            return ;
        }
        $office_model = BinOffice::findFirstById($id);
        if(empty($office_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = array('id' => -1, 'image_office_id' => $id, 'image_active' => 'Y', 'image_order' => 1);
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'image_id' => -1,
                'image_url' => $this->request->getPost("txtUrl"), array('string', 'trim'),
                'image_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'image_active' => $this->request->getPost("radActive"),
                'image_office_id' => $id,
            );
            if (empty($data["image_url"])) {
                $messages["url"] = "Url field is required.";
            }            
            if (empty($data["image_order"])) {
                $messages["order"] = "Order field is required.";
            }elseif (!is_numeric($data["image_order"]))
            {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $new_office_image = new BinOfficeImage();
                $new_office_image->setImageUrl($data["image_url"]);
                $new_office_image->setImageOrder($data["image_order"]);
                $new_office_image->setImageActive($data["image_active"]);
                $new_office_image->setImageOfficeId($data["image_office_id"]);
                $result = $new_office_image->save();
                if ($result === true){
                    $message = 'Create Office Image ID: '.$new_office_image->getImageId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('bin_office_image' => array($new_office_image->getImageId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $message =  "We can't store your info now: <br/>";
                    foreach ($new_office_image->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-office-image?office_id=".$id);
            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages
        ]);
    }
    public function editAction()
    {

        $id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $office_image_model = BinOfficeImage::findFirstById($id);
        if(empty($office_image_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $messages = array();
        $data_post = array (
            'image_id' => $id,
            'image_url' => $office_image_model->getImageUrl(),
            'image_order' => $office_image_model->getImageOrder(),
            'image_active' => $office_image_model->getImageActive(),
        );
        if($this->request->isPost()) {
            $data_post = array(
                'image_url' => $this->request->getPost("txtUrl"), array('string', 'trim'),
                'image_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'image_active' => $this->request->getPost("radActive"),
            );
            if (empty($data_post["image_url"])) {
                $messages["url"] = "Url field is required.";
            }
            if (empty($data_post["image_order"])) {
                $messages["order"] = "Order field is required.";
            }elseif (!is_numeric($data_post["image_order"]))
            {
                $messages["order"] = "Order is not valid ";
            }
            if(empty($messages)) {
                $data_old = array(
                    'image_id' => $office_image_model->getImageId(),
                    'image_url' => $office_image_model->getImageUrl(),
                    'image_order' => $office_image_model->getImageOrder(),
                    'image_active' => $office_image_model->getImageActive(),
                    'image_office_id' => $office_image_model->getImageOfficeId(),
                );
                $msg_result = array();
                $office_image_model->setImageUrl($data_post['image_url']);
                $office_image_model->setImageOrder($data_post['image_order']);
                $office_image_model->setImageActive($data_post['image_active']);
                $result = $office_image_model->update();
                $data_new = array(
                    'image_id' => $office_image_model->getImageId(),
                    'image_url' => $office_image_model->getImageUrl(),
                    'image_order' => $office_image_model->getImageOrder(),
                    'image_active' => $office_image_model->getImageActive(),
                    'image_office_id' => $office_image_model->getImageOfficeId(),
                );
                if ($result) {
                    $message = 'Update Office Image ID: '.$id.' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $data_log = json_encode(array('bin_office_image' => array($id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $message =  "Update Office Image fail";
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-office-image?office_id=".$data_new['image_office_id']);
            }
        }
        $formData = array(
            'image_id'=> $data_post['image_id'] ,
            'image_url' =>  $data_post['image_url'] ,
            'image_order' =>$data_post['image_order'],
            'image_active' => $data_post['image_active'],
            'image_office_id' => $office_image_model->getImageOfficeId()
        );
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
        ]);
    }
    public function deleteAction ()
    {
        $list_office_image = $this->request->get('item');
        $msg_delete = array('error' => '', 'success' => '');
        if($list_office_image) {
            foreach ($list_office_image as $image_id) {
                $office_image_model = BinOfficeImage::findFirstById($image_id);
                if($office_image_model) {
                    if ($office_image_model->delete() === false) {
                        $message_delete = 'Can\'t delete Office Image ID = '.$office_image_model->getImageId();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = array(
                            'image_id'=> $office_image_model->getImageId(),
                            'image_url' =>  $office_image_model->getImageUrl(),
                            'image_order' =>$office_image_model->getImageOrder(),
                            'image_active' => $office_image_model->getImageActive(),
                            'image_office_id' => $office_image_model->getImageOfficeId()
                        );
                        $occ_log[$image_id] = $old_data;
                    }
                }
            }
        }
        if(count($occ_log) > 0) {
            // delete success
            $message = 'Delete '. count($occ_log) .' Office Image success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('dsbc_office_image' => $occ_log));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/dashboard/list-office-image?office_id='.$old_data['image_office_id']);
    }
    private function getParameter(){
        $arrParameter = array();
        $validator = new Validator();
        $id = $this->request->getQuery("office_id");
        $keyword = trim($this->request->get("txtSearch"));

        if ($validator->validInt($id) == false || $id < 1) {
            return $data = NULL;

        }
        $office_model = BinOffice::findFirstById($id);
        if(empty($office_model))
        {
            return $data = NULL;

        }
        $sql = "SELECT * FROM Bincg\Models\BinOfficeImage WHERE image_office_id = :image_office_id: ";

        $arrParameter['image_office_id'] = $id;
        $this->dispatcher->setParam("office_id",$id);

        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (image_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= "AND (image_url like CONCAT('%',:keyword:,'%') OR image_url_thumnail like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql .= " ORDER BY image_order DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}