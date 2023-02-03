<?php

namespace Score\Repositories;
use Score\Models\ScUser;
use Score\Models\ScRole;
use Phalcon\Mvc\User\Component;

class User extends Component {
    /**
     * @var ScUser $user
     * @var ScRole $role
     */
    public function initSession($user,$role){
        if ($user) {            
            $role_name = ($role)?$role->getRoleName():"user";
            $this->session->set('auth', array(
                'id' => $user->getUserId(),
                'name' => $user->getUserName(),
                'email' => $user->getUserEmail(),
                'role' => $role_name,
                'insertTime' => $user->getUserInsertTime(),
            ));
        }
        return false;
    }
    public function redirectLogged($pre = "") {
        if ($this->session->has('preURL')){
            $preURL = $this->session->get('preURL');
            $this->session->remove('preURL');
            if (strlen($preURL)>1 && $preURL != "/"){
                $this->response->redirect($preURL);
                return;
            }
        }
        if($pre == "")
            $this->response->redirect("my-account");
        else
            $this->response->redirect($pre);
    }

    public static function getByLimit($limit){
        return ScUser::find(array(
            "order"      => "user_insert_time DESC",
            "limit"      => $limit,
        ));
    }
    public static function getFirstUserByUserId($user_id) {
        return ScUser::findFirst(array(
            'user_id = :user_id:',
            'bind' => array('user_id' => $user_id)
        ));
    }
}
