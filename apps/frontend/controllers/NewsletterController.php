<?php

namespace Bincg\Frontend\Controllers;
use Bincg\Models\BinSubscribe;
use Bincg\Utils\IpApi;
class NewsletterController extends ControllerBase
{
    public function indexAction()
    {
        $this->view->disable();
        if($this->request->isPost()){
            $email = $this->request->getPost("rgs_mail", "email");
            $captcha = $this->request->getPost('token','trim');
            if(empty($captcha)) {
                $msg_email = defined('txt_error_validating_the_captcha') ? txt_error_validating_the_captcha : '';
                echo json_encode(array(
                    "status"   => "error",
                    "msg"   => $msg_email
                ));
                die;
            }
            $secret_key = RECAPTCHA_SECRETKEY_V3;
            $verifyResponse = IpApi::curl_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=" . $secret_key . "&response=" . $captcha . "&remoteip=" . $_SERVER['REMOTE_ADDR']);
            $responseData = json_decode($verifyResponse);
            if ($responseData->success === false) {
                $msg_email = defined('txt_robot_verify_fail') ? txt_robot_verify_fail : '';
                echo json_encode(array(
                    "status"   => "error",
                    "msg"   => $msg_email
                ));
                die;
            }
            if ((strlen($email)) < 1 ) {
                $msg_email = defined('txt_enter_email') ? txt_enter_email : '';
                echo json_encode(array(
                    "status"   => "error",
                    "msg"   => $msg_email
                ));
            } else if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $msg_email = defined('txt_email_invalid') ? txt_email_invalid : '';
                echo json_encode(array(
                    "status"   => "error",
                    "msg"   => $msg_email
                ));
            }
            else{
                $result_msg = $this->checkExistEmail($email);
                if(strlen($result_msg)>0){
                    echo json_encode(array(
                        "status"   => "error",
                        "msg"   => $result_msg
                    ));
                }else{
                    $subsc = new BinSubscribe();
                    $subsc->setSubscribeEmail($email);
                    $subsc->setSubscribeIp($this->getIp());
                    $subsc->setSubscribeInsertTime($this->globalVariable->curTime);
                    $result_insert = $subsc->save();
                    if($result_insert){
                        $msg_email = defined('txt_subscriber_successfully') ? txt_subscriber_successfully : 'Register Successfully !';
                        echo json_encode(array(
                            "status"   => "success",
                            "msg"   => $msg_email
                        ));
                    }
                }

            }
        }
    }
    private function checkExistEmail($email){
        $msg = '';
        $exist_email = BinSubscribe::findFirst(array(
            "subscribe_email = :email: ",
            "bind" => array("email" => $email)
        ));
        if ($exist_email){
            $msg = defined('txt_already_subscriber_email') ? txt_already_subscriber_email : 'This email is already registered!';
        }
        return $msg;
    }
    private function getIp() {
        if (isset($_SERVER['HTTP_CLIENT_IP']))
            $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
        else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_X_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
        else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
            $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
        else if(isset($_SERVER['HTTP_FORWARDED']))
            $ipaddress = $_SERVER['HTTP_FORWARDED'];
        else if(isset($_SERVER['REMOTE_ADDR']))
            $ipaddress = $_SERVER['REMOTE_ADDR'];
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;

    }
}