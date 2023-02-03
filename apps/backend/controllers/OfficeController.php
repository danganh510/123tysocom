<?php

namespace Score\Backend\Controllers;
use Score\Models\ScLanguage;
use Score\Models\ScOffice;
use Score\Models\ScOfficeImage;
use Score\Models\ScOfficeLang;
use Score\Repositories\Activity;
use Score\Repositories\CountryGeneral;
use Score\Repositories\Language;
use Score\Repositories\Office;
use Score\Repositories\OfficeLang;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class OfficeController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_office = $this->modelsManager->executeQuery($data['sql'], $data['para']);

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
                'data'  => $list_office,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );

        $this->view->setVars(array(
            'office' => $paginator->getPaginate(),
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }
    public function createAction()
    {
        $data = array('id' => -1, 'active' => 'Y', 'order' => 1);
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => $this->request->getPost("txtName"), array('string', 'trim'),
                'image' => $this->request->getPost("txtImage"), array('string', 'trim'),
                'countryCode' => $this->request->getPost("slcCountryCode", array('string', 'trim')),
                'positionX' => $this->request->getPost("txtPositionX", array('string', 'trim')),
                'positionY' => $this->request->getPost("txtPositionY", array('string', 'trim')),
                'address' => $this->request->getPost("txtAddress", array('string', 'trim')),
                'email' => $this->request->getPost("txtEmail", array('string', 'trim')),
                'phone' => $this->request->getPost("txtPhone", array('string', 'trim')),
                'fax' => $this->request->getPost("txtFax", array('string', 'trim')),
                'workingTime' => $this->request->getPost("txtWorkingTime", array('string', 'trim')),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
            );

            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["countryCode"])) {
                $messages["countryCode"] = "Country Code field is required.";
            }
            if (empty($data["positionX"])) {
                $messages["positionX"] = "Latitude field is required.";
            }elseif (!is_numeric($data["positionX"]))
            {
                $messages["positionX"] = "Latitude is not valid ";
            }
            if (empty($data["positionY"])) {
                $messages["positionY"] = "Longitude field is required.";
            }elseif (!is_numeric($data["positionY"]))
            {
                $messages["positionY"] = "Longitude is not valid ";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            }elseif (!is_numeric($data["order"]))
            {
                $messages["order"] = "Order is not valid ";
            }

            if (count($messages) == 0) {
                $msg_result = array();
                $new_office = new ScOffice();
                $new_office->setOfficeName($data["name"]);
                $new_office->setOfficeImage($data["image"]);
                $new_office->setOfficeCountryCode($data["countryCode"]);
                $new_office->setOfficePositionX($data["positionX"]);
                $new_office->setOfficePositionY($data["positionY"]);
                $new_office->setOfficeAddress($data["address"]);
                $new_office->setOfficeEmail($data["email"]);
                $new_office->setOfficePhone($data["phone"]);
                $new_office->setOfficeFax($data["fax"]);
                $new_office->setOfficeWorkingTime($data["workingTime"]);
                $new_office->setOfficeOrder($data["order"]);
                $new_office->setOfficeActive($data["active"]);
                $result = $new_office->save();

                if ($result === true){
                    $message = 'Create the office ID: '.$new_office->getOfficeId().' success';
                    $msg_result = array('status' => 'success', 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('dsbcf_office' => array($new_office->getOfficeId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $message =  "We can't store your info now: <br/>";
                    foreach ($new_office->getMessages() as $msg) {
                        $message.=$msg."<br/>";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $this->session->set('msg_result',$msg_result );
                return $this->response->redirect("/dashboard/list-office");
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
        $office_id = $this->request->get('id');

        $checkID = new Validator();
        if(!$checkID->validInt($office_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $office_model = ScOffice::findFirstById($office_id);
        if(empty($office_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array (
            'office_id' => -1,
            'office_name' => "",
            'office_image' => "",
            'office_country_code' => "",
            'office_positionX' => "",
            'office_positionY' => "",
            'office_address' => "",
            'office_email' => "",
            'office_phone' => "",
            'office_fax' => "",
            'office_working_time' => "",
            'office_order' => "",
            'office_active' => "",
        ) ;
        $save_mode = '';
        $lang_current = $this->globalVariable->defaultLanguage;
        $arr_language = Language::arrLanguages();
        if($this->request->isPost()) {
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
            if($save_mode != ScLanguage::GENERAL) {
                $data_post['office_name'] = $this->request->getPost('txtName', array('string', 'trim'));
                $data_post['office_address'] = $this->request->getPost('txtAddress', array('string', 'trim'));
                $data_post['office_working_time'] = $this->request->getPost('txtWorkingTime', array('string', 'trim'));
                if(empty($data_post['office_name'])) {
                    $messages[$save_mode]['office_name'] = 'Name field is required.';
                }
            } else {
                $data_post['office_image'] = $this->request->getPost('txtImage', array('string', 'trim'));
                $data_post['office_country_code'] = $this->request->getPost('slcCountryCode', array('string', 'trim'));
                $data_post['office_positionX'] = $this->request->getPost('txtPositionX', array('string', 'trim'));
                $data_post['office_positionY'] = $this->request->getPost('txtPositionY', array('string', 'trim'));
                $data_post['office_email'] = $this->request->getPost('txtEmail', array('string', 'trim'));
                $data_post['office_phone'] = $this->request->getPost('txtPhone', array('string', 'trim'));
                $data_post['office_fax'] = $this->request->getPost('txtFax', array('string', 'trim'));
                $data_post['office_order'] = $this->request->getPost('txtOrder');
                $data_post['office_active'] = $this->request->getPost('radActive');
                if (empty($data_post["office_country_code"])) {
                    $messages["office_country_code"] = "Country code field is required.";
                }
                if (empty($data_post["office_positionX"])) {
                    $messages["office_positionX"] = "Latitude field is required.";
                }elseif (!is_numeric($data_post["office_positionX"]))
                {
                    $messages["office_positionX"] = "Latitude is not valid ";
                }
                if (empty($data_post["office_positionY"])) {
                    $messages["office_positionY"] = "Longitude field is required.";
                }elseif (!is_numeric($data_post["office_positionY"]))
                {
                    $messages["office_positionY"] = "Longitude is not valid ";
                }
                if (empty($data_post["office_order"])) {
                    $messages["office_order"] = "Order field is required.";
                }elseif (!is_numeric($data_post["office_order"]))
                {
                    $messages["office_order"] = "Order is not valid ";
                }
            }
            if(empty($messages)) {
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $data_old = array(
                            'office_image' => $office_model->getOfficeImage(),
                            'office_country_code' => $office_model->getOfficeCountryCode(),
                            'office_positionX' => $office_model->getOfficePositionX(),
                            'office_positionY' => $office_model->getOfficePositionY(),
                            'office_email' => $office_model->getOfficeEmail(),
                            'office_phone' => $office_model->getOfficePhone(),
                            'office_fax' => $office_model->getOfficeFax(),
                            'office_order' => $office_model->getOfficeOrder(),
                            'office_active' => $office_model->getOfficeActive()
                        );
                        $office_model->setOfficeImage($data_post['office_image']);
                        $office_model->setOfficeCountryCode($data_post['office_country_code']);
                        $office_model->setOfficePositionX($data_post['office_positionX']);
                        $office_model->setOfficePositionY($data_post['office_positionY']);
                        $office_model->setOfficeEmail($data_post['office_email']);
                        $office_model->setOfficePhone($data_post['office_phone']);
                        $office_model->setOfficeFax($data_post['office_fax']);
                        $office_model->setOfficeOrder($data_post['office_order']);
                        $office_model->setOfficeActive($data_post['office_active']);
                        $result = $office_model->update();
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'office_image' => $office_model->getOfficeImage(),
                            'office_country_code' => $office_model->getOfficeCountryCode(),
                            'office_positionX' => $office_model->getOfficePositionX(),
                            'office_positionY' => $office_model->getOfficePositionY(),
                            'office_email' => $office_model->getOfficeEmail(),
                            'office_phone' => $office_model->getOfficePhone(),
                            'office_fax' => $office_model->getOfficeFax(),
                            'office_order' => $office_model->getOfficeOrder(),
                            'office_active' => $office_model->getOfficeActive()
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'office_name' => $office_model->getOfficeName(),
                            'office_address' => $office_model->getOfficeAddress(),
                            'office_working_time' => $office_model->getOfficeWorkingTime(),
                        );
                        $office_model->setOfficeName($data_post['office_name']);
                        $office_model->setOfficeAddress($data_post['office_address']);
                        $office_model->setOfficeWorkingTime($data_post['office_working_time']);
                        $result = $office_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'office_name' => $office_model->getOfficeName(),
                            'office_address' => $office_model->getOfficeAddress(),
                            'office_working_time' => $office_model->getOfficeWorkingTime(),
                        );
                        break;
                    default:
                        $office_lang_model = OfficeLang::findFirstByIdAndLang($office_id, $save_mode);
                        if (empty($office_lang_model)) {
                            $office_lang_model = new ScOfficeLang();
                            $office_lang_model->setOfficeId($office_id);
                            $office_lang_model->setOfficeLangCode($save_mode);
                        }
                        $office_lang_model->setOfficeName($data_post['office_name']);
                        $office_lang_model->setOfficeAddress($data_post['office_address']);
                        $office_lang_model->setOfficeWorkingTime($data_post['office_working_time']);
                        $result = $office_lang_model->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'office_lang_code' => $office_lang_model->getOfficeLangCode(),
                            'office_name' => $office_lang_model->getOfficeName(),
                            'office_address' => $office_lang_model->getOfficeAddress(),
                            'office_working_time' => $office_lang_model->getOfficeWorkingTime(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Office success"),
                        'typeMessage' => "success",
                    );
                    $message = '';
                    $data_log = json_encode(array('bin_office_lang' => array($office_id => array($data_old, $data_new))));
                    $activity = new Activity();
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update Office fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $office_model = Office::getByID($office_model->getOfficeId());
        $item = array(
            'office_id' =>$office_model->getOfficeId(),
            'office_name'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['office_name']:$office_model->getOfficeName(),
            'office_address'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['office_address']:$office_model->getOfficeAddress(),
            'office_working_time'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['office_working_time']:$office_model->getOfficeWorkingTime(),
        );
        $arr_translate[$this->globalVariable->defaultLanguage] = $item;

        $arr_office_lang = ScOfficeLang::findById($office_model->getOfficeId());

        foreach ($arr_office_lang as $office_lang){
            $item = array(
                'office_id'=>$office_lang->getOfficeId(),
                'office_name'=>($save_mode === $office_lang->getOfficeName())?$data_post['office_name']:$office_lang->getOfficeName(),
                'office_address'=>($save_mode === $office_lang->getOfficeAddress())?$data_post['office_address']:$office_lang->getOfficeAddress(),
                'office_working_time'=>($save_mode === $office_lang->getOfficeWorkingTime())?$data_post['office_working_time']:$office_lang->getOfficeWorkingTime(),
            );
            $arr_translate[$office_lang->getOfficeLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'office_id'=> -1,
                'office_name'=> $data_post['office_name'],
                'office_address'=> $data_post['office_address'],
                'office_working_time'=> $data_post['office_working_time'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'office_id'=>$office_model->getOfficeId(),
            'office_name' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_name']:$office_model->getOfficeName(),
            'office_image' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_image']:$office_model->getOfficeImage(),
            'office_country_code' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_country_code']:$office_model->getOfficeCountryCode(),
            'office_positionX' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_positionX']:$office_model->getOfficePositionX(),
            'office_positionY' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_positionY']:$office_model->getOfficePositionY(),
            'office_address' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_address']:$office_model->getOfficeAddress(),
            'office_email' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_email']:$office_model->getOfficeEmail(),
            'office_phone' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_phone']:$office_model->getOfficePhone(),
            'office_fax' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_fax']:$office_model->getOfficeFax(),
            'office_working_time' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_working_time']:$office_model->getOfficeWorkingTime(),
            'office_order' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_order']:$office_model->getOfficeOrder(),
            'office_active' => ($save_mode ===ScLanguage::GENERAL)?$data_post['office_active']:$office_model->getOfficeActive(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_current' => $lang_current
        );
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
        ]);
    }
    public function deleteAction ()
    {
        $list_office = $this->request->get('item');
        $career_office = array();
        $msg_delete = array('error' => '', 'success' => '');
        if ($list_office) {
            foreach ($list_office as $office_id) {
                $office_model = Office::getByID($office_id);
                if($office_model) {
                    $officeImage = ScOfficeImage::findFirstByOfficeId($office_id);
                    if ($officeImage){
                        $message = 'Can\'t delete Office ID = '.$office_id.'. Because It\'s exists in Office Image.<br>';
                        $msg_delete['error'] .= $message;
                    } else {
                        $old_type_data = array(
                            'office_country_code' => $office_model->getOfficeCountryCode(),
                            'office_positionX' => $office_model->getOfficePositionX(),
                            'office_positionY' => $office_model->getOfficePositionY(),
                            'office_email' => $office_model->getOfficeEmail(),
                            'office_phone' => $office_model->getOfficePhone(),
                            'office_fax' => $office_model->getOfficeFax(),
                            'office_order' => $office_model->getOfficeOrder(),
                            'office_active' => $office_model->getOfficeActive(),
                            'office_name' => $office_model->getOfficeName(),
                            'office_address' => $office_model->getOfficeAddress(),
                            'office_working_time' => $office_model->getOfficeWorkingTime(),
                        );
                        $new_type_data = array();
                        $career_office[$office_id] = array($old_type_data, $new_type_data);
                        $office_model->delete();
                        OfficeLang::deleteById($office_id);
                    }
                }
            }
        }
        if (count($career_office) > 0) {
            // delete success
            $message = 'Delete '. count($career_office) .' Office success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('bin_office' => $career_office));
            $activity = new Activity();
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        return $this->response->redirect('/dashboard/list-office');
    }
    private function getParameter(){
        $sql = "SELECT * FROM Score\Models\ScOffice WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $country = $this->request->get('slcCountry');
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (office_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (office_name like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($country)) {
            $sql.=" AND (office_country_code = :country: )";
            $arrParameter['country'] = $country;
            $this->dispatcher->setParam("slcCountry", $country);
        }
        $sql .= " ORDER BY office_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        $slcCountry = CountryGeneral::getCountryCombobox($country);
        $this->view->setVars(array( 'slcCountry'  => $slcCountry,));
        return $data;
    }

}