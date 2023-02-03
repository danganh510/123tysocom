<?php

namespace Bincg\Models;

class BinLocation extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $location_id;

    /**
     *
     * @var string
     * @Column(type="string", length=2, nullable=false)
     */
    protected $location_country_code;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $location_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_schema_contactpoint;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_newspaperstalk;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $location_schema_social;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $location_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $location_active;

    /**
     * Method to set the value of field location_id
     *
     * @param integer $location_id
     * @return $this
     */
    public function setLocationId($location_id)
    {
        $this->location_id = $location_id;

        return $this;
    }

    /**
     * Method to set the value of field location_country_code
     *
     * @param string $location_country_code
     * @return $this
     */
    public function setLocationCountryCode($location_country_code)
    {
        $this->location_country_code = $location_country_code;

        return $this;
    }

    /**
     * Method to set the value of field location_lang_code
     *
     * @param string $location_lang_code
     * @return $this
     */
    public function setLocationLangCode($location_lang_code)
    {
        $this->location_lang_code = $location_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field location_schema_contactpoint
     *
     * @param string $location_schema_contactpoint
     * @return $this
     */
    public function setLocationSchemaContactpoint($location_schema_contactpoint)
    {
        $this->location_schema_contactpoint = $location_schema_contactpoint;

        return $this;
    }

    /**
     * Method to set the value of field location_newspaperstalk
     *
     * @param string $location_newspaperstalk
     * @return $this
     */
    public function setLocationNewspaperstalk($location_newspaperstalk)
    {
        $this->location_newspaperstalk = $location_newspaperstalk;

        return $this;
    }

    /**
     * Method to set the value of field location_schema_social
     *
     * @param string $location_schema_social
     * @return $this
     */
    public function setLocationSchemaSocial($location_schema_social)
    {
        $this->location_schema_social = $location_schema_social;

        return $this;
    }

    /**
     * Method to set the value of field location_order
     *
     * @param integer $location_order
     * @return $this
     */
    public function setLocationOrder($location_order)
    {
        $this->location_order = $location_order;

        return $this;
    }

    /**
     * Method to set the value of field location_active
     *
     * @param string $location_active
     * @return $this
     */
    public function setLocationActive($location_active)
    {
        $this->location_active = $location_active;

        return $this;
    }

    /**
     * Returns the value of field location_id
     *
     * @return integer
     */
    public function getLocationId()
    {
        return $this->location_id;
    }

    /**
     * Returns the value of field location_country_code
     *
     * @return string
     */
    public function getLocationCountryCode()
    {
        return $this->location_country_code;
    }

    /**
     * Returns the value of field location_lang_code
     *
     * @return string
     */
    public function getLocationLangCode()
    {
        return $this->location_lang_code;
    }

    /**
     * Returns the value of field location_schema_contactpoint
     *
     * @return string
     */
    public function getLocationSchemaContactpoint()
    {
        return $this->location_schema_contactpoint;
    }

    /**
     * Returns the value of field location_newspaperstalk
     *
     * @return string
     */
    public function getLocationNewspaperstalk()
    {
        return $this->location_newspaperstalk;
    }

    /**
     * Returns the value of field location_schema_social
     *
     * @return string
     */
    public function getLocationSchemaSocial()
    {
        return $this->location_schema_social;
    }

    /**
     * Returns the value of field location_order
     *
     * @return integer
     */
    public function getLocationOrder()
    {
        return $this->location_order;
    }

    /**
     * Returns the value of field location_active
     *
     * @return string
     */
    public function getLocationActive()
    {
        return $this->location_active;
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
        return 'bin_location';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinLocation|BinLocation[]|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinLocation|\Phalcon\Mvc\Model
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById($languageId)
    {
        return BinLocation::findFirst(array(
            "location_id =:ID:",
            'bind' => array('ID' => $languageId)
        ));
    }
    public static function findFirstByCountryCode($code)
    {
        return BinLocation::findFirst(array(
            'location_country_code=:country_code: AND location_active="Y"',
            'bind' => array('country_code' => $code),
            'order' => 'location_order ASC',
        ));
    }
    public static function findFirstByCountryCodeAndLang($code,$lang)
    {
        return BinLocation::findFirst(array(
            'location_country_code=:country_code: AND location_lang_code=:language_code: AND location_active="Y"',
            'bind' => array('country_code' => $code, 'language_code' => $lang),
            'order' => 'location_order ASC',
        ));
    }

}
