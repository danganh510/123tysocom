<?php
namespace Bincg\Backend\Controllers;
use Bincg\Models\BinLocation;
use Bincg\Repositories\CountryGeneral;
use Bincg\Utils\Validator;
use Phalcon\Paginator\Adapter\Model as PaginatorModel;
use Bincg\Repositories\Activity;
use Bincg\Repositories\Location;

class LocationController extends ControllerBase
{
    public function indexAction()
    {
            $current_page = $this->request->get('page');
        $validator = new Validator();
        if($validator->validInt($current_page) == false || $current_page < 1)
            $current_page=1;
        $country = strtolower($this->request->get('slCountry'));
        $sql = "SELECT * FROM Bincg\Models\BinLocation WHERE 1";
        $arrParameter = array();
        if(!empty($country)){
            $sql.=" AND (location_country_code = :countryCODE:)";
            $arrParameter['countryCODE'] = $country;
            $this->dispatcher->setParam("country", $country);
        }
        $sql.=" ORDER BY location_country_code ASC";
        $list_location = $this->modelsManager->executeQuery($sql,$arrParameter);
        $paginator = new PaginatorModel(array(
            'data'  => $list_location,
            'limit' => 20,
            'page'  => $current_page,
        ));
        if ($this->session->has('msg_result')) {
            $msg_result = $this->session->get('msg_result');
            $this->session->remove('msg_result');
            $this->view->msg_result = $msg_result;
        }
        if ($this->session->has('msg_del')) {
            $msg_result = $this->session->get('msg_del');
            $this->session->remove('msg_del');
            $this->view->msg_del = $msg_result;
        }
        $country_combobox = CountryGeneral::getCountryCombobox(strtoupper($country));
        $this->view->slCountry = $country_combobox;
        $this->view->list_location = $paginator->getPaginate();
    }

