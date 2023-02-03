<?php

namespace Score\Backend\Controllers;

use Score\Repositories\Role;

class ControllerBase extends \Phalcon\Mvc\Controller
{
	protected $auth;
	public function initialize()
    {
        $controllerName = $this->dispatcher->getControllerName();
        $this->view->controllerName = $this->controllerName = $controllerName;
        $actionName = $this->dispatcher->getActionName();
        $this->view->actionName = $this->actionName = $actionName;
        //current user
        $this->auth = $this->session->get('auth');
        if (isset($this->auth['role'])) {
            $role_function  = array();
            if ($this->session->has('action')) {
                $role_function = $this->session->get('action');
            } else {
                $role = Role::getFirstByName($this->auth['role']);
                if($role) {
                    $role_function = unserialize($role->getRoleFunction());
                    $this->session->set('action', $role_function);
                }
            }
            $this->view->role_function = $role_function;
        }
		$this->auth = $this->session->get('auth');
    }
}
