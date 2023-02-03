<?php

namespace Score\Backend\Controllers;

use Score\Repositories\Career;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Score\Utils\Validator;
class ApplyController extends ControllerBase
{
    public function indexAction()
    {
        $career_id = $this->request->get('slcCareer');
        $data = $this->getParameter($career_id);
        $list_apply = $this->modelsManager->executeQuery($data['sql'],$data['para']);
        $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $paginator = new PaginatorModel(
            [
                'data'  => $list_apply,
                'limit' => 20,
                'page'  => $current_page,
            ]
        );
        $btn_export = $this->request->getPost("btnExportcsv");
        if(isset($btn_export)){
            $this->view->disable();
            $results[] = array("Id", "Carrer", "User Name", "User Email", "User Telephone", "CV","Communication channel number","Communication channel number", "Insert Time");
            foreach ($list_apply as $item)
            {
                $user = array(
                    $this->my->formatApplyID($item->getApplyInsertTime(), $item->getApplyId()),
                    Career::getNameByID($item->getApplyCareerId()),
                    $item->getApplyName(),
                    $item->getApplyEmail(),
                    $item->getApplyTel(),
                    $item->getApplyCv(),
                    $item->getApplyCommunicationChannelName(),
                    $item->getApplyCommunicationChannelNumber(),
                    $this->my->formatDateTime($item->getApplyInsertTime(),false),
                );
                $results[] = $user;
            }
            header('Content-Encoding: UTF-8');
            header('Content-Type: text/csv; charset=UTF-8');
            header('Content-Disposition: attachment; filename=apply_'.time().'.csv');
            echo "\xEF\xBB\xBF";
            $out = fopen('php://output', 'w');
            foreach ($results as $fields) {
                fputcsv($out, $fields);
            }
            fclose($out);
            die();
        }
        $select_career = Career::getComboBox($career_id);
        $this->view->setVars(array(
            'page' => $paginator->getPaginate(),
            'select_career' => $select_career,
        ));
    }

    private function getParameter($career_id = ""){
        $sql = "SELECT * FROM Score\Models\ScApply WHERE 1";
        $keyword = trim($this->request->get("txtSearch"));
        $from = trim($this->request->get("txtFrom")); //string
        $to = trim($this->request->get("txtTo"));  //string
        $arrParameter = array();
        $validator = new Validator();
        if(!empty($keyword)) {
            if($validator->validInt($keyword)) {
                $sql.= " AND (apply_id = :number:)";
                $arrParameter['number'] = $keyword;
            }
            else {
                $sql.=" AND (apply_name like CONCAT('%',:keyword:,'%') OR apply_email like CONCAT('%',:keyword:,'%'))";
                $arrParameter['keyword'] = $keyword;
            }
            $this->dispatcher->setParam("txtSearch", $keyword);
        }
        if($from){
            $intFrom = $this->my->UTCTime(strtotime($from)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND apply_insert_time >= :from:";
            $arrParameter['from'] = $intFrom;
            $this->dispatcher->setParam("txtFrom", $from);
        }
        if($to){
            $intTo = $this->my->UTCTime(strtotime($to)); //UTC_mysql_time = date_picker - time zone
            $sql .= " AND apply_insert_time <= :to:";
            $arrParameter['to'] = $intTo;
            $this->dispatcher->setParam("txtTo", $to);
        }
        if ($career_id) {
            $sql.= " AND (apply_career_id = :career_id:)";
            $arrParameter['career_id'] = $career_id;
            $this->dispatcher->setParam("slcCareer", $career_id);
        }
        $sql.=" ORDER BY apply_insert_time DESC";
        $data['para'] = $arrParameter;
        $data['sql'] = $sql;
        return $data;
    }
}