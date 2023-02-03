<?php

namespace Score\Backend\Controllers;

use Score\Google\GoogleTranslate;

class TooltranslateController extends ControllerBase
{
    public function indexAction() {
        $data = array(
            'slLanguageFrom' => $this->globalVariable->defaultLanguage,
            'slLanguageTo' => 'vi',
        );
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('data')) {
            $data = $this->session->get('data');
            $this->session->remove('data');
        }
        $message_error = '';
        $messages = array();
        if($this->request->isPost()){
            ini_set('max_execution_time', 3600);
            $data = array(
                'txtFrom' => $this->request->getPost('txtFrom'),
                'txtTo' => $this->request->getPost('txtTo'),
                'slLanguageFrom' => $this->request->getPost('slLanguageFrom'),
                'slLanguageTo' => $this->request->getPost('slLanguageTo'),
            );
            if (empty($data["txtFrom"])) {
                $messages["message_from"] = "This field is required.";
            }
            if (empty($data["slLanguageFrom"])) {
                $messages["message_language_from"] = "Language is required.";
            }
            if (empty($data["slLanguageTo"])) {
                $messages["message_language_to"] = "Language is required.";
            }
            if (!empty($data["slLanguageFrom"]) && !empty($data["slLanguageTo"])) {
                if($data["slLanguageFrom"] == $data["slLanguageTo"]){
                    $messages["message_language_to"] = "Language is duplicate. Please try again.";
                }
            }
            if (count($messages) == 0) {
                try {
                    require_once(__DIR__ . '/../../library/google-cloud-translate/vendor/autoload.php');
                    $googleTranslate = new GoogleTranslate();
                    $googleTranslate->setGlossaryId($data["slLanguageFrom"], $data["slLanguageTo"]);

                    $content_translate = $googleTranslate->translate($data["txtFrom"], $data["slLanguageTo"], 'text/html', $data["slLanguageFrom"]);
                    if ($content_translate["status"] == "fail") {
                        $message_error .= "Content: " . $content_translate["errorcode"] . " - " . $content_translate["errormessage"] . "<br>";
                    }
                    if (strlen($message_error) == 0) {
                        $data["txtTo"] = $content_translate['data'];
                        $msg_result = array('status' => 'success', 'msg' => 'Translate success', 'data' => $content_translate['data']);
                    } else {
                        $msg_result = array('status' => 'error', 'msg' => 'Translate fail', 'data' => '');
                    }
                } catch (\Exception $e) {
                    $msg_result = array('status' => 'error', 'msg' => $e->getMessage(), 'data' => '');
                }
            } else {
                $msg_result = array('status' => 'border-red', 'msg' => $messages, 'data' => '');
            }
            $msg_result['slLanguageFrom'] = $data["slLanguageFrom"];
            $msg_result['slLanguageTo'] = $data["slLanguageTo"];
            $msg_result['txtFrom'] = $data["txtFrom"];
            $this->session->set('msg_result',$msg_result );
            $this->session->set('data',$data);
            $this->response->redirect("/dashboard/tool-translate");
            return;
        }
        $slLanguageFrom  = self::getCombobox($data['slLanguageFrom']);
        $slLanguageTo  = self::getCombobox($data['slLanguageTo']);
        $this->view->slLanguageFrom = $slLanguageFrom;
        $this->view->slLanguageTo = $slLanguageTo;
    }

    public static function getCombobox($code){
        $data = ['en' => 'English', 'vi' => 'Vietnamese'];
        $string = "";
        foreach($data as $key => $item){
            $seleted = "";
            if($key == $code) {
                $seleted = "selected='selected'";
            }
            $string.="<option ".$seleted." value='".$key."'>".strtoupper($key)." - ".$item."</option>";
        }
        return $string;
    }

}