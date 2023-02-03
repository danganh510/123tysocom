<?php

namespace General\Models;

class Country extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $country_id;

    /**
     *
     * @var string
     * @Column(type="string", length=200, nullable=true)
     */
    protected $country_name;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=true)
     */
    protected $country_iso_alpha2;

    /**
     *
     * @var string
     * @Column(type="string", length=3, nullable=true)
     */
    protected $country_iso_alpha3;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $country_iso_numeric;

    /**
     *
     * @var integer
     * @Column(type="integer", length=6, nullable=true)
     */
    protected $country_phone_code;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=false)
     */
    protected $country_continent_code;

    /**
     *
     * @var string
     * @Column(type="string", length=3, nullable=true)
     */
    protected $country_currency_code;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $country_active;

    /**
     * Method to set the value of field country_id
     *
     * @param integer $country_id
     * @return $this
     */
    public function setCountryId($country_id)
    {
        $this->country_id = $country_id;

        return $this;
    }

    /**
     * Method to set the value of field country_name
     *
     * @param string $country_name
     * @return $this
     */
    public function setCountryName($country_name)
    {
        $this->country_name = $country_name;

        return $this;
    }

    /**
     * Method to set the value of field country_iso_alpha2
     *
     * @param string $country_iso_alpha2
     * @return $this
     */
    public function setCountryIsoAlpha2($country_iso_alpha2)
    {
        $this->country_iso_alpha2 = $country_iso_alpha2;

        return $this;
    }

    /**
     * Method to set the value of field country_iso_alpha3
     *
     * @param string $country_iso_alpha3
     * @return $this
     */
    public function setCountryIsoAlpha3($country_iso_alpha3)
    {
        $this->country_iso_alpha3 = $country_iso_alpha3;

        return $this;
    }

    /**
     * Method to set the value of field country_iso_numeric
     *
     * @param integer $country_iso_numeric
     * @return $this
     */
    public function setCountryIsoNumeric($country_iso_numeric)
    {
        $this->country_iso_numeric = $country_iso_numeric;

        return $this;
    }

    /**
     * Method to set the value of field country_phone_code
     *
     * @param integer $country_phone_code
     * @return $this
     */
    public function setCountryPhoneCode($country_phone_code)
    {
        $this->country_phone_code = $country_phone_code;

        return $this;
    }

    /**
     * Method to set the value of field country_continent_code
     *
     * @param string $country_continent_code
     * @return $this
     */
    public function setCountryContinentCode($country_continent_code)
    {
        $this->country_continent_code = $country_continent_code;

        return $this;
    }

    /**
     * Method to set the value of field country_currency_code
     *
     * @param string $country_currency_code
     * @return $this
     */
    public function setCountryCurrencyCode($country_currency_code)
    {
        $this->country_currency_code = $country_currency_code;

        return $this;
    }

    /**
     * Method to set the value of field country_active
     *
     * @param string $country_active
     * @return $this
     */
    public function setCountryActive($country_active)
    {
        $this->country_active = $country_active;

        return $this;
    }

    /**
     * Returns the value of field country_id
     *
     * @return integer
     */
    public function getCountryId()
    {
        return $this->country_id;
    }

    /**
     * Returns the value of field country_name
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->country_name;
    }

    /**
     * Returns the value of field country_iso_alpha2
     *
     * @return string
     */
    public function getCountryIsoAlpha2()
    {
        return $this->country_iso_alpha2;
    }

    /**
     * Returns the value of field country_iso_alpha3
     *
     * @return string
     */
    public function getCountryIsoAlpha3()
    {
        return $this->country_iso_alpha3;
    }

    /**
     * Returns the value of field country_iso_numeric
     *
     * @return integer
     */
    public function getCountryIsoNumeric()
    {
        return $this->country_iso_numeric;
    }

    /**
     * Returns the value of field country_phone_code
     *
     * @return integer
     */
    public function getCountryPhoneCode()
    {
        return $this->country_phone_code;
    }

    /**
     * Returns the value of field country_continent_code
     *
     * @return string
     */
    public function getCountryContinentCode()
    {
        return $this->country_continent_code;
    }

    /**
     * Returns the value of field country_currency_code
     *
     * @return string
     */
    public function getCountryCurrencyCode()
    {
        return $this->country_currency_code;
    }

    /**
     * Returns the value of field country_active
     *
     * @return string
     */
    public function getCountryActive()
    {
        return $this->country_active;
    }

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setConnectionService('db_general');
        $config = $this->getDI()->get('db_general')->getDescriptor();
        $this->setSchema($config['schema']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'country';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Country[]|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Country|\Phalcon\Mvc\Model
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
