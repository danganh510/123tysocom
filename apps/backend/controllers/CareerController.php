<?php

namespace Score\Backend\Controllers;

use Score\Models\ScCareer;
use Score\Models\ScCareerLang;
use Score\Models\ScCareerOffice;
use Score\Models\ScLanguage;
use Score\Repositories\CareerLang;
use Score\Repositories\Career;
use Score\Repositories\CareerOffice;
use Score\Repositories\Language;
use Score\Repositories\Activity;
use Score\Repositories\Office;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Utils\Validator;
class CareerController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $list_career = $this->modelsManager->executeQuery($data['sql'],$data['para']);
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
                'data'  => $list_career,
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
        $data = array('id' => -1,'active' => 'Y', 'is_home' => 'N', 'expired' => 'N', 'featured' => 'N', 'order' => 1,'offices' =>array(), 'insert_time' => $this->my->formatDateTime($this->globalVariable->curTime,false));
        $messages = array();
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'id' => -1,
                'name' => $this->request->getPost("txtName", array('string', 'trim')),
                'offices' => $this->request->getPost('slcOffice'),
                'icon' => $this->request->getPost("txtIcon", array('string', 'trim')),
                'meta_image' => $this->request->getPost("txtMetaImage", array('string', 'trim')),
                'location' => $this->request->getPost("txtLocation", array('string', 'trim')),
                'keyword' => $this->request->getPost("txtKeyword", array('string', 'trim')),
                'title' => $this->request->getPost("txtTitle", array('string', 'trim')),
                'meta_keyword' => $this->request->getPost("txtMetakey", array('string', 'trim')),
                'meta_description' => $this->request->getPost("txtMetades", array('string', 'trim')),
                'insert_time' => $this->request->getPost("txtInsertTime", array('string', 'trim')),
                'summary' => $this->request->getPost("txtSummary"),
                'content' => $this->request->getPost("txtContent"),
                'order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'active' => $this->request->getPost("radActive"),
                'is_home' => $this->request->getPost("radIsHomeActive"),
                'expired' => $this->request->getPost("radExpired"),
                'featured' => $this->request->getPost("radFeatured"),
                'services' => $this->request->getPost("chkService"),
                'skill' => $this->request->getPost("txtSkill"),
                'experience' => $this->request->getPost("txtExperience"),
                'job_benefit' => $this->request->getPost("txtJobBenefit"),
                'base_salary' => $this->request->getPost("txtBaseSalary"),

            );
            $career_repo = new Career();
            if (empty($data["name"])) {
                $messages["name"] = "Name field is required.";
            }
            if (empty($data["insert_time"])) {
                $messages["insert_time"] = "Insert time field is required.";
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
            }elseif($career_repo->checkKeyword($data["keyword"],-1)){
                $messages["keyword"] = "Keyword is exists!";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            }elseif (!is_numeric($data["order"]))
            {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $new_type = new ScCareer();
                $new_type->setCareerName($data["name"]);
                $new_type->setCareerIcon($data["icon"]);
                $new_type->setCareerMetaImage($data["meta_image"]);
                $new_type->setCareerLocation($data["location"]);
                $new_type->setCareerKeyword($data["keyword"]);
                $new_type->setCareerTitle($data["title"]);
                $new_type->setCareerMetaKeyword($data["meta_keyword"]);
                $new_type->setCareerMetaDescription($data["meta_description"]);
                $new_type->setCareerSummary($data["summary"]);
                $new_type->setCareerContent($data["content"]);
                $new_type->setCareerOrder($data["order"]);
                $new_type->setCareerActive($data["active"]);
                $new_type->setCareerFeatured($data["featured"]);
                $new_type->setCareerIsHome($data["is_home"]);
                $new_type->setCareerExpired($data["expired"]);
                $new_type->setCareerInsertTime($this->my->UTCTime(strtotime($data["insert_time"])));
                $new_type->setCareerUpdateTime($this->globalVariable->curTime);
                $new_type->setCareerSkill($data["skill"]);
                $new_type->setCareerExperienceRequirements($data["experience"]);
                $new_type->setCareerJobBenefits($data["job_benefit"]);
                $new_type->setCareerBaseSalarySchema($data["base_salary"]);
                $result = $new_type->save();

                $message =  "We can't store your info now: "."<br/>";
                if ($result === true){
                    foreach ($data["offices"] as $office_id)
                    {
                        $career_office = new ScCareerOffice();
                        $career_office->setCoCareerId($new_type->getCareerId());
                        $career_office->setCoOfficeId($office_id);
                        $career_office->save();
                    }
                    $activity = new Activity();                    
                    $message = 'Create the career ID: '.$new_type->getCareerId().' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('bin_career' => array($new_type->getCareerId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    foreach ($new_type->getMessages() as $msg) {
                        $message .= $msg."<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
                $this->session->set('msg_result',$msg_result );
                $this->response->redirect("/dashboard/list-career");
                return;

            }
        }
        $select_office = Office::getOfficeCombobox($data['offices']);
        $messages["status"] = "border-red";
        $this->view->setVars([
            'oldinput' => $data,
            'messages' => $messages,
            'select_office' => $select_office,
        ]);
    }
    public function editAction()
    {
        $career_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($career_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $career_model = Career::getByID($career_id);
        if(!$career_model)
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_translate = array();
        $messages = array();
        $data_post = array (
            'career_id' => -1,
            'career_name' => '',
            'career_offices' => array(),
            'career_icon' => '',
            'career_meta_image' => '',
            'career_keyword' => '',
            'career_title' => '',
            'career_meta_keyword' => '',
            'career_meta_description' => '',
            'career_insert_time' => '',
            'career_summary' => '',
            'career_content' => '',
            'career_order' => '',
            'career_active' => '',
            'career_featured' => '',
            'career_is_home' => '',
            'career_expired' => '',
            'career_skill' => '',
            'career_experience' => '',
            'career_job_benefit' => '',
            'career_base_salary' => '',
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
            if($save_mode != ScLanguage::GENERAL) {
                $data_post['career_name'] = $this->request->get("txtName", array('string', 'trim'));
                $data_post['career_title'] = $this->request->get("txtTitle", array('string', 'trim'));
                $data_post['career_meta_keyword'] = $this->request->get("txtMetaKey", array('string', 'trim'));
                $data_post['career_meta_description'] = $this->request->get("txtMetaDesc", array('string', 'trim'));
                $data_post['career_icon'] = $this->request->getPost('txtIcon', array('string', 'trim'));
                $data_post['career_meta_image'] = $this->request->getPost('txtMetaImage', array('string', 'trim'));
                $data_post['career_location'] = $this->request->get("txtLocation", array('string', 'trim'));
                $data_post['career_summary'] = $this->request->get("txtSummary");
                $data_post['career_content'] = $this->request->get("txtContent");
                $data_post['career_skill'] = $this->request->get("txtSkill");
                $data_post['career_experience'] = $this->request->get("txtExperience");
                $data_post['career_job_benefit'] = $this->request->get("txtJobBenefit");
                $data_post['career_base_salary'] = $this->request->get("txtBaseSalary");
                if (empty($data_post['career_name'])) {
                    $messages[$save_mode]['name'] = 'Name field is required.';
                }
                if (empty($data_post['career_title'])) {
                    $messages[$save_mode]['title'] = 'Title field is required.';
                }
                if (empty($data_post['career_meta_keyword'])) {
                    $messages[$save_mode]['meta_keyword'] = 'Meta keyword field is required.';
                }
                if (empty($data_post['career_meta_description'])) {
                    $messages[$save_mode]['meta_description'] = 'Meta description field is required.';
                }
            } else {
                $data_post['career_offices'] = $this->request->getPost('slcOffice');
                $data_post['career_keyword'] = $this->request->get("txtKeyword", array('string', 'trim'));
                $data_post['career_insert_time'] = $this->request->get("txtInsertTime", array('string', 'trim'));
                $data_post['career_active'] = $this->request->getPost('radActive');
                $data_post['career_featured'] = $this->request->getPost('radFeatured');
                $data_post['career_is_home'] = $this->request->getPost('radIsHomeActive');
                $data_post['career_expired'] = $this->request->getPost('radExpired');
                $data_post['career_order'] = $this->request->getPost('txtOrder', array('string', 'trim'));
                if (empty($data_post['career_insert_time'])) {
                    $messages['career_insert_time'] = 'Insert time field is required.';
                }
                if (empty($data_post['career_order'])) {
                    $messages['order'] = 'Order field is required.';
                } else if (!is_numeric($data_post['career_order'])) {
                    $messages['order'] = 'Order is not valid.';
                }
                $career_repo = new Career();
                if (empty($data_post['career_keyword'])) {
                    $messages['keyword'] = 'Keyword field is required.';
                } elseif($career_repo->checkKeyword($data_post["career_keyword"],$career_id)){
                    $messages['keyword'] = "Keyword is exists!";
                }
            }
            if(empty($messages)) {
                $action = '';
                $arrLog = array();
                $messageLog = '';
                $messagesAzure = '';
                $activity = new Activity();
                switch ($save_mode) {
                    case ScLanguage::GENERAL:
                        $arr_career_lang = ScCareerLang::findById($career_id);
                        $data_old = array(
                            'career_keyword' => $career_model->getCareerKeyword(),
                            'career_insert_time' => $career_model->getCareerInsertTime(),
                            'career_order' => $career_model->getCareerOrder(),
                            'career_active' => $career_model->getCareerActive(),
                            'career_featured' => $career_model->getCareerFeatured(),
                            'career_is_home' => $career_model->getCareerIsHome(),
                            'career_expired' => $career_model->getCareerExpired(),
                        );
                        $career_model->setCareerInsertTime($this->my->UTCTime(strtotime($data_post['career_insert_time'])));
                        $career_model->setCareerKeyword($data_post['career_keyword']);
                        $career_model->setCareerOrder($data_post['career_order']);
                        $career_model->setCareerActive($data_post['career_active']);
                        $career_model->setCareerFeatured($data_post['career_featured']);
                        $career_model->setCareerIsHome($data_post['career_is_home']);
                        $career_model->setCareerExpired($data_post['career_expired']);
                        $career_model->setCareerUpdateTime($this->globalVariable->curTime);
                        $result = $career_model->update();
                        if($result){
                            CareerOffice::deleteByCareer($career_id);
                            foreach ($data_post["career_offices"] as $office_id)
                            {
                                $career_office = new ScCareerOffice();
                                $career_office->setCoCareerId($career_model->getCareerId());
                                $career_office->setCoOfficeId($office_id);
                                $career_office->save();
                            }
                        }
                        $info = ScLanguage::GENERAL;
                        $data_new = array(
                            'career_insert_time' => $career_model->getCareerInsertTime(),
                            'career_keyword' => $career_model->getCareerKeyword(),
                            'career_order' => $career_model->getCareerOrder(),
                            'career_active' => $career_model->getCareerActive(),
                            'career_featured' => $career_model->getCareerFeatured(),
                            'career_is_home' => $career_model->getCareerIsHome(),
                            'career_expired' => $career_model->getCareerExpired(),
                        );
                        break;
                    case $this->globalVariable->defaultLanguage :
                        $data_old = array(
                            'career_name' => $career_model->getCareerName(),
                            'career_title' => $career_model->getCareerTitle(),
                            'career_keyword' => $career_model->getCareerKeyword(),
                            'career_meta_keyword' => $career_model->getCareerMetaKeyword(),
                            'career_meta_description' => $career_model->getCareerMetaDescription(),
                            'career_icon' => $career_model->getCareerIcon(),
                            'career_meta_image' => $career_model->getCareerMetaImage(),
                            'career_location' => $career_model->getCareerLocation(),
                            'career_summary' => $career_model->getCareerSummary(),
                            'career_content' => $career_model->getCareerContent(),
                            'career_skill' => $career_model->getCareerSkill(),
                            'career_experience' => $career_model->getCareerExperienceRequirements(),
                            'career_job_benefit' => $career_model->getCareerJobBenefits(),
                            'career_base_salary' => $career_model->getCareerBaseSalarySchema(),
                        );
                        $career_model->setCareerName($data_post['career_name']);
                        $career_model->setCareerTitle($data_post['career_title']);
                        $career_model->setCareerMetaKeyword($data_post['career_meta_keyword']);
                        $career_model->setCareerMetaDescription($data_post['career_meta_description']);
                        $career_model->setCareerIcon($data_post['career_icon']);
                        $career_model->setCareerMetaImage($data_post['career_meta_image']);
                        $career_model->setCareerLocation($data_post['career_location']);
                        $career_model->setCareerSummary($data_post['career_summary']);
                        $career_model->setCareerContent($data_post['career_content']);
                        $career_model->setCareerSkill($data_post['career_skill']);
                        $career_model->setCareerExperienceRequirements($data_post['career_experience']);
                        $career_model->setCareerJobBenefits($data_post['career_job_benefit']);
                        $career_model->setCareerBaseSalarySchema($data_post['career_base_salary']);
                        $career_model->setCareerUpdateTime($this->globalVariable->curTime);
                        $result = $career_model->update();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'career_name' => $career_model->getCareerName(),
                            'career_title' => $career_model->getCareerTitle(),
                            'career_meta_keyword' => $career_model->getCareerMetaKeyword(),
                            'career_meta_description' => $career_model->getCareerMetaDescription(),
                            'career_icon' => $career_model->getCareerIcon(),
                            'career_meta_image' => $career_model->getCareerMetaImage(),
                            'career_location' => $career_model->getCareerLocation(),
                            'career_summary' => $career_model->getCareerSummary(),
                            'career_content' => $career_model->getCareerContent(),
                            'career_skill' => $career_model->getCareerSkill(),
                            'career_experience' => $career_model->getCareerExperienceRequirements(),
                            'career_job_benefit' => $career_model->getCareerJobBenefits(),
                            'career_base_salary' => $career_model->getCareerBaseSalarySchema(),
                        );
                        break;
                    default:
                        $content_career_lang = CareerLang::findFirstByIdAndLang($career_id, $save_mode);
                        if (!$content_career_lang) {
                            $content_career_lang = new ScCareerLang();
                            $content_career_lang->setCareerId($career_id);
                            $content_career_lang->setCareerLangCode($save_mode);
                        }
                        $data_old = $content_career_lang->toArray();
                        $content_career_lang->setCareerName($data_post['career_name']);
                        $content_career_lang->setCareerTitle($data_post['career_title']);
                        $content_career_lang->setCareerMetaKeyword($data_post['career_meta_keyword']);
                        $content_career_lang->setCareerMetaDescription($data_post['career_meta_description']);
                        $content_career_lang->setCareerIcon($data_post['career_icon']);
                        $content_career_lang->setCareerMetaImage($data_post['career_meta_image']);
                        $content_career_lang->setCareerLocation($data_post['career_location']);
                        $content_career_lang->setCareerSummary($data_post['career_summary']);
                        $content_career_lang->setCareerContent($data_post['career_content']);
                        $content_career_lang->setCareerSkill($data_post['career_skill']);
                        $content_career_lang->setCareerExperienceRequirements($data_post['career_experience']);
                        $content_career_lang->setCareerJobBenefits($data_post['career_job_benefit']);
                        $content_career_lang->setCareerBaseSalarySchema($data_post['career_base_salary']);
                        $result = $content_career_lang->save();
                        $info = $arr_language[$save_mode];
                        $data_new = array(
                            'career_lang_code' => $content_career_lang->getCareerLangCode(),
                            'career_name' => $content_career_lang->getCareerName(),
                            'career_title' => $content_career_lang->getCareerTitle(),
                            'career_meta_keyword' => $content_career_lang->getCareerMetaKeyword(),
                            'career_meta_description' => $content_career_lang->getCareerMetaDescription(),
                            'career_icon' => $content_career_lang->getCareerIcon(),
                            'career_meta_image' => $content_career_lang->getCareerMetaImage(),
                            'career_location' => $content_career_lang->getCareerLocation(),
                            'career_summary' => $content_career_lang->getCareerSummary(),
                            'career_content' => $content_career_lang->getCareerContent(),
                            'career_skill' => $content_career_lang->getCareerSkill(),
                            'career_experience' => $content_career_lang->getCareerExperienceRequirements(),
                            'career_job_benefit' => $content_career_lang->getCareerJobBenefits(),
                            'career_base_salary' => $content_career_lang->getCareerBaseSalarySchema(),
                        );
                        break;
                }
                if ($result) {
                    $messages = array(
                        'message' => ucfirst($info . " Update Career success<br>").$messagesAzure,
                        'typeMessage' => 'success'
                    );
                    //End Update Azure Search
                    $message = '';
                    $data_log = json_encode(array('bin_career_lang' => array($career_id => array($data_old, $data_new))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                }else{
                    $messages = array(
                        'message' => "Update Career fail",
                        'typeMessage' => "error",
                    );
                }
            }
        }
        $item = array(
            'career_id' =>$career_model->getCareerId(),
            'career_name'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_name']:$career_model->getCareerName(),
            'career_title' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_title']:$career_model->getCareerTitle(),
            'career_meta_keyword'=>($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_meta_keyword']:$career_model->getCareerMetaKeyword(),
            'career_meta_description' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_meta_description']:$career_model->getCareerMetaDescription(),
            'career_icon' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_icon']:$career_model->getCareerIcon(),
            'career_meta_image' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_meta_image']:$career_model->getCareerMetaImage(),
            'career_location' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_location']:$career_model->getCareerLocation(),
            'career_summary' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_summary']:$career_model->getCareerSummary(),
            'career_content' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_content']:$career_model->getCareerContent(),
            'career_skill' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_skill']:$career_model->getCareerSkill(),
            'career_experience' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_experience']:$career_model->getCareerExperienceRequirements(),
            'career_job_benefit' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_job_benefit']:$career_model->getCareerJobBenefits(),
            'career_base_salary' => ($save_mode ===$this->globalVariable->defaultLanguage)?$data_post['career_base_salary']:$career_model->getCareerBaseSalarySchema(),
        );
        $arr_translate[$lang_default] = $item;
        $arr_career_lang = ScCareerLang::findById($career_id);
        foreach ($arr_career_lang as $career_lang){
            $item = array(
                'career_id'=>$career_lang->getCareerId(),
                'career_name'=>($save_mode === $career_lang->getCareerLangCode())?$data_post['career_name']:$career_lang->getCareerName(),
                'career_title' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_title']:$career_lang->getCareerTitle(),
                'career_meta_keyword'=>($save_mode === $career_lang->getCareerLangCode())?$data_post['career_meta_keyword']:$career_lang->getCareerMetaKeyword(),
                'career_meta_description' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_meta_description']:$career_lang->getCareerMetaDescription(),
                'career_icon' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_icon']:$career_lang->getCareerIcon(),
                'career_meta_image' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_meta_image']:$career_lang->getCareerMetaImage(),
                'career_location' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_location']:$career_lang->getCareerLocation(),
                'career_summary' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_summary']:$career_lang->getCareerSummary(),
                'career_content' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_content']:$career_lang->getCareerContent(),
                'career_skill' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_skill']:$career_lang->getCareerSkill(),
                'career_experience' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_experience']:$career_lang->getCareerExperienceRequirements(),
                'career_job_benefit' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_job_benefit']:$career_lang->getCareerJobBenefits(),
                'career_base_salary' => ($save_mode === $career_lang->getCareerLangCode())?$data_post['career_base_salary']:$career_lang->getCareerBaseSalarySchema(),
            );
            $arr_translate[$career_lang->getCareerLangCode()] = $item;
        }
        if(!isset($arr_translate[$save_mode])&& isset($arr_language[$save_mode])){
            $item = array(
                'career_id'=> -1,
                'career_name'=> $data_post['career_name'],
                'career_title' => $data_post['career_title'],
                'career_meta_keyword' => $data_post['career_meta_keyword'],
                'career_meta_description' => $data_post['career_meta_description'],
                'career_icon' => $data_post['career_icon'],
                'career_meta_image' => $data_post['career_meta_image'],
                'career_location' => $data_post['career_location'],
                'career_summary'=> $data_post['career_summary'],
                'career_content' => $data_post['career_content'],
                'career_skill' => $data_post['career_skill'],
                'career_experience' => $data_post['career_experience'],
                'career_job_benefit' => $data_post['career_job_benefit'],
                'career_base_salary' => $data_post['career_base_salary'],
            );
            $arr_translate[$save_mode] = $item;
        }
        $formData = array(
            'career_id'=>$career_model->getCareerId(),
            'career_offices' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_offices']:CareerOffice::getOfficesByCareer($career_id),
            'career_keyword' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_keyword']:$career_model->getCareerKeyword(),
            'career_insert_time' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_insert_time']:$this->my->formatDateTime($career_model->getCareerInsertTime(),false),
            'career_order' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_order']:$career_model->getCareerOrder(),
            'career_active' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_active']:$career_model->getCareerActive(),
            'career_featured' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_featured']:$career_model->getCareerFeatured(),
            'career_is_home' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_is_home']:$career_model->getCareerIsHome(),
            'career_expired' => ($save_mode ===ScLanguage::GENERAL)?$data_post['career_expired']:$career_model->getCareerExpired(),
            'arr_translate' => $arr_translate,
            'arr_language' => $arr_language,
            'lang_default' => $lang_default,
            'lang_current' => $lang_current
        );
        $select_office = Office::getOfficeCombobox($formData['career_offices']);
        $messages['status'] = 'border-red';
        $this->view->setVars([
            'formData' => $formData,
            'messages' => $messages,
            'select_office' => $select_office,
        ]);
    }
    public function deleteAction ()
    {
        $arrLog = array();
        $activity = new Activity();
        $message = '';
        $list_career = $this->request->get('item');
        $bin_career = array();
        $msg_delete = array('success' => '');
        if($list_career) {
            foreach ($list_career as $career_id) {
                $career = Career::getByID($career_id);
                $arr_career_lang = ScCareerLang::findById($career_id);
                if( $career ) {
                    $old_career_data = $career->toArray();
                    $new_career_data = array();
                    $bin_career[$career_id] = array($old_career_data, $new_career_data);
                    $career->delete();
                    CareerLang::deleteById($career_id);
                    CareerOffice::deleteByCareer($career_id);
                }
            }
        }
        if (count($bin_career) > 0 ) {
            // delete success
            $message .= 'Delete '. count($bin_career) .' career success.';
            $msg_delete['success'] = $message;
            // store activity success
            $data_log = json_encode(array('bin_career' => $bin_career));
            $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
        }
        $this->session->set('msg_delete', $msg_delete);
        $this->response->redirect('/dashboard/list-career');
        return;
    }
    private function getParameter(){
        $sql = "SELECT * FROM Score\Models\ScCareer WHERE 1 ";
        $keyword = trim($this->request->get("txtSearch"));
        $office = $this->request->get("slcOffice");
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (career_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (career_name like CONCAT('%',:keyword:,'%') OR career_keyword like CONCAT('%',:keyword:,'%')
                OR career_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if (!empty($office)) {
            $sql.=" AND career_id IN (SELECT co_career_id  FROM Score\Models\ScCareerOffice WHERE co_office_id =:office_id: )";
            $arrParameter['office_id'] = $office;
            $this->dispatcher->setParam("slcOffice", $office);
        }
        $sql.=" ORDER BY career_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        $slcOffice = Office::getOfficeCombobox([$office]);
        $this->view->setVars(array( 'slcOffice'  => $slcOffice,));
        return $data;
    }

    private function getParameterExport(){
        $sql = "SELECT * FROM Score\Models\ScCareer WHERE career_active = 'Y'";
        $keyword = trim($this->request->get("txtSearch"));

        $arrParameter = array();
        $validator = new \Score\Utils\Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (career_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (career_name like CONCAT('%',:keyword:,'%') OR career_keyword like CONCAT('%',:keyword:,'%')
                OR career_title like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }


        $sql.=" ORDER BY career_id DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }

    public function exportAction()
    {
        $data = $this->getParameterExport();
        $list_career = $this->modelsManager->executeQuery($data['sql'],$data['para']);
        $current_page = $this->request->get('page');
        $validator = new \Score\Utils\Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
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
            'msg_result'  => $msg_result,
            'msg_delete'  => $msg_delete,
        ));
    }

}