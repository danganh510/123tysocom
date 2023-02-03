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
    //convert local site time to UTC0
    public function UTCTime($time)
    {
        return $time - $this->globalVariable->timeZone;
    }
    function formatTimeArDetail($time)
    {
        return strftime('%b %d, %Y | %H:%M', $time). " (".$this->globalVariable->gtmStr.")";
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
    public function renderPagination($url, $page, $totalPages, $limit = 0, $attributes = array())
    {
        if ($page < 1) $page = 1;
        if ($totalPages < 1) $totalPages = 1;

        $disablePrevious = $page <= 1;
        $disableNext = $page >= $totalPages;
        $showLeftDot = ($limit != 0) && ($page - $limit > 2);
        $showRightDot = ($limit != 0) && ($page + $limit < $totalPages - 1);
        $showFirstPage = ($limit != 0) && ($page - $limit >= 2);
        $showLastPage = ($limit != 0) && ($page + $limit <= $totalPages - 1);

        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $key = strtolower(trim((string)$key));
            if ($key != 'class') {
                $attributeString .= $key . '="' . $value . ' ';
            }
        }

        $html = '<ul class="pagination end ' . (isset($attributes['class']) ? $attributes['class'] : '') . '" ' . $attributeString . '>';

        if ($disablePrevious) {
            $html .= '<li class="disabled"><span class="item">&laquo;</span></li>';
            $html .= '<li class="disabled"><span class="item">Previous</span></li>';
        } else {
            $html .= '<li><a class="item" href="' . $url . (1) . '" >&laquo;</a></li>';
            $html .= '<li><a class="item" href="' . $url . ($page - 1) . '" >Previous</a></li>';
        }

        if ($limit == 0) {
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($page == $i) {
                    $html .= '<li class="active"><span class="item">' . $i . '</span></li>';
                } else {
                    $html .= '<li><a class="item" href="' . $url . $i . '" >' . $i . '</a></li>';
                }
            }
        } else {
            if ($showFirstPage) $html .= '<li><a class="item" href="' . $url . (1) . '" >1</a></li>';
            if ($showLeftDot) $html .= '<li><a class="item">&hellip;</a></li>';
            for ($i = $page - $limit; $i <= $page + $limit; $i++) {
                if ($i < 1 || $i > $totalPages) continue;
                if ($page == $i) {
                    $html .= '<li class="active"><span class="item">' . $i . '</span></li>';
                } else {
                    $html .= '<li><a class="item" href="' . $url . $i . '" >' . $i . '</a></li>';
                }
            }
            if ($showRightDot) $html .= '<li><a class="item">&hellip;</a></li>';
            if ($showLastPage) $html .= '<li><a class="item" href="' . $url . $totalPages . '" >' . $totalPages . '</a></li>';
        }

        if ($disableNext) {
            $html .= '<li class="disabled"><span class="item">Next</span></li>';
            $html .= '<li class="disabled"><span class="item">&raquo;</span></li>';
        } else {
            $html .= '<li><a class="item" href="' . $url . ($page + 1) . '" >Next</a></li>';
            $html .= '<li><a class="item" href="' . $url . ($totalPages) . '" >&raquo;</a></li>';
        }

        $html .= '</ul>';
        return $html;
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
        $sent_error = new \Bincg\Repositories\SendError();
        $sent_error->sendErrorNotfound('');
        $this->response->redirect($country.'/'.$lang.'/notfound');
    }
    public function ssIpInfo() {
        $ipInfo = \Bincg\Utils\IpApi::info_ip($_SERVER['REMOTE_ADDR']);
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
    public function formatID($idType, $insertTime, $id, $suffix='')
    {
        $insertYear = date('Y', $insertTime);
        $y = substr($insertYear, strlen($insertYear)-1);
        return sprintf("%s%s%04d%s",$idType,$y,$id,$suffix);
    }
    public function formatApplyID($insertTime, $id)
    {
        return $this->formatID(5, $this->localTime($insertTime), $id);
    }
    public function replaceQuotes($string) {
        return html_entity_decode(str_replace(array('"','&#34;','&quot;'),"'",preg_replace( "/\r|\n/", "", strip_tags($string))),ENT_QUOTES);
    }

    function getComboBox($array, $value = '')
    {
        $string="";
        foreach ($array as $key => $item){
            $selected ="";
            if($key == $value){
                $selected ="selected='selected'";
            }
            $string.="<option ".$selected."  value='".$key."'>".$item."</option>";
        }
        return $string;
    }
    public function frontendPagination($url, $page, $totalPages, $limit = 0, $attributes = array())
    {
        if ($page < 1) $page = 1;
        if ($totalPages < 1) $totalPages = 1;

        $disablePrevious = $page <= 1;
        $disableNext = $page >= $totalPages;
        $showLeftDot = ($limit != 0) && ($page - $limit > 2);
        $showRightDot = ($limit != 0) && ($page + $limit < $totalPages - 1);
        $showFirstPage = ($limit != 0) && ($page - $limit >= 2);
        $showLastPage = ($limit != 0) && ($page + $limit <= $totalPages - 1);
        $attributeString = '';
        foreach ($attributes as $key => $value) {
            $key = strtolower(trim((string)$key));
            if ($key != 'class') {
                $attributeString .= $key . '="' . $value . ' ';
            }
        }

        $html = '<ul class="list-inline text-center ' . (isset($attributes['class']) ? $attributes['class'] : '') . '" ' . $attributeString . '>';

        if (!$disablePrevious) {
            $html .= '<li><a href="' . $url . ($page - 1) . '" target="_self"><strong>'.'&lt;'.'</strong></a></li>';
        }
        if ($limit == 0) {
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($page == $i) {
                    $html .= '<li class="active"><a href="" target="_self"><strong>' . $i . '</strong></a></li>';
                } else {
                    $html .= '<li><a target="_self" href="' . $url . $i . '" ><strong>' . $i . '</strong></a></li>';
                }
            }
        } else {
            if ($showFirstPage) $html .= '<li><a  href="' . $url . (1) . '" ><strong>1</strong></a></li>';
            if ($showLeftDot) $html .= '<li><a>&hellip;</a></li>';
            for ($i = $page - $limit; $i <= $page + $limit; $i++) {
                if ($i < 1 || $i > $totalPages) continue;
                if ($page == $i) {
                    $html .= '<li class="active"><a href="" target="_self"><strong>' . $i . '</strong></a></li>';
                } else {
                    $html .= '<li><a href="' . $url . $i . '" ><strong>' . $i . '</strong></a></li>';
                }
            }
            if ($showRightDot) $html .= '<li><a>&hellip;</a></li>';
            if ($showLastPage) $html .= '<li><a href="' . $url . $totalPages . '" ><strong>' . $totalPages . '</strong></a></li>';
        }

        if (!$disableNext) {
            $html .= '<li><a target="_self" href="' . $url . ($page + 1) . '" ><strong>'.'&gt;'.'</strong></a></li>';
        }

        $html .= '</ul>';
        return $html;
    }
}

