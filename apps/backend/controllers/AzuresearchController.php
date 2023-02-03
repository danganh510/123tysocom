<?php

namespace Score\Backend\Controllers;

use Score\Repositories\Activity;
use Score\Repositories\SearchAzure;
use Score\Repositories\AzureSearch;
use Score\Repositories\Language;

class AzuresearchController extends ControllerBase
{
    public function indexAction()
    {
        if (!defined('AZURE_MODE') || !AZURE_MODE) {
            return $this->response->redirect("notfound");
        }
        $upload = array();
        $success = array();
        $alert = array();

        $limit = 1000;
        $numUpload = 0;
        $numUploadSuccess = 0;
        $arrLog = array();
        if ($this->request->getPost('upload_azure')) {
            //Upload Azure Search
            $search = new SearchAzure();
            $result = $search->getCache();
            $resultMerge = array();
            $list_language = Language::findAllLanguageCodes();
            foreach ($list_language as $lang){
                $resultMerge = array_merge($resultMerge,isset($result[$lang])?$result[$lang]:array());
            }
            while (count($resultMerge) > 0) {
                $numUpload++;
                $uploadCapacity = count($resultMerge) > $limit ? $limit : count($resultMerge);
                $resultSlice = array_splice($resultMerge, 0, $uploadCapacity);
                $upload = AzureSearch::updateIndexesDocs($resultSlice);
                if (isset($upload['status']) && ($upload['status'] == 'success')) {
                    $numUploadSuccess++;
                }
                else {
                    break;
                }
                $arrLog[]= isset($upload['result']['value'])?$upload['result']['value']:array();
            }
            if ($numUploadSuccess > 0 && ($numUpload == $numUploadSuccess)) {
                $success[] = 'Upload Docs success';
                $messageLog = 'Upload Azure Logs success';
                $dataLogAzure = json_encode($arrLog);
                $activity = new Activity();
                $activity->logActivity($this->controllerName, 'upload azure', $this->auth['id'], $messageLog, $dataLogAzure);
            }
            else {
                $alert[] = isset($upload['message'])?$upload['message']:'';
            }
        }

        //Create Azure Search
        if($this->request->getPost('create_azure'))
        {
            $createDatasources = AzureSearch::createDatasources();
            $createIndexes = AzureSearch::createIndexes();
            $createIndexers = AzureSearch::createIndexers();

            if (isset($createDatasources) && $createDatasources['status'] == 'success') {
                $success[] = 'Create Datasources success';
            }
            else {
                $alert[] = isset($createDatasources['message'])?$createDatasources['message']:'';
            }

            if (isset($createIndexes) && $createIndexes['status'] == 'success') {
                $success[] = 'Create Indexes success';
            }
            else {
                $alert[] = isset($createIndexes['message'])?$createIndexes['message']:'';
            }

            if (isset($createIndexers) && $createIndexers['status'] == 'success') {
                $success[] = 'Create Indexers success';
            }
            else {
                $alert[] = isset($createIndexers['message'])?$createIndexers['message']:'';
            }
        }
        //Delete Azure Search
        if($this->request->getPost('delete_azure'))
        {
            $deleteDatasources = AzureSearch::deleteDatasources();
            $deleteIndexes = AzureSearch::deleteIndexes();
            $deleteIndexers = AzureSearch::deleteIndexers();

            if (isset($deleteDatasources) && $deleteDatasources['status'] == 'success') {
                $success[] = 'Delete Datasources success';
            }
            else {
                $alert[] = isset($deleteDatasources['message'])?$deleteDatasources['message']:'';
            }

            if (isset($deleteIndexes) && $deleteIndexes['status'] == 'success') {
                $success[] = 'Delete Indexes success';
            }
            else {
                $alert[] = isset($deleteIndexes['message'])?$deleteIndexes['message']:'';
            }

            if (isset($deleteIndexers) && $deleteIndexers['status'] == 'success') {
                $success[] = 'Delete Indexers success';
            }
            else {
                $alert[] = isset($deleteIndexers['message'])?$deleteIndexers['message']:'';
            }
        }
        $message = implode('<br>',$success);
        $messageAlert = implode('<br>',$alert);
        $this->view->setVars(array(
            'message' => $message,
            'messageAlert' => $messageAlert
        ));
    }
}
