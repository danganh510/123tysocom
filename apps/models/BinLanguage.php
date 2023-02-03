<?php

namespace Bincg\Models;

class BinLanguage extends BaseModel
{
    const GENERAL = 'general';
    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $language_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $language_name;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $language_code;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=true)
     */
    protected $language_country_code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $language_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $language_active;

    /**
     * Method to set the value of field language_id
     *
     * @param integer $language_id
     * @return $this
     */
    public function setLanguageId($language_id)
    {
        $this->language_id = $language_id;

        return $this;
    }

    /**
     * Method to set the value of field language_name
     *
     * @param string $language_name
     * @return $this
     */
    public function setLanguageName($language_name)
    {
        $this->language_name = $language_name;

        return $this;
    }

    /**
     * Method to set the value of field language_code
     *
     * @param string $language_code
     * @return $this
     */
    public function setLanguageCode($language_code)
    {
        $this->language_code = $language_code;

        return $this;
    }

    /**
     * Method to set the value of field language_country_code
     *
     * @param string $language_country_code
     * @return $this
     */
    public function setLanguageCountryCode($language_country_code)
    {
        $this->language_country_code = $language_country_code;

        return $this;
    }

    /**
     * Method to set the value of field language_order
     *
     * @param integer $language_order
     * @return $this
     */
    public function setLanguageOrder($language_order)
    {
        $this->language_order = $language_order;

        return $this;
    }

    /**
     * Method to set the value of field language_active
     *
     * @param string $language_active
     * @return $this
     */
    public function setLanguageActive($language_active)
    {
        $this->language_active = $language_active;

        return $this;
    }

    /**
     * Returns the value of field language_id
     *
     * @return integer
     */
    public function getLanguageId()
    {
        return $this->language_id;
    }

    /**
     * Returns the value of field language_name
     *
     * @return string
     */
    public function getLanguageName()
    {
        return $this->language_name;
    }

    /**
     * Returns the value of field language_code
     *
     * @return string
     */
    public function getLanguageCode()
    {
        return $this->language_code;
    }

    /**
     * Returns the value of field language_country_code
     *
     * @return string
     */
    public function getLanguageCountryCode()
    {
        return $this->language_country_code;
    }

    /**
     * Returns the value of field language_order
     *
     * @return integer
     */
    public function getLanguageOrder()
    {
        return $this->language_order;
    }

    /**
     * Returns the value of field language_active
     *
     * @return string
     */
    public function getLanguageActive()
    {
        return $this->language_active;
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
        return 'bin_language';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinLanguage[]|BinLanguage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinLanguage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function getLanguages(){
        return self::find(array("language_active = 'Y'",
            "order" => "language_order"));
    }
    public static function findFirstById($languageId)
    {
        return self::findFirst(array(
            "language_id =:ID:",
            'bind' => array('ID' => $languageId)
        ));
    }
    public static function getCountryByCode($languageCode)
    {
        $language = self::findFirst(array(
            "language_code =:CODE: AND language_active ='Y'",
            'bind' => array('CODE' => $languageCode)
        ));
        return ($language)?$language->getLanguageCountryCode():'';
    }
}
