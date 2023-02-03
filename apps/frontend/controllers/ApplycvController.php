<?php
namespace Score\Frontend\Controllers;
use Score\Models\ScApply;
use Score\Models\ScCommunicationChannel;
use Score\Repositories\Activity;
use Score\Repositories\Career;
use Score\Repositories\CommunicationChannel;
use Score\Repositories\EmailTemplate;
use Score\Utils\NumVerify;
use Score\Utils\Validator;

define('MyS3UploadFolder', 'applycv');
class ApplycvController extends ControllerBase
{
    public function applycvAction()
    {
        if(!$this->request->isPost()){
            $this->my->sendErrorEmailAndRedirectToNotFoundPage($this->lang_code, $this->location_code);
            return;
        }
        $message = array();
        $uploadFiles = array();
        $validFile = array();
        $data = array(
            'apply_name' => $this->request->getPost('txtName', array('string', 'trim')),
            'apply_email' => $this->request->getPost('txtEmail', array('string', 'trim')),
            'apply_tel' => $this->request->getPost('txtPhone', array('string', 'trim')),
            'apply_career_id' => $this->request->getPost('slcRole', array('string', 'trim')),
            'apply_telapi_number' => $this->request->getPost('txt_phone_number', array('trim')),
            'apply_communication_channel' => $this->request->getPost('communication_chanel', array('trim')),
            'apply_communication_channel_type' => $this->request->getPost('txtCommunicationChannelType', array('string', 'trim')),
            'apply_communication_channel_other' => $this->request->getPost('txtCommunicationChannelOther', array('string', 'trim')),
            'apply_communication_channel_id' => $this->request->getPost('txtCommunicationChannelID', array('string', 'trim')),
            'apply_communication_channel_number' => $this->request->getPost('txtCommunicationChannelNumber', array('string', 'trim')),
            'apply_communication_channel_telapi_number' => $this->request->getPost('txtPhoneNumberCommunicationChannel', array('trim')),
            'apply_communication_channel_country_code' => $this->request->getPost('txtPhoneIso2CommunicationChannel', array('trim')),
        );
        $user_tel_info = NumVerify::info($data['apply_telapi_number']);
        $inter_phone_format = ($user_tel_info->valid) ? $user_tel_info->international_format : $data['apply_tel'];
        $apply_tel_communication = NumVerify::info($data['apply_communication_channel_telapi_number']);
        $apply_tel_communication_format = ($apply_tel_communication->valid) ? $apply_tel_communication->international_format : $data['apply_communication_channel_telapi_number'];
        if (empty($data['apply_name'])) {
            $message['name'] = 'txt_enter_full_name';
        }
        if (empty($data['apply_email'])) {
            $message['email'] = 'txt_enter_email';
        } else if (!filter_var($data['apply_email'], FILTER_VALIDATE_EMAIL)) {
            $message['email'] = 'txt_enter_valid_email';
        }
        if (empty($data['apply_tel'])) {
            $message['phone'] = 'txt_enter_phone_number';
        } elseif (!$user_tel_info->valid) {
            $message['phone'] = 'txt_enter_valid_phone_number';
        }
        if (empty($data['apply_career_id'])) {
            $message['career'] = 'txt_enter_content';
        }
        if (!empty($data['apply_communication_channel_type']) && $data['apply_communication_channel_type'] == ScCommunicationChannel::TYPE_PHONE) {
            if (empty($data['apply_communication_channel_number'])) {
                $message['communicationChannelNumber'] = defined('txt_enter_phone_number') ? txt_enter_phone_number : '';
            } elseif (!$apply_tel_communication->valid){
                $message['communicationChannelNumber'] = 'txt_enter_valid_phone_number';
            }
        }

        if (!empty($data['apply_communication_channel_type']) && $data['apply_communication_channel_type'] == ScCommunicationChannel::TYPE_TEXT) {
            if (empty($data['apply_communication_channel_id'])) {
                $message['communicationChannelId'] = defined('txt_please_select_topics') ? txt_please_select_topics : '';
            }
        }

        if (!empty($data['apply_communication_channel_type']) && $data['apply_communication_channel_type'] == ScCommunicationChannel::TYPE_OTHER) {
            if (empty($data['apply_communication_channel_id'])) {
                $message['communicationChannelId'] = defined('txt_please_select_topics') ? txt_please_select_topics : '';
            }
            if (empty($data['apply_communication_channel_other'])) {
                $message['communicationChannelOther'] = defined('txt_please_enter_other_channel') ? txt_please_enter_other_channel : '';
            }
        }

        if ($data['apply_communication_channel'] == 0) {
            $message['communicationChannel'] = defined('txt_please_select_communication_channel') ? txt_please_select_communication_channel : '';
        }
        if ($this->request->hasFiles()) {
            $files = $this->request->getUploadedFiles();
            if(count($files) > 3){
                $message['file'] = 'txt_select_file_limit';
            }elseif (count($files) <= 0){
                $message['file'] = 'txt_select_file_required';
            }else{
                foreach ($files as $file)
                {
                    if(!empty($file->getName())) {
                        $validator = new Validator();
                        $type = $validator->acceptCV($file->getType());
                        if (empty($type)) {
                            $message['file'] = 'txt_select_correct_file_format';
                        } else
                            if ($file->getSize() > $this->globalVariable->maximumSizeUploadFile) {
                                $message['file'] ='txt_select_file_size_limit';
                            } else {
                                $validFile[] = $file;
                            }
                    }
                }
            }
        }
        if (empty($message)) {
            \S3::setAuth(MyS3Key, MyS3Secret);
            $bucket = MyS3Bucket;
            foreach ($validFile as $file)
            {
                $path_parts = pathinfo($file->getName());
                $file_extension = strtolower($path_parts['extension']);
                $convertfileName = str_replace('--','-',$this->convertkeyword($this->stripunicode($path_parts['filename'])));
                $fileName = $convertfileName.'-'.time().'.'.$file_extension;
                $tmp = $file->getTempName();
                $key = MyS3UploadFolder.'/'.$fileName;
                $params = array('Cache-Control' => 'max-age=86400');
                if(isset($this->globalVariable->contentTypeImages[$file_extension])){
                    $params['Content-Type'] = $this->globalVariable->contentTypeImages[$file_extension];
                }
                $result = \S3::putObjectFile($tmp, $bucket , $key, \S3::ACL_PUBLIC_READ,array(),$params);
                if($result) {
                    $uploadFiles[] = array(
                        "file_name" => $fileName,
                        "file_size" => $file->getSize(),
                        "file_type" => $file_extension,
                        "file_type_info" => $file_extension,
                        "file_url" => MyCloudFrontURL . $key
                    );
                }
            }
            $new_apply = new ScApply();
            /**
             * @var ScApply $new_apply
             */
            $new_apply->setApplyName($data['apply_name']);
            $new_apply->setApplyEmail($data['apply_email']);
            $new_apply->setApplyTel($inter_phone_format);
            $getFileName = array();
            if($uploadFiles){
                foreach ($uploadFiles as $key => $val) {
                    $getFileName[] = $uploadFiles[$key]['file_url'];
                }
            }
            $new_apply->setApplyCv(implode('<br>',$getFileName));
            $new_apply->setApplyCareerId($data['apply_career_id']);
            $new_apply->setApplyInsertTime($this->globalVariable->curTime);
            $new_apply->setApplyCommunicationChannelId($data['apply_communication_channel']);
            $new_apply->setApplyCommunicationChannelName(($data['apply_communication_channel_type'] == ScCommunicationChannel::TYPE_OTHER) ? $data['apply_communication_channel_other'] : CommunicationChannel::getCommunicationNameByID($data['apply_communication_channel']));
            $new_apply->setApplyCommunicationChannelNumber(($data['apply_communication_channel_type'] == ScCommunicationChannel::TYPE_PHONE) ? $apply_tel_communication_format : $data['apply_communication_channel_id']);
            if ($new_apply->save() === true) {
                $message = array('status' => 'success', 'msg' => 'txt_contact_us_success');
                $old_data = array();
                $new_data = $data;
                $data_log = json_encode(array('bin_apply' => array($new_apply->getApplyId() => array($old_data, $new_data))));
                $activity = new Activity();
                $user_agent = $activity->logActivity($this->router->getControllerName(), $this->router->getActionName(), '', '', $data_log);
                $this->sendApplCVEmail($new_apply, $user_agent);
            } else {
                $message = array('status' => 'error', 'msg' => 'txt_contact_us_fail');
            }
        }else {
            $message = array('status' => 'error', 'msg' => 'txt_contact_us_fail');
        }
        die(json_encode($message));
    }
    /**
     * @param ScApply $apply
     * @param $user_agent
     */
    private function sendApplCVEmail($apply, $user_agent) {
        $emailRepo = new EmailTemplate();
        $email_template = $emailRepo->getEmailApplyCV($apply);
        if($email_template['success'] == true) {
            $from_email = $reply_to_email = "support@bincg.com";
            $to_email = $apply->getApplyEmail();
            $time = $apply->getApplyInsertTime()+$this->globalVariable->timeZone;
            $subject = "Application for placement  ".Career::getNameByID($apply->getApplyCareerId()). " ". date('Y-m-d H:i:s', $time);
            $from_full_name = $reply_to_name = 'BIN Corporation Group';
            $to_full_name = $apply->getApplyName();
            $this->my->sendEmail($from_email, $to_email, $subject,$email_template['content'] , $from_full_name, $to_full_name, $reply_to_email, $reply_to_name);

            $from_email = "noreply@bincg.com";
            $to_email = "hr@bincg.com";
            $from_full_name = $apply->getApplyName();
            $to_full_name = 'BIN Corporation Group';
            $reply_to_email = $apply->getApplyEmail();
            $reply_to_name = $apply->getApplyName();
            $this->my->sendEmail($from_email, $to_email, $subject, $email_template['content'], $from_full_name, $to_full_name, $reply_to_email, $reply_to_name);
        }
    }
    private function convertkeyword($strparama){
        $keywords = strtolower($strparama);
        $keywords = preg_replace('/[^A-Za-z0-9\s]/', '', $keywords);
        $keywords = str_replace(' ','-',$keywords);
        $keywords = str_replace('--','-',$keywords);
        return $keywords;
    }
    private function stripunicode($str){
        $unicode = array(
            'a'=>'á|à|ả|ã|ạ|ă|ắ|ặ|ằ|ẳ|ẵ|â|ấ|ầ|ẩ|ẫ|ậ',
            'd'=>'đ',
            'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
            'i'=>'í|ì|ỉ|ĩ|ị',
            'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
            'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
            'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
            'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ặ|Ằ|Ẳ|Ẵ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
            'D'=>'Đ',
            'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
            'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
            'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
            'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
            'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ',
        );
        foreach($unicode as $nonUnicode=>$uni){
            $str = preg_replace("/($uni)/i", $nonUnicode, $str);
        }
        return $str;
    }
}