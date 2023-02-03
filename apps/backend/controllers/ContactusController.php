<?php

namespace Score\Backend\Controllers;
use Score\Models\ScContactus;
use Score\Repositories\Activity;
use Score\Repositories\User;
use Score\Repositories\UserAgent;
use Score\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
class ContactusController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $keyword = $this->dispatcher->getParam("txtSearch");
        $from = $this->dispatcher->getParam('txtFrom');
        $to = $this->dispatcher->getParam('txtTo');
        $list_contactus = $this->modelsManager->executeQuery($data['sql'],$data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $paginator = new PaginatorModel(
            [
                'data'  => $list_contactus,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'from' => $from,
            'to' => $to,
            'keyword' => $keyword
        ));
    }
    public function viewAction()
    {
        $contactus_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($contactus_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $contactus_model = ScContactus::findFirstById($contactus_id);
        if(empty($contactus_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_contactus = array('contactus' => $contactus_model, 'location_info' => array(), 'user_agent' => array());
        $activityRepo = new Activity();
        $activities = $activityRepo->getByControllerAndAction($this->controllerName, 'index');
        foreach ($activities as $activity){
            $list_contactus = json_decode($activity['activity_data_log'], true);
            if (array_key_exists($contactus_id, $list_contactus['bin_contactus'])) {
                $location_info = array('ip_address' => $activity['activity_ip'], 'location' => '');
                $user = User::getFirstUserByUserId($activity['activity_user_id']);
                if($user) $location_info['location'] = '';
                $arr_contactus['location_info'] = $location_info;
                $userAgent = UserAgent::getFirstUserAgentById($activity['activity_user_agent_id']);
                if($userAgent) $arr_contactus['user_agent'] =  $userAgent;
                break;
            }
        }
        $this->view->arr_contactus = $arr_contactus;
    }
    private function getParameter(){
        $sql = "SELECT * FROM Score\Models\ScContactus WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (contact_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (contact_name like CONCAT('%',:keyword:,'%') OR contact_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if($from){
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND contact_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if($to){
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND contact_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql.=" ORDER BY contact_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}
