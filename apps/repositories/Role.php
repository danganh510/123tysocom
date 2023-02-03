<?php

namespace Bincg\Repositories;

use Bincg\Models\BinRole;
use Phalcon\Mvc\User\Component;

class Role extends Component {

    // get by ID Role
    public static function getByID($role_id) {
        return BinRole::findFirst(array(
            'role_id = :role_id:',
            'bind' => array('role_id' => $role_id)
        ));
    }
    // get by Name Role
    public static function getByName($role_name, $role_id) {
        return BinRole::findFirst(array(
            'role_name = :role_name: AND role_id != :role_id:',
            'bind' => array('role_id' => $role_id,
                             'role_name' => $role_name)
           ));
    }
    public static function getFirstByName($role_name){
        return BinRole::findFirst(array(
            'role_name = :role_name:',
            'bind' => array('role_name' => $role_name)
        ));
    }
    // get Function Role
    public function getFunctionRole($num_of_cols, $resources, $values, $prefix) {
        $row_count = 0;
        $bootstrap_col_width = 12 / $num_of_cols;
        $default_actions = BinRole::getActions();
        $result ="<div class='row'>";
        foreach ($resources as $key => $items) {
            $name = str_replace($prefix, '', $key);
            $result_control_head = "";
            $result_control_end = "";
            $default_control = array();
            if(isset($default_actions[$key]))
                $default_control = $default_actions[$key];
            if (count($items) > count($default_control)) {
                $result_control_head = "<div class='role_block col-md-" . $bootstrap_col_width . "'>
                <div class='well'><h2 class='text-danger'>" . ucfirst($name) ."</h2>"    ;
                $result_control_end = "</div></div>";
                $row_count ++;
            }
            $result .=$result_control_head;

            foreach ($items as $item) {
                if(!in_array($item, $default_control)) {
                    $seletced = (isset($values[$key]) && in_array($item, $values[$key])) ? 'checked' : '';
                    $result .=
                        "<label class='container_checkbox'> ".ucfirst($item)."
                        <input type='checkbox' class='form-control check' name='" . $key . "[]' id='" . $key . $item . "' " . $seletced . " value='" . $item . "' />
                        <span class='checkmark_checkbox'></span>
                    </label><div class='clearfix'></div>";
                }
            }
            $result .=$result_control_end;
            if( $row_count % $num_of_cols == 0) $result .="</div><div class='row'>";
        }
        $result .="</div>";
        return $result;
    }
    public static function checkMenu($role_action,$role_default){
        $result = false;
        foreach ($role_default as $controller) {
            if(isset($role_action[$controller])&& in_array('index',$role_action[$controller])) {
                $result = true;
                break;
            }
        }
        return $result;
    }
    public static function checkMenuSub($role_action,$role_current){
        $result = false;
        $pre = "backend";
        if(in_array($pre.$role_current, $role_action)){
            $result = true;
        }
        return $result;
    }
    public static function checkiMenuItem($role_action,$role_current){
        $result = false;
        $pre = "backend";
        if(isset($role_action[$pre.$role_current])&& in_array('index',$role_action[$pre.$role_current])) {
            $result = true;
        }
        return $result;
    }
    public static  function getAllActive()
    {
        return BinRole::find("role_active ='Y'");
    }
    public static function getComboBox ($role_id)
    {
        $data =  BinRole::find(array("order" => "role_order"));

        $output="";
        foreach ($data as  $value)
        {
            $selected ="";
            if($value->getRoleId()== $role_id)
            {
                $selected ="selected = 'selected'";
            }
            $output.= "<option ".$selected." value=".$value->getRoleId().">".$value->getRoleName()."</option>";
        }
        return $output;
    }
    public static function getNameRole($role_id) {
        $role= BinRole::findFirst(array(
            'role_id = :role_id:',
            'bind' => array('role_id' => $role_id)
        ));
        return ($role)?$role->getRoleName():'';
    }
}
