<?php

namespace Score\Backend\Controllers;

use Score\Models\ScNewspaper;
use Score\Repositories\Newspaper;
use Score\Repositories\NewspaperArticle;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;

class NewspaperController extends ControllerBase
{
    public function indexAction()
    {
        $current_page = $this->request->getQuery('page', 'int');
        $validator = new Validator();
        $keyword = $this->request->get('txtSearch','trim');
        $sql = "SELECT * FROM Score\Models\ScNewspaper WHERE 1";
        $arrParameter = array();

        if(!empty($keyword)){
            if($validator->validInt($keyword)) {
                $sql.=" AND (newspaper_id = :keyword:) ";
            } else {
                $sql.=" AND (newspaper_name like CONCAT('%',:keyword:,'%'))";
            }
            $arrParameter['keyword'] = $keyword;
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql.=" ORDER BY newspaper_id DESC";


        $list_newspaper = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(
            [
                'data'  => $list_newspaper,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_delete')) {
            $msg_result = $this->session->get('msg_delete');
            $this->session->remove('msg_delete');
            $this->view->msg_delete = $msg_result;
        }
        $this->view->setVars(array(
            'list_newspaper' => $paginator->getPaginate(),
        ));
    }

    public function createAction()
    {
        $this->view->pick($this->dispatcher->getControllerName().'/model');
        $data = array(
            'newspaper_id' => -1,
            'newspaper_active' => 'Y',
            'newspaper_order' => 1,
        );
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'newspaper_id' => -1,
                'newspaper_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'newspaper_logo' => $this->request->getPost('txtLogo', array('string', 'trim')),
                'newspaper_keyword' => $this->request->getPost('txtKeyword', array('string', 'trim')),
                'newspaper_title' => $this->request->getPost('txtTitle', array('string', 'trim')),
                'newspaper_link' => $this->request->getPost('txtLink', array('string', 'trim')),
                'newspaper_meta_keyword' => $this->request->getPost('txtMetaKey', array('string', 'trim')),
                'newspaper_meta_description' => $this->request->getPost('txtMetaDesc', array('string', 'trim')),
                'newspaper_meta_image' => $this->request->getPost('txtMetaImage', array('string', 'trim')),
                'newspaper_active' => $this->request->getPost('radActive', array('string', 'trim')),
                'newspaper_order' => $this->request->getPost('txtOrder', array('string', 'trim')),
                'newspaper_insert_time' => $this->globalVariable->curTime,
                'newspaper_update_time' => $this->globalVariable->curTime,
            );
            if(empty($data['newspaper_name'])) {
                $messages['name'] = 'Name field is required.';
            }
            if(empty($data['newspaper_title'])) {
                $messages['title'] = 'Title field is required.';
            }
            if(empty($data['newspaper_keyword'])) {
                $messages['keyword'] = 'Keyword field is required.';
            } else {
                if((Newspaper::checkKeyword($data['newspaper_keyword'], -1))) {
                    $messages["keyword"] = "Keyword is exists.";
                }
            }
            if(empty($data['newspaper_meta_keyword'])) {
                $messages['meta_keyword'] = 'Meta keyword field is required.';
            }
            if(empty($data['newspaper_meta_description'])) {
                $messages['meta_description'] = 'Meta description field is required.';
            }

            if (empty($data['newspaper_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["newspaper_order"])) {
                $messages["order"] = "Order is not valid ";
            }

            if(count($messages) == 0)
            {
                $msg_result = array();
                $new_content_newspaper = new ScNewspaper();
                foreach ($data as $key => $value){
                    $new_content_newspaper->$key = $value;
                }
                $result = $new_content_newspaper->save();

                if ($result === false) {
                    $message = "We can't store your info now: <br/>";
                    foreach ($new_content_newspaper->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Newspaper ID: '.$new_content_newspaper->getNewspaperId().' Success');
                }
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-newspaper");
            }
        }
        $messages['status'] = 'border-red';
        $this->view->setVars([
            'title' => 'Newspaper Create',
            'formData' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $this->view->pick($this->dispatcher->getControllerName().'/model');
        $id_newspaper = $this->request->getQuery("id");
        $checkID = new Validator();
        if(!$checkID->validInt($id_newspaper))
        {
            return $this->response->redirect('notfound');
        }
        $newspaper_model = Newspaper::findFirstById($id_newspaper);
        if(empty($newspaper_model))
        {
            return $this->response->redirect('notfound');
        }
        $data = $newspaper_model->toArray();
        $messages = array();

        if($this->request->isPost()) {
            $data = array(
                'newspaper_id' => $id_newspaper,
                'newspaper_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'newspaper_logo' => $this->request->getPost('txtLogo', array('string', 'trim')),
                'newspaper_keyword' => $this->request->getPost('txtKeyword', array('string', 'trim')),
                'newspaper_title' => $this->request->getPost('txtTitle', array('string', 'trim')),
                'newspaper_link' => $this->request->getPost('txtLink', array('string', 'trim')),
                'newspaper_meta_keyword' => $this->request->getPost('txtMetaKey', array('string', 'trim')),
                'newspaper_meta_description' => $this->request->getPost('txtMetaDesc', array('string', 'trim')),
                'newspaper_meta_image' => $this->request->getPost('txtMetaImage', array('string', 'trim')),
                'newspaper_active' => $this->request->getPost('radActive', array('string', 'trim')),
                'newspaper_order' => $this->request->getPost('txtOrder', array('string', 'trim')),
                'newspaper_update_time' => $this->globalVariable->curTime,
            );
            if(empty($data['newspaper_name'])) {
                $messages['name'] = 'Name field is required.';
            }
            if(empty($data['newspaper_title'])) {
                $messages['title'] = 'Title field is required.';
            }
            if(empty($data['newspaper_keyword'])) {
                $messages['keyword'] = 'Keyword field is required.';
            } else {
                if((Newspaper::checkKeyword($data['newspaper_keyword'], $data['newspaper_id']))) {
                    $messages["keyword"] = "Keyword is exists.";
                }
            }
            if(empty($data['newspaper_meta_keyword'])) {
                $messages['meta_keyword'] = 'Meta keyword field is required.';
            }
            if(empty($data['newspaper_meta_description'])) {
                $messages['meta_description'] = 'Meta description field is required.';
            }
            if (empty($data['newspaper_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["newspaper_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if(count($messages) == 0)
            {
                foreach ($data as $key => $value){
                    $newspaper_model->$key = $value;
                }
                $result = $newspaper_model->save();
                if ($result === false) {
                    $message = "We can't store your info now: <br/>";
                    foreach ($newspaper_model->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                } else {
                    $msg_result = array('status' => 'success', 'msg' => 'Edit Newspaper Success');
                }
                $this->session->set('msg_result', $msg_result);
            }
        }
        $messages['status'] = 'border-red';
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        $this->view->setVars([
            'title' => 'Newspaper Edit',
            'formData' => $data,
            'messages' => $messages,
        ]);
    }
    public function deleteAction()
    {
        $items_checked = $this->request->getPost("item");
        if (!empty($items_checked)) {
            $msg_result = array();
            $count_delete = 0;
            foreach ($items_checked as $id) {
                $table = array();
                $item = Newspaper::findFirstById($id);
                if ($item) {
                    $newspaperArticle = NewspaperArticle::findFirstByNewspaperArticle($id);
                    if ($newspaperArticle) $table['article'] = 'Newspaper Article';
                    if (count($table) > 0) {
                        $message_delete = 'Can\'t delete the Newspaper Name : ' . $item->getNewspaperName().' Because has item in ' . implode(', ',$table)."<br>";
                        $msg_delete['status'] = 'error';
                        $msg_delete['msg'] .= $message_delete;
                    } else {
                        if ($item->delete() === false) {
                            $message_delete = 'Can\'t delete Newspaper ID = ' . $item->getNewspaperId() . "<br>";
                            $msg_result['status'] = 'error';
                            $msg_result['msg'] .= $message_delete;
                        } else {
                            $count_delete++;
                        }
                    }
                }
            }
            if ($count_delete > 0) {
                $message_delete = 'Delete ' . $count_delete . ' Newspaper successfully.' . "<br>";
                $msg_result['status'] = 'success';
                $msg_result['msg'] .= $message_delete;
            }
            $this->session->set('msg_result', $msg_result);
            $this->session->set('msg_delete', $msg_delete);
            return $this->response->redirect('/dashboard/list-newspaper');
        }
    }


}
