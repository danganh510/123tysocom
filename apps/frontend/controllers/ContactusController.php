<?php
namespace Score\Frontend\Controllers;

use Score\Models\ScCommunicationChannel;
use Score\Models\ScContactus;
use Score\Models\ScUser;
use Score\Repositories\Activity;
use Score\Repositories\Article;
use Score\Repositories\Banner;
use Score\Repositories\CommunicationChannel;
use Score\Repositories\EmailTemplate;
use Score\Repositories\Office;
use Score\Repositories\Page;
use Score\Utils\IpApi;
use Score\Utils\NumVerify;
use Score\Utils\Validator;

define('MyS3UploadFolder', 'contactus');

class ContactusController extends ControllerBase
{
    public function indexAction()
    {
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }

        $id_success = $this->request->get("success_id");
        if(preg_match("/^\d+$/",$id_success)){
            if($this->session->has("contact") ) {
                $id_request = $this->session->get("contact");
                $this->session->remove("contact");
                if($id_success != $id_request)
                {
                    $this->response->redirect($this->lang_url.'/contact-us');
                    return;
                }
            }
            else{
                $this->response->redirect($this->lang_url.'/contact-us');
                return;
            }
        }

        $user = array();
        if($this->auth) {
            $user = ScUser::findFirstById($this->auth['id']);
            if($user){
                $user = $user->toArray();
            }
        }

        $page = new Page();
        $page->AutoGenMetaPage("contact-us",defined('txt_contact_us') ? txt_contact_us : '',$this->lang_code);
        $page->generateStylePage('contact-us');
        $parent_keyword = 'contact-us';

        $office_repo = new Office();
        $offices = $office_repo->getAllOffice($this->lang_code);
        $coordinates_office = json_encode($office_repo->getCoordinates());

        $article_repo = new Article();
        $services = $article_repo->getServiceCheckbox($this->globalVariable->typeServicesId,$_POST['list_service'],$this->lang_code);

