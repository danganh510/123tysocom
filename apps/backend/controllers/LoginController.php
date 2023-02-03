<?php
namespace Score\Backend\Controllers;

use Score\Models\ScRole;
use Phalcon\Mvc\View;
use Score\Utils\PasswordGenerator;
use Score\Models\ScUser;
use Score\Repositories\Activity;
use Score\Repositories\User;
use Score\Utils\Validator;

class LoginController extends ControllerBase
{
    public function indexAction()
    {
        if($this->session->has('auth')){
            $this->response->redirect('dashboard/');
            return;
        }
        if($this->session->has('msg_login')){
            $this->view->msg_login = $this->session->get('msg_login');
            $this->session->remove('msg_login');
        }
        if ($this->request->isPost()) {

            $validate = new Validator();
            $libpassgenerator = new PasswordGenerator();
            $email = trim($this->request->getPost('email'));
            $password = trim($this->request->getPost('password'));
            $this->view->email = $email;
            $this->view->password = $password;

            $validLogin = true;
            if (strlen($email) < 1) {
                $this->view->msgErrorEmail = "This field cannot be empty.";
                $validLogin = false;
            } else if (strlen($email) > 255 || !$validate->validEmail($email)) {
                $this->view->msgErrorEmail = "Enter a valid email";
                $validLogin = false;
            } else {
                $this->view->msgErrorEmail = "";
            }
            if (strlen($password) < 1 || strlen($password) > 255) {
                $this->view->msgErrorPass = "This field cannot be empty.";
                $validLogin = false;
            } else {
                $this->view->msgErrorPass = "";
            }

            if ($validLogin) {
                $user = ScUser::findFirstByEmail($email);
                if ($user) {
                    $role = ScRole::getFirstLoginById($user->getUserRoleId());
                    $controllerClass = $this->dispatcher->getControllerClass();
                    if (($role)||(strpos($controllerClass, 'Frontend') !== false)){
                        $cur_pass = $user->getUserPassword();
                        $passArray = explode("$", $cur_pass);
                       if (isset($passArray[0]) && isset($passArray[1]) && isset($passArray[2])) {
                            $auth_pass = $passArray[2];
                            $salt = $passArray[1];
                            $iteration = $passArray[0];
                            $hash_pass = $libpassgenerator->decodePass($password, $salt, $iteration);
                            if ($auth_pass == $hash_pass) {
                                $user_repo = new User();
                                $ativityRepo = new Activity();
                                $logChangeData = array();
                                $user_id = $user->getUserId();
                                $message = '';
                                $user_repo->initSession($user, $role);
                                $user_repo->redirectLogged("dashboard/");
                                $data_log = json_encode($logChangeData);
                                $ativityRepo->logActivity($this->controllerName, $this->actionName, $user_id, $message, $data_log);
                                return;
                            } else {
                                $this->view->msgErrorLogin = "Email or password not correct";
                            }
                        } else {
                            $this->view->msgErrorLogin = "Email or password not correct";
                        }
                    } else {
                        $this->view->msgErrorLogin = "User not granted permissions";
                    }
                } else {
                    $this->view->msgErrorLogin = "Email or password not correct";
                }
            }
        }
        $this->view->disableLevel(array(
            View::LEVEL_LAYOUT => false,
            View::LEVEL_MAIN_LAYOUT => false
        ));
        $this->tag->setTitle('Login');
        $this->view->pick('login/index');
    }
    public function logoutAction(){
        $ativityRepo = new Activity();
        $logChangeData = array();
        $user_id = $this->auth['id'];
        $message = '';
        $data_log = json_encode($logChangeData);
        $ativityRepo->logActivity($this->controllerName, $this->actionName, $user_id, $message, $data_log);
        $this->session->destroy();
        $this->response->redirect('dashboard/login');
        return;
    }
}