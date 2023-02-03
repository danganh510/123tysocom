<?php

namespace Score\Models;

use Score\Utils\IpApi;

class ScIp extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $ip_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $ip_date_modified;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_status;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_country;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_countrycode;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_region;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_regionname;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_city;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_zip;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_lat;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_lon;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_timezone;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_isp;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_org;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_as;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_reverse;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_query;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $ip_message;

    /**
     * Method to set the value of field ip_id
     *
     * @param integer $ip_id
     * @return $this
     */
    public function setIpId($ip_id)
    {
        $this->ip_id = $ip_id;

        return $this;
    }

    /**
     * Method to set the value of field ip_date_modified
     *
     * @param integer $ip_date_modified
     * @return $this
     */
    public function setIpDateModified($ip_date_modified)
    {
        $this->ip_date_modified = $ip_date_modified;

        return $this;
    }

    /**
     * Method to set the value of field ip_status
     *
     * @param string $ip_status
     * @return $this
     */
    public function setIpStatus($ip_status)
    {
        $this->ip_status = $ip_status;

        return $this;
    }

    /**
     * Method to set the value of field ip_country
     *
     * @param string $ip_country
     * @return $this
     */
    public function setIpCountry($ip_country)
    {
        $this->ip_country = $ip_country;

        return $this;
    }

    /**
     * Method to set the value of field ip_countrycode
     *
     * @param string $ip_countrycode
     * @return $this
     */
    public function setIpCountrycode($ip_countrycode)
    {
        $this->ip_countrycode = $ip_countrycode;

        return $this;
    }

    /**
     * Method to set the value of field ip_region
     *
     * @param string $ip_region
     * @return $this
     */
    public function setIpRegion($ip_region)
    {
        $this->ip_region = $ip_region;

        return $this;
    }

    /**
     * Method to set the value of field ip_regionname
     *
     * @param string $ip_regionname
     * @return $this
     */
    public function setIpRegionname($ip_regionname)
    {
        $this->ip_regionname = $ip_regionname;

        return $this;
    }

    /**
     * Method to set the value of field ip_city
     *
     * @param string $ip_city
     * @return $this
     */
    public function setIpCity($ip_city)
    {
        $this->ip_city = $ip_city;

        return $this;
    }

    /**
     * Method to set the value of field ip_zip
     *
     * @param string $ip_zip
     * @return $this
     */
    public function setIpZip($ip_zip)
    {
        $this->ip_zip = $ip_zip;

        return $this;
    }

    /**
     * Method to set the value of field ip_lat
     *
     * @param string $ip_lat
     * @return $this
     */
    public function setIpLat($ip_lat)
    {
        $this->ip_lat = $ip_lat;

        return $this;
    }

    /**
     * Method to set the value of field ip_lon
     *
     * @param string $ip_lon
     * @return $this
     */
    public function setIpLon($ip_lon)
    {
        $this->ip_lon = $ip_lon;

        return $this;
    }

    /**
     * Method to set the value of field ip_timezone
     *
     * @param string $ip_timezone
     * @return $this
     */
    public function setIpTimezone($ip_timezone)
    {
        $this->ip_timezone = $ip_timezone;

        return $this;
    }

    /**
     * Method to set the value of field ip_isp
     *
     * @param string $ip_isp
     * @return $this
     */
    public function setIpIsp($ip_isp)
    {
        $this->ip_isp = $ip_isp;

        return $this;
    }

    /**
     * Method to set the value of field ip_org
     *
     * @param string $ip_org
     * @return $this
     */
    public function setIpOrg($ip_org)
    {
        $this->ip_org = $ip_org;

        return $this;
    }

    /**
     * Method to set the value of field ip_as
     *
     * @param string $ip_as
     * @return $this
     */
    public function setIpAs($ip_as)
    {
        $this->ip_as = $ip_as;

        return $this;
    }

    /**
     * Method to set the value of field ip_reverse
     *
     * @param string $ip_reverse
     * @return $this
     */
    public function setIpReverse($ip_reverse)
    {
        $this->ip_reverse = $ip_reverse;

        return $this;
    }

    /**
     * Method to set the value of field ip_query
     *
     * @param string $ip_query
     * @return $this
     */
    public function setIpQuery($ip_query)
    {
        $this->ip_query = $ip_query;

        return $this;
    }

    /**
     * Method to set the value of field ip_message
     *
     * @param string $ip_message
     * @return $this
     */
    public function setIpMessage($ip_message)
    {
        $this->ip_message = $ip_message;

        return $this;
    }

    /**
     * Returns the value of field ip_id
     *
     * @return integer
     */
    public function getIpId()
    {
        return $this->ip_id;
    }

    /**
     * Returns the value of field ip_date_modified
     *
     * @return integer
     */
    public function getIpDateModified()
    {
        return $this->ip_date_modified;
    }

    /**
     * Returns the value of field ip_status
     *
     * @return string
     */
    public function getIpStatus()
    {
        return $this->ip_status;
    }

    /**
     * Returns the value of field ip_country
     *
     * @return string
     */
    public function getIpCountry()
    {
        return $this->ip_country;
    }

    /**
     * Returns the value of field ip_countrycode
     *
     * @return string
     */
    public function getIpCountrycode()
    {
        return $this->ip_countrycode;
    }

    /**
     * Returns the value of field ip_region
     *
     * @return string
     */
    public function getIpRegion()
    {
        return $this->ip_region;
    }

    /**
     * Returns the value of field ip_regionname
     *
     * @return string
     */
    public function getIpRegionname()
    {
        return $this->ip_regionname;
    }

    /**
     * Returns the value of field ip_city
     *
     * @return string
     */
    public function getIpCity()
    {
        return $this->ip_city;
    }

    /**
     * Returns the value of field ip_zip
     *
     * @return string
     */
    public function getIpZip()
    {
        return $this->ip_zip;
    }

    /**
     * Returns the value of field ip_lat
     *
     * @return string
     */
    public function getIpLat()
    {
        return $this->ip_lat;
    }

    /**
     * Returns the value of field ip_lon
     *
     * @return string
     */
    public function getIpLon()
    {
        return $this->ip_lon;
    }

    /**
     * Returns the value of field ip_timezone
     *
     * @return string
     */
    public function getIpTimezone()
    {
        return $this->ip_timezone;
    }

    /**
     * Returns the value of field ip_isp
     *
     * @return string
     */
    public function getIpIsp()
    {
        return $this->ip_isp;
    }

    /**
     * Returns the value of field ip_org
     *
     * @return string
     */
    public function getIpOrg()
    {
        return $this->ip_org;
    }

    /**
     * Returns the value of field ip_as
     *
     * @return string
     */
    public function getIpAs()
    {
        return $this->ip_as;
    }

    /**
     * Returns the value of field ip_reverse
     *
     * @return string
     */
    public function getIpReverse()
    {
        return $this->ip_reverse;
    }

    /**
     * Returns the value of field ip_query
     *
     * @return string
     */
    public function getIpQuery()
    {
        return $this->ip_query;
    }

    /**
     * Returns the value of field ip_message
     *
     * @return string
     */
    public function getIpMessage()
    {
        return $this->ip_message;
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
        return 'sc_ip';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScIp[]|ScIp
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScIp
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param string $ipAddress
     * @return \Phalcon\Mvc\Model|ScIp
     */
    public static function findFirstByIpAddress($ipAddress)
    {
        return parent::findFirst(array(
            'ip_query = :ip_query:',
            'bind' => array('ip_query' => $ipAddress)
        ));
    }
    /**
     * @param ScIp $ipInfo
     */
    public function setWithIpInfo($ipInfo = null)
    {
        if (isset($ipInfo)) {
            $ipDbKeys = IpApi::getDbKeys();
            foreach ($ipDbKeys as $ipDbKey) {
                $fieldName = 'ip_'.strtolower($ipDbKey);
                $this->$fieldName = $ipInfo->$ipDbKey;
            }
        }
    }
}