        $validFile = array();
        $validator = new Validator();
        $message = array();
        $uploadFiles = array();
        $data = array();
        if ($this->request->isPost()) {
            $data = array(
                'contact_name' => $this->request->getPost('txtName', array('string', 'trim')),
                'contact_email' => $this->request->getPost('txtEmail', array('string', 'trim')),
                'contact_number' => $this->request->getPost('txtPhone', array('string', 'trim')),
                'contact_telapi_number' => $this->request->getPost('txt_phone_number', array('trim')),
                'contact_country_code' => $this->request->getPost('txt_iso2', array('trim')),
                'contact_subject' => $this->request->getPost('txtSubject'),
                'contact_comment' => $this->request->getPost('txtComment'),
                'contact_communication_channel' => $this->request->getPost('communication_chanel', array('trim')),
                'contact_communication_channel_type' => $this->request->getPost('txtCommunicationChannelType', array('string', 'trim')),
                'contact_communication_channel_other' => $this->request->getPost('txtCommunicationChannelOther', array('string', 'trim')),
                'contact_communication_channel_id' => $this->request->getPost('txtCommunicationChannelID', array('string', 'trim')),
                'contact_communication_channel_number' => $this->request->getPost('txtCommunicationChannelNumber', array('string', 'trim')),
                'contact_communication_channel_telapi_number' => $this->request->getPost('txtPhoneNumberCommunicationChannel', array('trim')),
                'contact_communication_channel_country_code' => $this->request->getPost('txtPhoneIso2CommunicationChannel', array('trim')),
            );
            $contact_tel_communication_format = '';
            if(!empty($_POST['list_service'])) {
                for ($i = 0; $i < count($_POST['list_service']); $i++) {
                    $data['contact_list_services'][] = $_POST['list_service'][$i];
                }
            } else{
                $message['service'] = defined('txt_please_select_topics') ? txt_please_select_topics : '';
            }
            if (!empty($data['contact_communication_channel_type']) && $data['contact_communication_channel_type'] == ScCommunicationChannel::TYPE_PHONE) {
                if (empty($data['contact_communication_channel_number'])) {
                    $message['communicationChannelNumber'] = defined('txt_enter_phone_number') ? txt_enter_phone_number : '';
                }
                $contact_tel_communication = NumVerify::info($data['contact_communication_channel_telapi_number']);
                if($contact_tel_communication->valid){
                    $contact_tel_communication_format = $contact_tel_communication->international_format;
                } else{
                    $message['communicationChannelNumber'] = defined('txt_please_enter_valid_number') ? txt_please_enter_valid_number : '';
                }
            }

            if (!empty($data['contact_communication_channel_type']) && $data['contact_communication_channel_type'] == ScCommunicationChannel::TYPE_TEXT) {
                if (empty($data['contact_communication_channel_id'])) {
                    $message['communicationChannelId'] = defined('txt_please_enter_id') ? txt_please_enter_id : '';
                }
            }

            if (!empty($data['contact_communication_channel_type']) && $data['contact_communication_channel_type'] == ScCommunicationChannel::TYPE_OTHER) {
                if (empty($data['contact_communication_channel_id'])) {
                    $message['communicationChannelId'] = defined('txt_please_enter_id') ? txt_please_enter_id : '';
                }
                if (empty($data['contact_communication_channel_other'])) {
                    $message['communicationChannelOther'] = defined('txt_please_enter_other_channel') ? txt_please_enter_other_channel : '';
                }
            }

            if ($data['contact_communication_channel'] == 0) {
                $message['communicationChannel'] = defined('txt_please_select_communication_channel') ? txt_please_select_communication_channel : '';
            }

            if (empty($data['contact_name'])) {
                $message['name'] = defined('txt_enter_full_name') ? txt_enter_full_name : '';
            }
            if (empty($data['contact_subject'])) {
                $message['subject'] = defined('txt_please_enter_subject') ? txt_please_enter_subject : '';
            }

            if (empty($data['contact_comment'])) {
                $message['comment'] = defined('txt_enter_comment') ? txt_enter_comment : '';
            }

            if (empty($data['contact_email'])) {
                $message['email'] = defined('txt_please_enter_your_email') ? txt_please_enter_your_email : '';
            } else if (!filter_var($data['contact_email'], FILTER_VALIDATE_EMAIL)) {
                $message['email'] = defined('txt_email_not_valid') ? txt_email_not_valid : '';
            }

            if (empty($data['contact_number'])) {
                $message['phone'] = defined('txt_enter_phone_number') ? txt_enter_phone_number : '';
            }

            if ($this->request->hasFiles()) {
                $files = $this->request->getUploadedFiles();
                if (count($files) > 3) {
                    $message['file'] = defined('txt_please_select_file_limit') ? txt_please_select_file_limit : '';
                } else if (count($files) > 0) {
                    foreach ($files as $file) {
                        if (!empty($file->getName())) {
                            $type = $validator->accept($file->getType());
                            if (empty($type)) {
                                $message['file'] = defined('txt_choose_file_valid') ? txt_choose_file_valid : '';
                            } else
                                if ($file->getSize() > $this->globalVariable->maximumSizeUploadFile) {
                                    $message['file'] = defined('txt_please_select_file_size_limit') ? txt_please_select_file_size_limit : '';
                                } else {
                                    $validFile[] = $file;
                                }
                        }
                    }
                }
            }

            if (isset($_POST['g-recaptcha-response']) && !empty($_POST['g-recaptcha-response'])) {
                $secret_key = RECAPTCHA_SECRETKEY;
                $verifyResponse = IpApi::curl_get_contents('https://www.google.com/recaptcha/api/siteverify?secret=' . $secret_key . '&response=' . $_POST['g-recaptcha-response']);
                $responseData = json_decode($verifyResponse);
                if ($responseData->success != 1) {
                    $message['captcha'] = defined('txt_robot_verify_fail') ? txt_robot_verify_fail : '';
                }
            } else {
                $message['captcha'] = defined('txt_click_captcha') ? txt_click_captcha : '';
            }
            $inter_phone_format = '';
            $user_tel_info = NumVerify::info($data['contact_telapi_number']);
            if($user_tel_info->valid){
                $inter_phone_format = $user_tel_info->international_format;
            } else{
                $message['phone'] = defined('txt_please_enter_valid_number') ? txt_please_enter_valid_number : '';
            }
            if (empty($message)){
                \S3::setAuth(MyS3Key, MyS3Secret);
                $bucket = MyS3Bucket;
                foreach ($validFile as $file) {
                    $path_parts = pathinfo($file->getName());
                    $file_extension = strtolower($path_parts['extension']);
                    $convertfileName = str_replace('--', '-', $this->convertkeyword($this->stripunicode($path_parts['filename'])));
                    $fileName = $convertfileName . '-' . time() . '.' . $file_extension;
                    $tmp = $file->getTempName();
                    $key = MyS3UploadFolder . '/' . $fileName;
                    $params = array('Cache-Control' => 'max-age=86400');
                    if (isset($this->globalVariable->acceptUploadTypes[$file_extension])) {
                        $params['Content-Type'] = $this->globalVariable->acceptUploadTypes[$file_extension];
                    }
                    $result = \S3::putObjectFile($tmp, $bucket, $key, \S3::ACL_PUBLIC_READ, array(), $params);
                    if ($result) {
                        $uploadFiles[] = array(
                            "file_name" => $fileName,
                            "file_size" => $file->getSize(),
                            "file_type" => $file_extension,
                            "file_type_info" => $file_extension,
                            "file_url" => MyCloudFrontURL . $key
                        );
                    }
                }
                $count = 0;
                $list_service_contact = '';
                foreach($_POST['list_service'] as $selected){
                    $count++;
                    $list_service_contact .= $selected;
                    if(count($_POST['list_service']) > $count){
                        $list_service_contact .= ',';
                    }
                }

                $new_contact = new ScContactus();
                /**
                 * @var ScContactus $new_contact
                 */
                $new_contact->setContactName($data['contact_name']);
                $new_contact->setContactNumber($inter_phone_format);
                $new_contact->setContactEmail($data['contact_email']);
                $new_contact->setContactSubject($data['contact_subject']);
                $new_contact->setContactContent($data['contact_comment']);
                $new_contact->setContactTopics($list_service_contact);
                $new_contact->setContactCommunicationChannelId($data['contact_communication_channel']);
                $new_contact->setContactCommunicationChannelName(($data['contact_communication_channel_type'] == ScCommunicationChannel::TYPE_OTHER) ? $data['contact_communication_channel_other'] : CommunicationChannel::getCommunicationNameByID($data['contact_communication_channel']));
                $new_contact->setContactCommunicationChannelNumber(($data['contact_communication_channel_type'] == ScCommunicationChannel::TYPE_PHONE) ? $contact_tel_communication_format : $data['contact_communication_channel_id']);
                $getFileUrlFull = array();
                if ($uploadFiles) {
                    foreach ($uploadFiles as $key => $val) {
                        $getFileUrlFull[] = $uploadFiles[$key]['file_url'];
                    }
                }
                $new_contact->setContactFile(implode('<br>', $getFileUrlFull));
                $new_contact->setContactInsertTime($this->globalVariable->curTime);
                $service_names = explode(',', $new_contact->getContactTopics());
                if($new_contact->save()){
                    $message_result = array('status' => 'success', 'msg' => defined('txt_contact_us_success') ? txt_contact_us_success : '');
                    $old_data = array();
                    $new_data = $data;
                    $id = $this->my->formatContactID($new_contact->getContactInsertTime(), $new_contact->getContactId());
                    $data_log = json_encode(array('bin_contactus' => array($new_contact->getContactId() => array($old_data, $new_data))));
                    $activity = new Activity();
                    $activity->logActivity($this->router->getControllerName(), $this->router->getActionName(), $this->auth['id'], '', $data_log);
                    $this->session->set("contact", $id);
                    foreach ($service_names as $service_name){
                        $serviceSendEmail = $article_repo->getByName($service_name,$this->lang_code);
                        if($serviceSendEmail){
                            if(!empty($serviceSendEmail->getArticleEmailSupport())){
                                $this->sendContactUsServiceEmail($new_contact,$serviceSendEmail->getArticleEmailSupport(),$this->lang_code);
                            }
                        }
                    }
                    $this->sendContactUsEmail($new_contact,$this->lang_code);
                } else {
                    $message_result = array('status' => 'error', 'msg' => defined('txt_contact_us_fail') ? txt_contact_us_fail : '');
                }
                $this->session->set('msg_result', $message_result);
                if($message_result['status'] === 'success') {
                    return $this->response->redirect($this->lang_url.'/contact-us?success_id='.$id);
                }
                return $this->response->redirect($this->lang_url.'/contact-us');
            }
        }

