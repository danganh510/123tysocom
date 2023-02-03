<?php

namespace Bincg\Backend\Controllers;

use Bincg\Repositories\Activity;
use Bincg\Repositories\Subscribe;
use Bincg\Repositories\User;
use Bincg\Repositories\UserAgent;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Bincg\Utils\Validator;
class SubscribeController extends ControllerBase
{
    public function indexAction()
    {
        $data = $this->getParameter();
        $btn_export = $this->request->getPost("btnExportcsv");
        $list_newletter = $this->modelsManager->executeQuery($data['sql'],$data['para']);
        $current_page = $this->request->get('page');
        $keyword = $this->request->get('txtSearch',array('string', 'trim'));
        $from = $this->dispatcher->getParam('txtFrom');
        $to = $this->dispatcher->getParam('txtTo');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $paginator = new PaginatorModel(
            [
                'data'  => $list_newletter,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'keyword' => $keyword,
            'from' => $from,
            'to' => $to,
        ));
        if(isset($btn_export)){
            $this->view->disable();
            $results[] = array("Id","Email","Insert Time");
            foreach ($list_newletter as $item)
            {
                $subscribe_email = array(
                    $item->getSubscribeId(),
                    $item->getSubscribeEmail(),
                    $this->my->formatTimeAr($item->getSubscribeInsertTime(),false),
                );
                $results[] = $subscribe_email;
            }
            header('Content-Type: text/csv; charset=utf-8');
            header('Content-Disposition: attachment; filename=subscribe_email_'.time().'.csv');
            $out = fopen('php://output', 'w');
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            return;
        }
    }
    public function viewAction()
    {
        $subscribe_id = $this->request->get('id');
        $checkID = new Validator();
        if(!$checkID->validInt($subscribe_id))
        {
            $this->response->redirect('notfound');
            return ;
        }
        $newletter = new Subscribe();
        $subscribe_model = $newletter->getByID($subscribe_id);
        if(empty($subscribe_model))
        {
            $this->response->redirect('notfound');
            return;
        }
        $arr_newletter = array('newletter' => $subscribe_model, 'location_info' => array(), 'user_agent' => array());
        $activityRepo = new Activity();
        $activities = $activityRepo->getByControllerAndAction($this->controllerName, 'index');
        foreach ($activities as $activity){
            $list_subscribe = json_decode($activity['activity_data_log'], true);
            if (array_key_exists($subscribe_id, $list_subscribe['bin_subscribe'])) {
                $location_info = array('ip_address' => $activity['activity_ip'], 'location' => '');
                $user = User::getFirstUserByUserId($activity['activity_user_id']);
                if($user) $location_info['location'] = $user->getUserTelapiCountryName();
                $arr_newletter['location_info'] = $location_info;
                $userAgent = UserAgent::getFirstUserAgentById($activity['activity_user_id']);
                if($userAgent) $arr_newletter['user_agent'] =  $userAgent;
                break;
            }
        }
        $this->view->arr_newletter = $arr_newletter;
    }
    private function getParameter(){
        $sql = "SELECT * FROM Bincg\Models\BinSubscribe WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (subscribe_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (subscribe_ip like CONCAT('%',:keyword:,'%') OR subscribe_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if($from){
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND subscribe_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if($to){
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND subscribe_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        $sql.=" ORDER BY subscribe_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}