<?php

namespace Bincg\Backend\Controllers;

use Bincg\Models\BinAlbum;
use Bincg\Models\BinAlbumLang;
use Bincg\Models\BinLanguage;
use Bincg\Repositories\AlbumLang;
use Bincg\Repositories\ALbum;
use Bincg\Repositories\Image;
use Bincg\Repositories\Language;
use Bincg\Repositories\Activity;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Bincg\Utils\Validator;

class AlbumController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_album = $this->modelsManager->executeQuery($data['sql'], $data['para']);
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
                'data' => $list_album,
                'limit' => 20,
                'page' => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
        ));
    }

    public function createAction()
    {
        $data = array('id' => -1, 'active' => 'Y', 'is_home' => 'N', 'insert_time' => $this->my->formatDateTime($this->globalVariable->curTime, false));
        $messages = array();
        if ($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'description' => $this->request->getPost("txtDesc", array('string', 'trim')),
                'insert_time' => $this->request->getPost("txtInsertTime", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
                'is_home' => $this->request->getPost("radIsHomeActive"),
            );
            $album_repo = new Album();
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["insert_time"])) {
                $messages["insert_time"] = "Insert time field is required.";
            }
            if (empty($data["description"])) {
                $messages["description"] = "Description field is required.";
            }
            if (empty($data["keyword"])) {
                $messages["keyword"] = "Keyword field is required.";
            } elseif ($album_repo->checkKeyword($data["keyword"], -1)) {
                $messages["keyword"] = "Keyword is exists!";
            }
            if (count($messages) == 0) {
                $new_type = new BinAlbum();
                $new_type->setAlbumName($data["name"]);
                $new_type->setAlbumKeyword($data["keyword"]);
                $new_type->setAlbumDescription($data["description"]);
                $new_type->setAlbumActive($data["active"]);
                $new_type->setAlbumIsHome($data["is_home"]);
                $new_type->setAlbumInsertTime($this->my->UTCTime(strtotime($data["insert_time"])));
                $result = $new_type->save();

                $message = "We can't store your info now: " . "<br/>";
                if ($result === true) {
                    $activity = new Activity();
                    $message = 'Create the album ID: ' . $new_type->getAlbumId() . ' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('bin_album' => array($new_type->getAlbumId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result', $msg_result);
                $this->response->redirect("/dashboard/list-album");
                return;

            }
        }
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
        ]);
    }

    public function editAction()
    {
        $album_id = $this->request->get('id');
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
        $arr_translate = array();
        $messages = array();
        $data_post = array(
            'album_id' => -1,
            'album_name' => '',
            'album_keyword' => '',
            'album_description' => '',
            'album_insert_time' => '',
            'album_active' => '',
            'album_is_home' => '',

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
            if ($save_mode != BinLanguage::GENERAL) {
                $data_post['album_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data_post['album_description'] = $this->request->get("txtDesc", array('string', 'trim'));
                if (empty($data_post['album_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['album_description'])) {
                    $messages[$save_mode]['description'] = 'Description field is required.';
                }
            } else {
                $data_post['album_keyword'] = $this->request->get("txtKeyword", array('string', 'trim'));
                $data_post['album_insert_time'] = $this->request->get("txtInsertTime", array('string', 'trim'));
                $data_post['album_active'] = $this->request->getPost('radActive');
                $data_post['album_is_home'] = $this->request->getPost('radIsHomeActive');
                if (empty($data_post['album_insert_time'])) {
                    $messages['album_insert_time'] = 'Insert time field is required.';
                }
                $album_repo = new Album();
                if (empty($data_post['album_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } elseif ($album_repo->checkKeyword($data_post["album_keyword"], $album_id)) {
                    $messages['keyword'] = "Keyword is exists!";
                }
            }
            if (empty($messages)) {
                $action = '';
                $arrLog = array();
                $messageLog = '';
                $messagesAzure = '';
                $activity = new Activity();
                switch ($save_mode) {
                    case BinLanguage::GENERAL:
                        $arr_album_lang = BinAlbumLang::findById($album_id);
                        $data_old = array(
                            'album_keyword' => $album_model->getAlbumKeyword(),
                            'album_insert_time' => $album_model->getAlbumInsertTime(),
                            'album_active' => $album_model->getAlbumActive(),
                            'album_is_home' => $album_model->getAlbumIsHome(),
                        );
                        $album_model->setAlbumInsertTime($this->my->UTCTime(strtotime($data_post['album_insert_time'])));
                        $album_model->setAlbumKeyword($data_post['album_keyword']);
                        $album_model->setAlbumActive($data_post['album_active']);
                        $album_model->setAlbumIsHome($data_post['album_is_home']);
                        $result = $album_model->update();
                        $info = BinLanguage::GENERAL;
                        $data_new = array(
                            'album_insert_time' => $album_model->getAlbumInsertTime(),
                            'album_keyword' => $album_model->getAlbumKeyword(),
                            'album_active' => $album_model->getAlbumActive(),
                            'album_is_home' => $album_model->getAlbumIsHome(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'album_name' => $album_model->getAlbumName(),
                            'album_keyword' => $album_model->getAlbumKeyword(),
                            'album_description' => $album_model->getAlbumDescription(),
                        );
                        $album_model->setAlbumName($data_post['album_name']);
                        $album_model->setAlbumDescription($data_post['album_description']);
                        $result = $album_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'album_name' => $album_model->getAlbumName(),
                            'album_description' => $album_model->getAlbumDescription(),
                        );
                        break;
                    default:
                        $album_lang_model = AlbumLang::findFirstByIdAndLang($album_id, $save_mode);
                        if (!$album_lang_model) {
                            $album_lang_model = new BinAlbumLang();
                            $album_lang_model->setAlbumId($album_id);
                            $album_lang_model->setAlbumLangCode($save_mode);
                        }
                        $data_old = array(
                            'album_lang_code' => $album_lang_model->getAlbumLangCode(),
                            'album_name' => $album_lang_model->getAlbumName(),
                            'album_description' => $album_lang_model->getAlbumDescription(),
                        );
                        $album_lang_model->setAlbumName(($data_post['album_name']));
                        $album_lang_model->setAlbumDescription($data_post['album_description']);
                        $result = $album_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'album_lang_code' => $album_lang_model->getAlbumLangCode(),
                            'album_name' => $album_lang_model->getAlbumName(),
                            'album_description' => $album_lang_model->getAlbumDescription(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Album success<br>") . $messagesAzure,
                        'typeMessage' => 'success'
                    );
                    //End Update Azure Search
                    $message = '';
                    $data_log = json_encode(array('bin_album_lang' => array($album_id => array($data_old, $data_new))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    $messages = array(
                        'message' => "Update Album fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'album_id' => $album_model->getAlbumId(),
            'album_name' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['album_name'] : $album_model->getAlbumName(),
            'album_description' => ($save_mode === $this->globalVariable->defaultLanguage) ? $data_post['album_description'] : $album_model->getAlbumDescription(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_album_lang = BinAlbumLang::findById($album_id);
        foreach ($arr_album_lang as $album_lang) {
            $item = array(
                'album_id' => $album_lang->getAlbumId(),
                'album_name' => ($save_mode === $album_lang->getAlbumLangCode()) ? $data_post['album_name'] : $album_lang->getAlbumName(),
                'album_description' => ($save_mode === $album_lang->getAlbumLangCode()) ? $data_post['album_description'] : $album_lang->getAlbumDescription(),

            );
            $arr_translate[$album_lang->getAlbumLangCode()] = $item;
        }
        if (!isset($arr_translate[$save_mode]) && isset($arr_language[$save_mode])) {
            $item = array(
                'album_id' => -1,
                'album_name' => $data_post['album_name'],
                'album_description' => $data_post['album_description'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'album_id' => $album_model->getAlbumId(),
            'album_keyword' => ($save_mode === BinLanguage::GENERAL) ? $data_post['album_keyword'] : $album_model->getAlbumKeyword(),
            'album_insert_time' => ($save_mode === BinLanguage::GENERAL) ? $data_post['album_insert_time'] : $this->my->formatDateTime($album_model->getAlbumInsertTime(), false),
            'album_active' => ($save_mode === BinLanguage::GENERAL) ? $data_post['album_active'] : $album_model->getAlbumActive(),
            'album_is_home' => ($save_mode === BinLanguage::GENERAL) ? $data_post['album_is_home'] : $album_model->getAlbumIsHome(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );
        $messages['status'] = 'border-red';
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
        ]);
    }

    public function deleteAction()
    {
        $activity = new Activity();
        $message = '';
        $list_album = $this->request->get('item');
        $bin_album = array();
        $msg_delete = array('error' => '', 'success' => '');
        if ($list_album) {
            foreach ($list_album as $album_id) {
                $album = Album::getByID($album_id);

                if ($album) {
                    $image = Image::getFirstByAlbum($album->getAlbumId());
                    $table_names = array();
                    $message_temp = "Can't delete the Album Name = " . $album->getAlbumName() . ". Because It's exist in";

                    if ($image) {
                        $table_names[] = " Image";
                    }
                    if (empty($table_names)) {
                        $old_album_data = $album->toArray();
                        $new_album_data = array();
                        $bin_album[$album_id] = array($old_album_data, $new_album_data);
                        $album->delete();
                        AlbumLang::deleteById($album_id);
                    } else {
                        $msg_delete['error'] .= $message_temp . implode(", ", $table_names) . "<br>";
                    }
                }
            }
        }
        if (count($bin_album) > 0) {
            // delete success
            $message .= 'Delete ' . count($bin_album) . ' album success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('bin_album' => $bin_album));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        $this->response->redirect('/dashboard/list-album');
        return;
    }

    private function getParameter()
    {
        $sql = "SELECT * FROM Bincg\Models\BinAlbum WHERE 1 ";
        $keyword = trim($this->request->get("txtSearch"));
        $arrParameter = array();
        $validator = new Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (album_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (album_name like CONCAT('%',:keyword:,'%') OR album_keyword like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        $validator = new Validator();
        $sql .= " ORDER BY album_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    private function getParameterExport()
    {
        $sql = "SELECT * FROM Bincg\Models\BinCareer WHERE career_active = 'Y'";
        $keyword = trim($this->request->get("txtSearch"));

        $arrParameter = array();
        $validator = new \Bincg\Utils\Validator();
        if (!empty($keyword)) {
            if ($validator->validInt($keyword)) {
                $sql .= " AND (career_id = :number:)";
                $arrParameter['number'] = $keyword;
            } else {
                $sql .= " AND (career_name like CONCAT('%',:keyword:,'%') OR career_keyword like CONCAT('%',:keyword:,'%')
                OR career_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }


        $sql .= " ORDER BY career_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    public function exportAction()
    {
        $data = $this->getParameterExport();
        $list_career = $this->modelsManager->executeQuery($data['sql'], $data['para']);
        $current_page = $this->request->get('page');
        $validator = new \Bincg\Utils\Validator();
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

        $this->view->setVars(array(
            'page' => $list_career,
            'msg_result' => $msg_result,
            'msg_delete' => $msg_delete,
        ));
    }

}