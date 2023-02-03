<?php

namespace Bincg\Backend\Controllers;

use Bincg\Models\BinTableTranslate;
use Bincg\Repositories\Activity;
use General\Models\Country;
use General\Models\LanguageSupportTranslate;

class TranslateController extends ControllerBase
{
    public function indexAction()
    {
        $array_models = array();
        $directory_frontend =__DIR__."/../../models/*Lang.php";
        foreach (glob($directory_frontend) as $controller) {
            $className = basename($controller, '.php');
            array_push($array_models,$className);
        }
//        foreach ($array_models as $str_model_lang){
//            $translateItem = BinTableTranslate::getByName($str_model_lang);
//            if(!$translateItem){
//                $newTranslateItem = new BinTableTranslate();
//                $newTranslateItem->setTranslateTable($str_model_lang);
//                $newTranslateItem->setTranslateOrder(1);
//                $newTranslateItem->setTranslateActive('Y');
//                $newTranslateItem->save();
//            }
//        }
        $repoLang = new LanguageSupportTranslate();
        $listLang = $repoLang->getAll();
        $data = array('active' => 'Y','order' => 1);
        $messages = array();
        $msg_result = array();
        if ($this->request->isPost()){
            $data = array(
                'language' => $this->request->getPost('slcLanguage', array('trim')),
                'order' => $this->request->getPost('txtOrder', array('string','trim')),
                'active' => $this->request->getPost("radActive"),
            );
            if(!empty($_POST['backendtranslate'])) {
                for ($i = 0; $i < count($_POST['backendtranslate']); $i++) {
                    $data['list_table'][] = $_POST['backendtranslate'][$i];
                }
            }
            if (empty($data["language"])) {
                $messages["language"] = "Please choose language";
            }
            if (empty($data["order"])) {
                $messages["order"] = "Order field is required.";
            } elseif (!is_numeric($data["order"])) {
                $messages["order"] = "Order is not valid ";
            }
            $old_data = array();
            if (empty($messages)){
                $check_translate = BinTableTranslate::checkCodeCountry($data["language"]);
                if ($check_translate) {
                    $old_data = array (
                        'language' => $check_translate->getTranslateLanguage(),
                        'order' => $check_translate->getTranslateOrder(),
                        'active' => $check_translate->getTranslateActive(),
                        'list_table' => $check_translate->getTranslateTable()
                    );
                }
                $new_translate = new BinTableTranslate();
                /**
                 * @var BinTableTranslate $new_translate
                 */
                $new_translate->setTranslateLanguage($data['language']);
                $new_translate->setTranslateTable(json_encode($data['list_table']));
                $new_translate->setTranslateActive($data['active']);
                $new_translate->setTranslateOrder($data['order']);
                $result = $new_translate->save();
                $message = "We can't store your info now: " . "<br/>";
                if ($result === true){
                    $activity = new Activity();
                    $message = 'Create the translate language: ' . $new_translate->getTranslateLanguage() . ' success<br>';
                    $status = 'success';
                    $msg_result = array('status' => $status, 'msg' => $message);
                    $new_data = $data;
                    $data_log = json_encode(array('content_article' => array($new_translate->getTranslateId() => array($old_data, $new_data))));
                    $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                } else {
                    foreach ($new_translate->getMessages() as $msg) {
                        $message .= $msg . "<br/>";
                    }
                    $msg_result = array('status' => 'error', 'msg' => $message);
                }
            }
        }
        $this->view->setVars(array(
            'list_translate' => $array_models,
            'list_lang' => $listLang,
            'formData' => $data,
            'messages' => $messages,
            'msg_result' => $msg_result
        ));
    }
}