        $communicationChannel = $this->request->getPost('communication_chanel', array('string', 'trim'));
        $selectCommunicationChannel = CommunicationChannel::getCommunicationChannel($communicationChannel,$this->lang_code);

        $repoBanner = new Banner();
        $banners = $repoBanner->getBannerByController($this->router->getControllerName(), $this->lang_code);
        $this->view->setVars([
            'parent_keyword' => $parent_keyword,
            'banners'        => $banners,
            'coordinates_office' => $coordinates_office,
            'offices' => $offices,
            'selectCommunicationChannel' => $selectCommunicationChannel,
            'services' => $services,
            'message' => $message,
            'formData' => $data,
            'userData' => $user,
        ]);
    }

    private function convertkeyword($strparama)
    {
        $keywords = strtolower($strparama);
        $keywords = preg_replace('/[^A-Za-z0-9\s]/', '', $keywords);
        $keywords = str_replace(' ', '-', $keywords);
        $keywords = str_replace('--', '-', $keywords);
        return $keywords;
    }

    private function stripunicode($str)
    {
        $unicode = array(
            'a' => 'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd' => 'đ',
            'e' => 'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i' => 'í|ì|ỉ|ĩ|ị',
            'o' => 'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u' => 'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y' => 'ý|ỳ|ỷ|ỹ|ỵ',
            'A' => 'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D' => 'Đ',
            'E' => 'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I' => 'Í|Ì|Ỉ|Ĩ|Ị',
            'O' => 'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U' => 'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y' => 'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach ($unicode as $nonUnicode => $uni) {
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }

    private function sendContactUsEmail($contactus,$lang_code)
    {
        /**
         * @var ScContactus $contactus
         */
        $emailRepo = new EmailTemplate();
        $email_template = $emailRepo->getEmailContactUs($contactus,$lang_code);
        if ($email_template['success'] == true) {
            $contact_id = $this->my->formatContactID($contactus->getContactInsertTime(), $contactus->getContactId());
            $from_email = "noreply@bincg.com";
            $reply_to_email = "support@bincg.com";
            $to_email = $contactus->getContactEmail();
            $subject = $email_template['subject'] . " #" . $contact_id;
            $from_full_name = $reply_to_name = 'BIN Corporation Group';
            $to_full_name = $contactus->getContactName();
            $this->my->sendEmail($from_email, $to_email, $subject, $email_template['content'], $from_full_name, $to_full_name, $reply_to_email, $reply_to_name);

            $from_email = "noreply@bincg.com";
            $to_email = "support@bincg.com";
            $from_full_name = $contactus->getContactName();
            $to_full_name = 'BIN Corporation Group';
            $reply_to_email = $contactus->getContactEmail();
            $reply_to_name = $contactus->getContactName();
            $this->my->sendEmail($from_email, $to_email, $subject, $email_template['content'], $from_full_name, $to_full_name, $reply_to_email, $reply_to_name);
        }
    }

    private function sendContactUsServiceEmail($contactus,$service_email,$lang_code)
    {
        /**
         * @var ScContactus $contactus
         */
        $emailRepo = new EmailTemplate();
        $email_template = $emailRepo->getEmailContactUs($contactus,$lang_code);
        if ($email_template['success'] == true) {
            $contact_id = $this->my->formatContactID($contactus->getContactInsertTime(), $contactus->getContactId());
            $subject = $email_template['subject'] . " #" . $contact_id;
            $from_email = "noreply@bincg.com";
            $to_email = $service_email;
            $from_full_name = $contactus->getContactName();
            $to_full_name = 'BIN Corporation Group';
            $reply_to_email = $contactus->getContactEmail();
            $reply_to_name = $contactus->getContactName();
            $this->my->sendEmail($from_email, $to_email, $subject, $email_template['content'], $from_full_name, $to_full_name, $reply_to_email, $reply_to_name);
        }
    }
}