<?php

namespace Score\Repositories;
use Phalcon\Mvc\User\Component;
use Score\Utils\UserAgent;
use Score\Models\ScActivity;
use Score\Models\ScIp;
use Score\Models\ScUserAgent;
use Score\Utils\IpApi;

class Activity extends Component
{
    // get By Controller And Action
    public function getByControllerAndAction($activity_controller, $activity_action) {
        return ScActivity::find(array(
            'columns' => 'activity_user_id, activity_data_log, activity_ip, activity_user_agent_id',
            'activity_controller = :activity_controller: AND activity_action = :activity_action:',
            'bind' => array('activity_controller' => $activity_controller, 'activity_action' => $activity_action)
        ));
    }
    public function logActivity($controller, $action, $user_id, $message, $data_log){
        $ip = $_SERVER['REMOTE_ADDR'];
        $agent = $_SERVER['HTTP_USER_AGENT'];
        $info = array();

        $activity = new ScActivity();
        $activity->setActivityController($controller);
        $activity->setActivityAction($action);
        $activity->setActivityUserId($user_id);
        $activity->setActivityDateCreated($this->globalVariable->curTime);
        $activity->setActivityIp($ip);
        $activity->setActivityMessage($message);
        $activity->setActivityDataLog($data_log);

        $checkUser_Agent = ScUserAgent::findFirstByUserAgent($agent);
        if ($checkUser_Agent) {
            if($checkUser_Agent->getAgentChecked() == 'N'){
                $userAgent = UserAgent::info($agent);
                if (!empty($userAgent)) {
                    $info = $this->setUserAgent($userAgent);
                    $info->save();
                }
            }
            $activity->setActivityUserAgentId($checkUser_Agent->getAgentId());
        }
        else {
            $userAgent = UserAgent::info($agent);
            if (!empty($userAgent)) {
                $info = $this->setUserAgent($userAgent);

                if($info->save()){
                    $activity->setActivityUserAgentId($info->getAgentId());
                }else{
                    $info = new ScUserAgent();
                    $info->setAgentChecked('N');
                    $info->setAgentUserAgent($_SERVER['HTTP_USER_AGENT']);
                    $info->save();
                    $activity->setActivityUserAgentId($info->getAgentId());
                }
            }else{
                $info = new ScUserAgent();
                $info->setAgentChecked('N');
                $info->setAgentUserAgent($_SERVER['HTTP_USER_AGENT']);
                $info->save();
                if($info->save()){
                    $activity->setActivityUserAgentId($info->getAgentId());
                }else {
                    $activity->setActivityUserAgentId(0);
                }
            }
        }

        $erpIp = ScIp::findFirstByIpAddress($ip);
        if($erpIp){
            if($erpIp->getIpDateModified() == 0){
                $ip = IpApi::info_ip($_SERVER['REMOTE_ADDR']);
                if($ip){
                    $erpIp->setIpDateModified($this->globalVariable->curTime);
                    $erpIp->setWithIpInfo($ip);
                    $erpIp->update();
                }
            }
        }else{
            $newErpIp = new ScIp();
            $newErpIp->setIpQuery($_SERVER['REMOTE_ADDR']);
            $ip = IpApi::info_ip($_SERVER['REMOTE_ADDR']);
            if($ip){
                $newErpIp->setWithIpInfo($ip);
            }
            $newErpIp->save();
        }

        if($activity->save())
            return array('activity' => $activity, 'info' => $info);
        return array();
    }
    private function setUserAgent($userAgent) {
        $info = new ScUserAgent();
        $info->setAgentChecked('Y');
        $info->setAgentUserAgent($_SERVER['HTTP_USER_AGENT']);
        $info->setAgentHardwareType($userAgent->hardware_type);
        $info->setAgentOperatingSystemName($userAgent->operating_system_name);
        $info->setAgentSoftwareSubType($userAgent->software_sub_type);
        $info->setAgentSimpleSubDescriptionString($userAgent->simple_sub_description_string);
        $info->setAgentSimpleBrowserString($userAgent->simple_browser_string);
        $info->setAgentBrowserVersion($userAgent->browser_version);
        $info->setAgentSoftwareType($userAgent->software_type);
        $info->setAgentExtraInfo(json_encode($userAgent->extra_info));
        $info->setAgentOperatingPlatform($userAgent->operating_platform);
        $info->setAgentExtraInfoTable(json_encode($userAgent->extra_info_table));
        $info->setAgentLayoutEngineName($userAgent->layout_engine_name);
        $info->setAgentOperatingSystemFlavourCode($userAgent->operating_system_flavour_code);
        $info->setAgentDetectedAddons(json_encode($userAgent->detected_addons));
        $info->setAgentOperatingSystemFlavour($userAgent->operating_system_flavour);
        $info->setAgentOperatingSystemFrameworks(json_encode($userAgent->operating_system_frameworks));
        $info->setAgentBrowserNameCode($userAgent->browser_name_code);
        $info->setAgentSimpleMinor($userAgent->simple_minor);
        $info->setAgentOperatingSystemVersion($userAgent->operating_system_version);
        $info->setAgentSimpleOperatingPlatformString($userAgent->simple_operating_platform_string);
        $info->setAgentIsAbusive($userAgent->is_abusive);
        $info->setAgentSimpleMedium($userAgent->simple_medium);
        $info->setAgentLayoutEngineVersion($userAgent->layout_engine_version);
        $info->setAgentBrowserCapabilities(json_encode($userAgent->browser_capabilities));
        $info->setAgentOperatingPlatformVendorName($userAgent->operating_platform_vendor_name);
        $info->setAgentOperatingSystem($userAgent->operating_system);
        $info->setAgentHardwareArchitecture($userAgent->hardware_architecture);
        $info->setAgentOperatingSystemVersionFull($userAgent->operating_system_version_full);
        $info->setAgentOperatingPlatformCode($userAgent->operating_platform_code);
        $info->setAgentBrowserName($userAgent->browser_name);
        $info->setAgentOperatingSystemNameCode($userAgent->operating_system_name_code);
        $info->setAgentSimpleMajor($userAgent->simple_major);
        $info->setAgentBrowserVersionFull($userAgent->browser_version_full);
        $info->setAgentBrowser($userAgent->browser);
        return $info;
    }
}