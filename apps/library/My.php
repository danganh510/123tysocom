<?php
/**
 * My
 *
 * My Common functions
 */
class My extends Phalcon\Mvc\User\Component
{
    /**
     * Using for add external JS sources
     * @param mixed $src
     */
    public function addJsSource($src) {
        $jsSources = isset($this->view->jsSources) ? $this->view->jsSources : array();
        array_push($jsSources, $src);
        $this->view->jsSources = $jsSources;
    }
    public function getAPi($method, $params, $url) {
        
    }

    public function localTime($time)
    {
        return $time + $this->globalVariable->timeZone;
    }
    function formatDateTime($time, $showUtc = true)
    {
        //return date('d M Y , H:i', $this->localTime($time)) . " (UTC+08:00)";
        return strftime('%m/%d/%Y %H:%M:%S', $this->localTime($time)).($showUtc?" (".$this->globalVariable->gtmStr.")":'');
    }
    function formatTimeAr($time)
    {
        return strftime('%B %d, %Y', $time);
    }
    function formatDateTo3Month($time)
    {
        return strftime('%B %d, %Y', strtotime("+3 month", $time));
    }
    function formatDateYMDTo3Month($time)
    {
        return date('Y-m-d', strtotime("+3 month", $time));
    }
    function formatDateYMD($time)
    {
        return date('Y-m-d', $time);
    }
    
	//send email
    public function sendEmail($fromEmail, $toEmail, $subject, $message, $fromFullName, $toFullName, $reply_to_email, $reply_to_name)
    {
        if (defined('EMAIL_TEST_MODE') && EMAIL_TEST_MODE && defined('EMAIL_TEST_EMAIL')) {
            $toEmail = EMAIL_TEST_EMAIL;
        }
        /** @var \PHPMailer $mail */
        $mail = $this->myMailer;
        if (!$mail) return array('success' => false, 'message' => 'Mail is null!');
        $result = array();
        try {
            //reply to
            $mail->AddReplyTo($reply_to_email, $reply_to_name);
            //
            $mail->SetFrom($fromEmail, '=?utf-8?B?'.base64_encode($fromFullName).'?='); //from (verified email address)
            $mail->Subject = '=?utf-8?B?'.base64_encode((defined('EMAIL_SUBJECT_PREFIX') ? EMAIL_SUBJECT_PREFIX : '') . $subject).'?='; //subject
            //message
            $body = $message;
            //$body = preg_replace("/\\/i",'',$body);
            $mail->MsgHTML($body);
            //
            //recipient
            $mail->AddAddress($toEmail, $toFullName);

            // add bbc
            //$mail->AddBCC($bbc_email, $bbc_name);
            //Success
            $isSent = $mail->Send();
            if ($isSent) {
                $result['success'] = true;
                $result['message'] = "Message sent!";
            }
            else
            {
                $result['success'] = false;
                $result['message'] = "Mailer Error: " . $mail->ErrorInfo;
            }
        } catch (phpmailerException $e) {
            $result['success'] = false;
            $result['message'] = "Mailer Error: " . $e->errorMessage();//Pretty error messages from PHPMailer
        } catch (Exception $e) {
            $result['success'] = false;
            $result['message'] = "Mailer Error: " . $e->getMessage();//Boring error messages from anything else!
        }
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->ClearAttachments();
        return $result;
    }
   
    public function sendError($message,$title)
    {
        if (defined('EMAIL_TEST_MODE') && EMAIL_TEST_MODE && defined('EMAIL_TEST_EMAIL')) {
            $toEmail = EMAIL_TEST_EMAIL;
        }else{
            $toEmail = 'dev-error@bin.com.vn';
        }
        /** @var \PHPMailer $mail */
        $mail = $this->myMailer;
        if (!$mail) return array('success' => false, 'message' => 'Mail is null!');
        //reply to
        $mail->AddReplyTo("noreply@bincg.com", "Noreply Sender");
        $mail->SetFrom("noreply@bincg.com", "BINCG System Error");
        //get date
        $date_now = date("Y-m-d H:i:s");
        $mail->Subject = (defined('EMAIL_SUBJECT_PREFIX') ? EMAIL_SUBJECT_PREFIX : '') . "System BINCG Error - " ."[".$date_now."]" . " - " . $title; //subject
        //message
        $body = $message;
        //$body = eregi_replace("[\]",'',$body);
        $mail->MsgHTML($body);
        //
        //recipient
        $whitelist = array('127.0.0.1', "::1");
        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)) {
            $mail->AddAddress($toEmail, "BINCG System Error");
        }
        // add bbc
        //$mail->AddBCC($bbc_email, $bbc_name);
        //Success
        $isSent = $mail->Send();
        $result = array();
        if ($isSent) {
            $result['success'] = true;
            $result['message'] = "Message sent!";
        }
        //Error
        if(!$isSent) {
            $result['success'] = false;
            $result['message'] = "Mailer Error: " . $mail->ErrorInfo;
        }
        $mail->ClearAllRecipients();
        $mail->ClearReplyTos();
        $mail->ClearAttachments();
        return $result;
    }
    public function sendErrorEmailAndRedirectToNotFoundPage($lang = 'en', $country = 'gx')
    {
        $sent_error = new \Score\Repositories\SendError();
        $sent_error->sendErrorNotfound('');
        $this->response->redirect($country.'/'.$lang.'/notfound');
    }
    public function ssIpInfo() {
        $ipInfo = \Score\Utils\IpApi::info_ip($_SERVER['REMOTE_ADDR']);
        if ($ipInfo->status == 'success') {
            $this->session->set('ssIpInfo', serialize($ipInfo));
            return $ipInfo;
        }
        else {
            return null;
        }
    }
    public function formatContactID($insertTime, $id)
    {
        return $this->formatID(4, $this->localTime($insertTime), $id);
    }


}