    public function createAction()
    {
        $data = array('location_id' => -1,'location_active' => 'Y', 'location_order' => 1, );
        if($this->request->isPost()) {
            $messages = array();
            $data = array(
                'location_country_code' => strtolower($this->request->getPost("slcCountry", array('string', 'trim'))),
                'location_lang_code' => $this->request->getPost("slcLanguage", array('string', 'trim')),
                'location_schema_contactpoint' => trim($this->request->getPost('txtSchemaContactpoint')),
                'location_schema_social' => trim($this->request->getPost('txtSchemaSocial')),
                'location_newspaperstalk' => trim($this->request->getPost('txtNewspaperstalk')),
                'location_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'location_active' => $this->request->getPost("radActive"),
            );
            if (empty($data["location_country_code"])) {
                $messages["country"] = "Country field is required.";
            }
            if (empty($data["location_lang_code"])) {
                $messages["language"] = "Language field is required.";
            }
            if (!empty($data['location_country_code']) && !empty($data['location_lang_code'])) {
                if (Location::checkCode($data["location_country_code"], $data["location_lang_code"], -1)) {
                    $messages["language"] = "Language is exists";
                }
            }
            if (empty($data['location_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["location_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $new_location = new BinLocation();
                $new_location->setLocationCountryCode($data["location_country_code"]);
                $new_location->setLocationLangCode($data["location_lang_code"]);
                $new_location->setLocationSchemaContactpoint($data["location_schema_contactpoint"]);
                $new_location->setLocationSchemaSocial($data["location_schema_social"]);
                $new_location->setLocationNewspaperstalk($data["location_newspaperstalk"]);
                $new_location->setLocationOrder($data["location_order"]);
                $new_location->setLocationActive($data["language_active"]);
                $result = $new_location->save();
                $data_log = json_encode(array());
                if ($result === true) {
                    $msg_result = array('status' => 'success', 'msg' => 'Create Location Success');
                    $old_data = array();
                    $new_data = $data;
                    $data_log = json_encode(array('content_location' => array($new_location->getLocationId() => array($old_data, $new_data))));

                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($new_location->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-location");
            }
        }
        $select_country = CountryGeneral::getCountryCombobox(strtoupper($data['location_country_code']));
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $data,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    /**
     * @return \Phalcon\Http\Response|\Phalcon\Http\ResponseInterface
     */
    public function editAction()
    {
        $id = $this->request->get('id');
        $checkID = new Validator();
        if (!$checkID->validInt($id)) {
            return $this->response->redirect('notfound');
        }
        $location_model = BinLocation::findFirstById($id);
        if (empty($location_model)) {
            return $this->response->redirect('notfound');
        }
        $model_data = array(
            'location_id' => $location_model->getLocationId(),
            'location_country_code' => $location_model->getLocationCountryCode(),
            'location_lang_code' => $location_model->getLocationLangCode(),
            'location_schema_contactpoint' => $location_model->getLocationSchemaContactpoint(),
            'location_schema_social' => $location_model->getLocationSchemaSocial(),
            'location_newspaperstalk' => $location_model->getLocationNewspaperstalk(),
            'location_order' => $location_model->getLocationOrder(),
            'location_active' => $location_model->getLocationActive(),
        );
        $input_data = $model_data;
        $messages = array();
        if ($this->request->isPost()) {
            $data = array(
                'location_id' => $id,
                'location_country_code' => strtolower($this->request->getPost("slcCountry", array('string', 'trim'))),
                'location_lang_code' => $this->request->getPost("slcLanguage", array('string', 'trim')),
                'location_schema_contactpoint' => trim($this->request->getPost('txtSchemaContactpoint')),
                'location_schema_social' => trim($this->request->getPost('txtSchemaSocial')),
                'location_newspaperstalk' => trim($this->request->getPost('txtNewspaperstalk')),
                'location_order' => $this->request->getPost("txtOrder", array('string', 'trim')),
                'location_active' => $this->request->getPost("radActive"),
            );
            $input_data = $data;
            if (empty($data["location_country_code"])) {
                $messages["country"] = "Country field is required.";
            }
            if (empty($data["location_lang_code"])) {
                $messages["language"] = "Language field is required.";
            }
            if (!empty($data['location_country_code']) && !empty($data['location_lang_code'])){
                if(Location::checkCode($data["location_country_code"],$data["location_lang_code"],$id)){
                    $messages["language"] = "Language is exists";
                }
            }
            if (empty($data['location_order'])) {
                $messages["order"] = "Order field is required.";
            } else if (!is_numeric($data["location_order"])) {
                $messages["order"] = "Order is not valid ";
            }
            if (count($messages) == 0) {
                $msg_result = array();
                $location_model->setLocationCountryCode($data["location_country_code"]);
                $location_model->setLocationLangCode($data["location_lang_code"]);
                $location_model->setLocationSchemaContactpoint($data["location_schema_contactpoint"]);
                $location_model->setLocationSchemaSocial($data["location_schema_social"]);
                $location_model->setLocationNewspaperstalk($data["location_newspaperstalk"]);
                $location_model->setLocationOrder($data["location_order"]);
                $location_model->setLocationActive($data["location_active"]);
                $result = $location_model->update();
                $data_log = json_encode(array());
                if ($result === true) {
                    $old_data = $model_data;
                    $new_data = $input_data;
                    $data_log = json_encode(array('content_location' => array($id => array($old_data, $new_data))));
                    $msg_result = array('status' => 'success', 'msg' => 'Edit location ID = '.$id.' Success');
                } else {
                    $message = "We can't store your info now: \n";
                    foreach ($location_model->getMessages() as $msg) {
                        $message .= $msg . "\n";
                    }
                    $msg_result['status'] = 'error';
                    $msg_result['msg'] = $message;
                }
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
                $this->session->set('msg_result', $msg_result);
                return $this->response->redirect("/dashboard/list-location");
            }
        }
        $select_country = CountryGeneral::getCountryCombobox(strtoupper($input_data['location_country_code']));
        $messages["status"] = "border-red";
        $this->view->setVars([
            'formData' => $input_data,
            'messages' => $messages,
            'select_country' => $select_country,
        ]);
    }

    public function deleteAction()
    {
        $location_checked = $this->request->getPost("item");
        if (!empty($location_checked)) {
            $location_log = array();
            foreach ($location_checked as $id) {
                $location_item = BinLocation::findFirstById($id);
                if ($location_item) {
                    $msg_result = array();
                    if ($location_item->delete() === false) {
                        $message_delete = 'Can\'t delete the Location ID = '.$location_item->getLocationId();
                        $msg_result['status'] = 'error';
                        $msg_result['msg'] = $message_delete;
                    } else {
                        $old_data = array(
                            'location_id' => $location_item->getLocationId(),
                            'location_country_code' => $location_item->getLocationCountryCode(),
                            'location_lang_code' => $location_item->getLocationLangCode(),
                            'location_schema_contactpoint' => $location_item->getLocationSchemaContactpoint(),
                            'location_schema_social' => $location_item->getLocationSchemaSocial(),
                            'location_newspaperstalk' => $location_item->getLocationNewspaperstalk(),
                            'location_order' => $location_item->getLocationOrder(),
                            'location_active' => $location_item->getLocationActive(),
                        );
                        $location_log[$id] = $old_data;
                    }
                }
            }
            if (count($location_log) > 0) {
                $message_delete = 'Delete '. count($location_log) .' Location successfully.';
                $msg_result['status'] = 'success';
                $msg_result['msg'] = $message_delete;
                $message = '';
                $data_log = json_encode(array('bin_location' => $location_log));
                $activity = new Activity();
                $activity->logActivity($this->controllerName, $this->actionName, $this->auth['id'], $message, $data_log);
            }
            $this->session->set('msg_result', $msg_result);
            return $this->response->redirect("/dashboard/list-location");
        }
    }
}