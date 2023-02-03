<?php

namespace Score\Backend\Controllers;

use Score\Models\ScImage;
use Score\Models\ScImageLang;
use Score\Models\ScLanguage;
use Score\Repositories\ImageLang;
use Score\Repositories\Album;
use Score\Repositories\Image;
use Score\Repositories\Language;
use Score\Repositories\Activity;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Utils\Validator;

class ImageController extends ControllerBase
{
    public function indexAction()
    {
        $album_id = $this->request->get('album_id');
        //setParam url for paginator
        $this->dispatcher->setParam("album_id", $album_id);
        $checkID = new Validator();
        if (!$checkID->validInt($album_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $album_model = Album::getByID($album_id);
        if (!$album_model) {
            $this->response->redirect('notfound');
            return;
        }
        $data = $this->getParameter($album_id);
        $list_image = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
        $paginator = new PaginatorModel(
            [
                'data' => $list_image,
                'limit' => 20,
                'page' => $current_page,
                'album_id' => $album_id,

            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
            'album_id' => $album_id,
        ));
    }

    public function createAction()
    {
        $album_id = $this->request->get('album_id');
        $checkID = new Validator();
        if (!$checkID->validInt($album_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $album_model = Album::getByID($album_id);
        if (!$album_model) {
            $this->response->redirect('notfound');
            return;
        }
        $data = array('album_id' => $album_id, 'order' => 1, 'insert_time' => $this->my->formatDateTime($this->globalVariable->curTime, false));
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'description' => $this->request->getPost("txtDesc", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'insert_time' => $this->request->getPost("txtInsertTime", array('string', 'trim')),
            );
            if (empty($data["icon"])) {
                $messages["icon"] = "Icon field is required.";
            }
            if (empty($data["insert_time"])) {
                $messages["insert_time"] = "Insert time field is required.";
            }
            if (empty($data["description"])) {
                $messages["description"] = "Description field is required.";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $new_type = new ScImage();
                $new_type->setImageAlbumId($album_id);
                $new_type->setImageIcon($data["icon"]);
                $new_type->setImageDescription($data["description"]);
                $new_type->setImageOrder($data["order"]);
                $new_type->setImageInsertTime($this->my->UTCTime(strtotime($data["insert_time"])));
                $result = $new_type->save();

                $message = "We can't store your info now: " . "<br/>";
                if ($result === true) {
                    $activity = new Activity();
                    $message = 'Create the image ID: ' . $new_type->getImageId() . ' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('bin_image' => array($new_type->getImageId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                $this->response->redirect("/dashboard/list-image?album_id=" . $album_id);
                return;

            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'album_id' => $album_id,
        ]);
    }

    public function editAction()
    {
        $album_id = $this->request->get('album_id');
        $checkID = new Validator();
        if (!$checkID->validInt($album_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $album_model = Album::getByID($album_id);
        if (!$album_model) {
            $this->response->redirect('notfound');
            return;
        }
        $image_id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($image_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $image_model = Image::getByID($image_id);
        if (!$image_model) {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'image_id' => -1,
            'image_album_id' => '',
            'image_icon' => '',
            'image_description' => '',
            'image_order' => '',
            'image_insert_time' => '',
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
            if (isset($arr_language[$save_mode])) {
                $lang_current = $save_mode;
            }
            if ($save_mode != ScLanguage::GENERAL) {
                $data_post['image_description'] = $this->request->get("txtDesc", array('string', 'trim'));
                if (empty($data_post['image_description'])) {
                    $messages[$save_mode]['description'] = 'Description field is required.';
                }
            } else {
                $data_post['image_icon'] = $this->request->get("txtIcon", array('string', 'trim'));
                $data_post['image_insert_time'] = $this->request->get("txtInsertTime", array('string', 'trim'));
                $data_post['image_order'] = $this->request->get("txtOrder", array('string', 'trim'));
                if (empty($data_post['image_insert_time'])) {
                    $messages['image_insert_time'] = 'Insert time field is required.';
                }
            }
            if (empty($messages)) {
                $messagesAzure = '';
                $activity = new Activity();
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $data_old = array(
                            'image_icon' => $image_model->getImageIcon(),
                            'image_order' => $image_model->getImageOrder(),
                            'image_insert_time' => $image_model->getImageInsertTime(),
                        );
                        $image_model->setImageInsertTime($this->my->UTCTime(strtotime($data_post['image_insert_time'])));
                        $image_model->setImageIcon($data_post['image_icon']);
                        $image_model->setImageOrder($data_post['image_order']);
                        $result = $image_model->update();
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'image_insert_time' => $image_model->getImageInsertTime(),
                            'image_icon' => $image_model->getImageIcon(),
                            'image_order' => $image_model->getImageOrder(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'image_description' => $image_model->getImageDescription(),
                        );
                        $image_model->setImageDescription($data_post['image_description']);
                        $result = $image_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'image_description' => $image_model->getImageDescription(),
                        );
                        break;
                    default:
                        $image_lang_model = ImageLang::findFirstByIdAndLang($image_id, $save_mode);
                        if (!$image_lang_model) {
                            $image_lang_model = new ScImageLang();
                            $image_lang_model->setImageId($image_id);
                            $image_lang_model->setImageLangCode($save_mode);
                        }
                        $data_old = array(
                            'image_lang_code' => $image_lang_model->getImageLangCode(),
                            'image_description' => $image_lang_model->getImageDescription(),
                        );

                        $image_lang_model->setImageDescription($data_post['image_description']);
                        $result = $image_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'image_lang_code' => $image_lang_model->getImageLangCode(),
                            'image_description' => $image_lang_model->getImageDescription(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Image success<br>") . $messagesAzure,
                        'typeMessage' => 'success'
                    );
                    //End Update Azure Search
                    $message = '';
                    $data_log = json_encode(array('bin_image_lang' => array($image_id => array($data_old, $data_new))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $messages = array(
                        'message' => "Update Image fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'image_id' => $image_model->getImageId(),
            'image_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['image_description'] : $image_model->getImageDescription(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_image_lang = ScImageLang::findById($image_id);
        foreach ($arr_image_lang as $image_lang) {
            $item = array(
                'image_id' => $image_lang->getImageId(),
                'image_description' => ($save_mode === $image_lang->getImageLangCode()) ? $data_post['image_description'] : $image_lang->getImageDescription(),

            );
            $arr_translate[$image_lang->getImageLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'image_id' => -1,
                'image_description' => $data_post['image_description'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'image_id' => $image_model->getImageId(),
            'image_icon' => ($save_mode === ScLanguage::GENERAL) ? $data_post['image_icon'] : $image_model->getImageIcon(),
            'image_insert_time' => ($save_mode === ScLanguage::GENERAL) ? $data_post['image_insert_time'] : $this->my->formatDateTime($image_model->getImageInsertTime(), false),
            'image_order' => ($save_mode === ScLanguage::GENERAL) ? $data_post['image_order'] : $image_model->getImageOrder(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );
        $messages['status'] = 'border-red';
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'album_id' => $album_id,
        ]);
    }

    public function deleteAction()
    {
        $album_id = $this->request->get('album_id');
        $checkID = new Validator();
        if (!$checkID->validInt($album_id)) {
            $this->response->redirect('notfound');
            return;
        }
        $activity = new Activity();
        $message = '';
        $list_image = $this->request->get('item');

        $bin_image = array();
        if ($list_image) {
            foreach ($list_image as $image_id) {
                $image = Image::getByAlbumAndID($album_id, $image_id);

                if ($image) {
                    $old_image_data = $image->toArray();
                    $new_image_data = array();
                    $bin_image[$image_id] = array($old_image_data, $new_image_data);
                    $image->delete();
                    ImageLang::deleteById($image_id);
                }
            }
        }
        if (count($bin_image) > 0) {
            // delete success
            $message .= 'Delete ' . count($bin_image) . ' image success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('bin_image' => $bin_image));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);

        $this->response->redirect("/dashboard/list-image?album_id=" . $album_id);
        return;
    }

    private function getParameter($id)
    {
        $sql = "SELECT * FROM Score\Models\ScImage WHERE image_album_id =:albumID: ";
        $keyword = trim($this->request->get("txtSearch"));
        $arrParameter = array('albumID' => $id);
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (image_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (image_icon like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $sql .= " ORDER BY image_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}