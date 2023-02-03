<?php

namespace Score\Models;

use Phalcon\Db\RawValue;

class ScConfig extends BaseModel
{

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=255, nullable=false)
     */
    protected $config_key;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $config_language;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $config_content;

    /**
     * Method to set the value of field config_key
     *
     * @param string $config_key
     * @return $this
     */
    public function setConfigKey($config_key)
    {
        $this->config_key = $config_key;

        return $this;
    }

    /**
     * Method to set the value of field config_language
     *
     * @param string $config_language
     * @return $this
     */
    public function setConfigLanguage($config_language)
    {
        $this->config_language = $config_language;

        return $this;
    }

    /**
     * Method to set the value of field config_content
     *
     * @param string $config_content
     * @return $this
     */
    public function setConfigContent($config_content)
    {
        $this->config_content = $config_content;

        return $this;
    }

    /**
     * Returns the value of field config_key
     *
     * @return string
     */
    public function getConfigKey()
    {
        return $this->config_key;
    }

    /**
     * Returns the value of field config_language
     *
     * @return string
     */
    public function getConfigLanguage()
    {
        return $this->config_language;
    }

    /**
     * Returns the value of field config_content
     *
     * @return string
     */
    public function getConfigContent()
    {
        return $this->config_content;
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
        return 'sc_config';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScConfig[]|ScConfig
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScConfig
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public function beforeValidation()
    {
        if (empty($this->config_key)) {
            $this->config_key = new RawValue('\'\'');
        }
        if (empty($this->config_content)) {
            $this->config_content = new RawValue('\'\'');
        }
    }

}
