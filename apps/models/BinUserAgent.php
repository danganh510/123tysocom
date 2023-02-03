<?php

namespace Bincg\Models;

class BinUserAgent extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $agent_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $agent_user_agent;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $agent_checked;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_hardware_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_software_sub_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_sub_description_string;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_browser_string;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser_version;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_software_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_extra_info;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_platform;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_extra_info_table;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_layout_engine_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_flavour_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_detected_addons;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_flavour;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_frameworks;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser_name_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_minor;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_version;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_operating_platform_string;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_is_abusive;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_medium;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_layout_engine_version;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser_capabilities;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_platform_vendor_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_hardware_architecture;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_version_full;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_platform_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_operating_system_name_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_simple_major;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser_version_full;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $agent_browser;

    /**
     * Method to set the value of field agent_id
     *
     * @param integer $agent_id
     * @return $this
     */
    public function setAgentId($agent_id)
    {
        $this->agent_id = $agent_id;

        return $this;
    }

    /**
     * Method to set the value of field agent_user_agent
     *
     * @param string $agent_user_agent
     * @return $this
     */
    public function setAgentUserAgent($agent_user_agent)
    {
        $this->agent_user_agent = $agent_user_agent;

        return $this;
    }

    /**
     * Method to set the value of field agent_checked
     *
     * @param string $agent_checked
     * @return $this
     */
    public function setAgentChecked($agent_checked)
    {
        $this->agent_checked = $agent_checked;

        return $this;
    }

    /**
     * Method to set the value of field agent_hardware_type
     *
     * @param string $agent_hardware_type
     * @return $this
     */
    public function setAgentHardwareType($agent_hardware_type)
    {
        $this->agent_hardware_type = $agent_hardware_type;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_name
     *
     * @param string $agent_operating_system_name
     * @return $this
     */
    public function setAgentOperatingSystemName($agent_operating_system_name)
    {
        $this->agent_operating_system_name = $agent_operating_system_name;

        return $this;
    }

    /**
     * Method to set the value of field agent_software_sub_type
     *
     * @param string $agent_software_sub_type
     * @return $this
     */
    public function setAgentSoftwareSubType($agent_software_sub_type)
    {
        $this->agent_software_sub_type = $agent_software_sub_type;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_sub_description_string
     *
     * @param string $agent_simple_sub_description_string
     * @return $this
     */
    public function setAgentSimpleSubDescriptionString($agent_simple_sub_description_string)
    {
        $this->agent_simple_sub_description_string = $agent_simple_sub_description_string;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_browser_string
     *
     * @param string $agent_simple_browser_string
     * @return $this
     */
    public function setAgentSimpleBrowserString($agent_simple_browser_string)
    {
        $this->agent_simple_browser_string = $agent_simple_browser_string;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser_version
     *
     * @param string $agent_browser_version
     * @return $this
     */
    public function setAgentBrowserVersion($agent_browser_version)
    {
        $this->agent_browser_version = $agent_browser_version;

        return $this;
    }

    /**
     * Method to set the value of field agent_software_type
     *
     * @param string $agent_software_type
     * @return $this
     */
    public function setAgentSoftwareType($agent_software_type)
    {
        $this->agent_software_type = $agent_software_type;

        return $this;
    }

    /**
     * Method to set the value of field agent_extra_info
     *
     * @param string $agent_extra_info
     * @return $this
     */
    public function setAgentExtraInfo($agent_extra_info)
    {
        $this->agent_extra_info = $agent_extra_info;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_platform
     *
     * @param string $agent_operating_platform
     * @return $this
     */
    public function setAgentOperatingPlatform($agent_operating_platform)
    {
        $this->agent_operating_platform = $agent_operating_platform;

        return $this;
    }

    /**
     * Method to set the value of field agent_extra_info_table
     *
     * @param string $agent_extra_info_table
     * @return $this
     */
    public function setAgentExtraInfoTable($agent_extra_info_table)
    {
        $this->agent_extra_info_table = $agent_extra_info_table;

        return $this;
    }

    /**
     * Method to set the value of field agent_layout_engine_name
     *
     * @param string $agent_layout_engine_name
     * @return $this
     */
    public function setAgentLayoutEngineName($agent_layout_engine_name)
    {
        $this->agent_layout_engine_name = $agent_layout_engine_name;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_flavour_code
     *
     * @param string $agent_operating_system_flavour_code
     * @return $this
     */
    public function setAgentOperatingSystemFlavourCode($agent_operating_system_flavour_code)
    {
        $this->agent_operating_system_flavour_code = $agent_operating_system_flavour_code;

        return $this;
    }

    /**
     * Method to set the value of field agent_detected_addons
     *
     * @param string $agent_detected_addons
     * @return $this
     */
    public function setAgentDetectedAddons($agent_detected_addons)
    {
        $this->agent_detected_addons = $agent_detected_addons;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_flavour
     *
     * @param string $agent_operating_system_flavour
     * @return $this
     */
    public function setAgentOperatingSystemFlavour($agent_operating_system_flavour)
    {
        $this->agent_operating_system_flavour = $agent_operating_system_flavour;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_frameworks
     *
     * @param string $agent_operating_system_frameworks
     * @return $this
     */
    public function setAgentOperatingSystemFrameworks($agent_operating_system_frameworks)
    {
        $this->agent_operating_system_frameworks = $agent_operating_system_frameworks;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser_name_code
     *
     * @param string $agent_browser_name_code
     * @return $this
     */
    public function setAgentBrowserNameCode($agent_browser_name_code)
    {
        $this->agent_browser_name_code = $agent_browser_name_code;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_minor
     *
     * @param string $agent_simple_minor
     * @return $this
     */
    public function setAgentSimpleMinor($agent_simple_minor)
    {
        $this->agent_simple_minor = $agent_simple_minor;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_version
     *
     * @param string $agent_operating_system_version
     * @return $this
     */
    public function setAgentOperatingSystemVersion($agent_operating_system_version)
    {
        $this->agent_operating_system_version = $agent_operating_system_version;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_operating_platform_string
     *
     * @param string $agent_simple_operating_platform_string
     * @return $this
     */
    public function setAgentSimpleOperatingPlatformString($agent_simple_operating_platform_string)
    {
        $this->agent_simple_operating_platform_string = $agent_simple_operating_platform_string;

        return $this;
    }

    /**
     * Method to set the value of field agent_is_abusive
     *
     * @param string $agent_is_abusive
     * @return $this
     */
    public function setAgentIsAbusive($agent_is_abusive)
    {
        $this->agent_is_abusive = $agent_is_abusive;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_medium
     *
     * @param string $agent_simple_medium
     * @return $this
     */
    public function setAgentSimpleMedium($agent_simple_medium)
    {
        $this->agent_simple_medium = $agent_simple_medium;

        return $this;
    }

    /**
     * Method to set the value of field agent_layout_engine_version
     *
     * @param string $agent_layout_engine_version
     * @return $this
     */
    public function setAgentLayoutEngineVersion($agent_layout_engine_version)
    {
        $this->agent_layout_engine_version = $agent_layout_engine_version;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser_capabilities
     *
     * @param string $agent_browser_capabilities
     * @return $this
     */
    public function setAgentBrowserCapabilities($agent_browser_capabilities)
    {
        $this->agent_browser_capabilities = $agent_browser_capabilities;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_platform_vendor_name
     *
     * @param string $agent_operating_platform_vendor_name
     * @return $this
     */
    public function setAgentOperatingPlatformVendorName($agent_operating_platform_vendor_name)
    {
        $this->agent_operating_platform_vendor_name = $agent_operating_platform_vendor_name;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system
     *
     * @param string $agent_operating_system
     * @return $this
     */
    public function setAgentOperatingSystem($agent_operating_system)
    {
        $this->agent_operating_system = $agent_operating_system;

        return $this;
    }

    /**
     * Method to set the value of field agent_hardware_architecture
     *
     * @param string $agent_hardware_architecture
     * @return $this
     */
    public function setAgentHardwareArchitecture($agent_hardware_architecture)
    {
        $this->agent_hardware_architecture = $agent_hardware_architecture;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_version_full
     *
     * @param string $agent_operating_system_version_full
     * @return $this
     */
    public function setAgentOperatingSystemVersionFull($agent_operating_system_version_full)
    {
        $this->agent_operating_system_version_full = $agent_operating_system_version_full;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_platform_code
     *
     * @param string $agent_operating_platform_code
     * @return $this
     */
    public function setAgentOperatingPlatformCode($agent_operating_platform_code)
    {
        $this->agent_operating_platform_code = $agent_operating_platform_code;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser_name
     *
     * @param string $agent_browser_name
     * @return $this
     */
    public function setAgentBrowserName($agent_browser_name)
    {
        $this->agent_browser_name = $agent_browser_name;

        return $this;
    }

    /**
     * Method to set the value of field agent_operating_system_name_code
     *
     * @param string $agent_operating_system_name_code
     * @return $this
     */
    public function setAgentOperatingSystemNameCode($agent_operating_system_name_code)
    {
        $this->agent_operating_system_name_code = $agent_operating_system_name_code;

        return $this;
    }

    /**
     * Method to set the value of field agent_simple_major
     *
     * @param string $agent_simple_major
     * @return $this
     */
    public function setAgentSimpleMajor($agent_simple_major)
    {
        $this->agent_simple_major = $agent_simple_major;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser_version_full
     *
     * @param string $agent_browser_version_full
     * @return $this
     */
    public function setAgentBrowserVersionFull($agent_browser_version_full)
    {
        $this->agent_browser_version_full = $agent_browser_version_full;

        return $this;
    }

    /**
     * Method to set the value of field agent_browser
     *
     * @param string $agent_browser
     * @return $this
     */
    public function setAgentBrowser($agent_browser)
    {
        $this->agent_browser = $agent_browser;

        return $this;
    }

    /**
     * Returns the value of field agent_id
     *
     * @return integer
     */
    public function getAgentId()
    {
        return $this->agent_id;
    }

    /**
     * Returns the value of field agent_user_agent
     *
     * @return string
     */
    public function getAgentUserAgent()
    {
        return $this->agent_user_agent;
    }

    /**
     * Returns the value of field agent_checked
     *
     * @return string
     */
    public function getAgentChecked()
    {
        return $this->agent_checked;
    }

    /**
     * Returns the value of field agent_hardware_type
     *
     * @return string
     */
    public function getAgentHardwareType()
    {
        return $this->agent_hardware_type;
    }

    /**
     * Returns the value of field agent_operating_system_name
     *
     * @return string
     */
    public function getAgentOperatingSystemName()
    {
        return $this->agent_operating_system_name;
    }

    /**
     * Returns the value of field agent_software_sub_type
     *
     * @return string
     */
    public function getAgentSoftwareSubType()
    {
        return $this->agent_software_sub_type;
    }

    /**
     * Returns the value of field agent_simple_sub_description_string
     *
     * @return string
     */
    public function getAgentSimpleSubDescriptionString()
    {
        return $this->agent_simple_sub_description_string;
    }

    /**
     * Returns the value of field agent_simple_browser_string
     *
     * @return string
     */
    public function getAgentSimpleBrowserString()
    {
        return $this->agent_simple_browser_string;
    }

    /**
     * Returns the value of field agent_browser_version
     *
     * @return string
     */
    public function getAgentBrowserVersion()
    {
        return $this->agent_browser_version;
    }

    /**
     * Returns the value of field agent_software_type
     *
     * @return string
     */
    public function getAgentSoftwareType()
    {
        return $this->agent_software_type;
    }

    /**
     * Returns the value of field agent_extra_info
     *
     * @return string
     */
    public function getAgentExtraInfo()
    {
        return $this->agent_extra_info;
    }

    /**
     * Returns the value of field agent_operating_platform
     *
     * @return string
     */
    public function getAgentOperatingPlatform()
    {
        return $this->agent_operating_platform;
    }

    /**
     * Returns the value of field agent_extra_info_table
     *
     * @return string
     */
    public function getAgentExtraInfoTable()
    {
        return $this->agent_extra_info_table;
    }

    /**
     * Returns the value of field agent_layout_engine_name
     *
     * @return string
     */
    public function getAgentLayoutEngineName()
    {
        return $this->agent_layout_engine_name;
    }

    /**
     * Returns the value of field agent_operating_system_flavour_code
     *
     * @return string
     */
    public function getAgentOperatingSystemFlavourCode()
    {
        return $this->agent_operating_system_flavour_code;
    }

    /**
     * Returns the value of field agent_detected_addons
     *
     * @return string
     */
    public function getAgentDetectedAddons()
    {
        return $this->agent_detected_addons;
    }

    /**
     * Returns the value of field agent_operating_system_flavour
     *
     * @return string
     */
    public function getAgentOperatingSystemFlavour()
    {
        return $this->agent_operating_system_flavour;
    }

    /**
     * Returns the value of field agent_operating_system_frameworks
     *
     * @return string
     */
    public function getAgentOperatingSystemFrameworks()
    {
        return $this->agent_operating_system_frameworks;
    }

    /**
     * Returns the value of field agent_browser_name_code
     *
     * @return string
     */
    public function getAgentBrowserNameCode()
    {
        return $this->agent_browser_name_code;
    }

    /**
     * Returns the value of field agent_simple_minor
     *
     * @return string
     */
    public function getAgentSimpleMinor()
    {
        return $this->agent_simple_minor;
    }

    /**
     * Returns the value of field agent_operating_system_version
     *
     * @return string
     */
    public function getAgentOperatingSystemVersion()
    {
        return $this->agent_operating_system_version;
    }

    /**
     * Returns the value of field agent_simple_operating_platform_string
     *
     * @return string
     */
    public function getAgentSimpleOperatingPlatformString()
    {
        return $this->agent_simple_operating_platform_string;
    }

    /**
     * Returns the value of field agent_is_abusive
     *
     * @return string
     */
    public function getAgentIsAbusive()
    {
        return $this->agent_is_abusive;
    }

    /**
     * Returns the value of field agent_simple_medium
     *
     * @return string
     */
    public function getAgentSimpleMedium()
    {
        return $this->agent_simple_medium;
    }

    /**
     * Returns the value of field agent_layout_engine_version
     *
     * @return string
     */
    public function getAgentLayoutEngineVersion()
    {
        return $this->agent_layout_engine_version;
    }

    /**
     * Returns the value of field agent_browser_capabilities
     *
     * @return string
     */
    public function getAgentBrowserCapabilities()
    {
        return $this->agent_browser_capabilities;
    }

    /**
     * Returns the value of field agent_operating_platform_vendor_name
     *
     * @return string
     */
    public function getAgentOperatingPlatformVendorName()
    {
        return $this->agent_operating_platform_vendor_name;
    }

    /**
     * Returns the value of field agent_operating_system
     *
     * @return string
     */
    public function getAgentOperatingSystem()
    {
        return $this->agent_operating_system;
    }

    /**
     * Returns the value of field agent_hardware_architecture
     *
     * @return string
     */
    public function getAgentHardwareArchitecture()
    {
        return $this->agent_hardware_architecture;
    }

    /**
     * Returns the value of field agent_operating_system_version_full
     *
     * @return string
     */
    public function getAgentOperatingSystemVersionFull()
    {
        return $this->agent_operating_system_version_full;
    }

    /**
     * Returns the value of field agent_operating_platform_code
     *
     * @return string
     */
    public function getAgentOperatingPlatformCode()
    {
        return $this->agent_operating_platform_code;
    }

    /**
     * Returns the value of field agent_browser_name
     *
     * @return string
     */
    public function getAgentBrowserName()
    {
        return $this->agent_browser_name;
    }

    /**
     * Returns the value of field agent_operating_system_name_code
     *
     * @return string
     */
    public function getAgentOperatingSystemNameCode()
    {
        return $this->agent_operating_system_name_code;
    }

    /**
     * Returns the value of field agent_simple_major
     *
     * @return string
     */
    public function getAgentSimpleMajor()
    {
        return $this->agent_simple_major;
    }

    /**
     * Returns the value of field agent_browser_version_full
     *
     * @return string
     */
    public function getAgentBrowserVersionFull()
    {
        return $this->agent_browser_version_full;
    }

    /**
     * Returns the value of field agent_browser
     *
     * @return string
     */
    public function getAgentBrowser()
    {
        return $this->agent_browser;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("bincgcom");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_user_agent';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinUserAgent[]|BinUserAgent
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinUserAgent
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * @param string $userAgentString
     * @return \Phalcon\Mvc\Model|BinUserAgent
     */
    public static function findFirstByUserAgent($userAgentString)
    {
        return parent::findFirst(array(
            'agent_user_agent=:user_agent:',
            'bind' => array('user_agent' => $userAgentString)
        ));
    }
}
