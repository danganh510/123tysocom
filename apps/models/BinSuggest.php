<?php

namespace Bincg\Models;

class BinSuggest extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $sug_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $sug_name;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=true)
     */
    protected $sug_lang_code;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $sug_count;

    /**
     * Method to set the value of field sug_id
     *
     * @param integer $sug_id
     * @return $this
     */
    public function setSugId($sug_id)
    {
        $this->sug_id = $sug_id;

        return $this;
    }

    /**
     * Method to set the value of field sug_name
     *
     * @param string $sug_name
     * @return $this
     */
    public function setSugName($sug_name)
    {
        $this->sug_name = $sug_name;

        return $this;
    }

    /**
     * Method to set the value of field sug_lang_code
     *
     * @param string $sug_lang_code
     * @return $this
     */
    public function setSugLangCode($sug_lang_code)
    {
        $this->sug_lang_code = $sug_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field sug_count
     *
     * @param integer $sug_count
     * @return $this
     */
    public function setSugCount($sug_count)
    {
        $this->sug_count = $sug_count;

        return $this;
    }

    /**
     * Returns the value of field sug_id
     *
     * @return integer
     */
    public function getSugId()
    {
        return $this->sug_id;
    }

    /**
     * Returns the value of field sug_name
     *
     * @return string
     */
    public function getSugName()
    {
        return $this->sug_name;
    }

    /**
     * Returns the value of field sug_lang_code
     *
     * @return string
     */
    public function getSugLangCode()
    {
        return $this->sug_lang_code;
    }

    /**
     * Returns the value of field sug_count
     *
     * @return integer
     */
    public function getSugCount()
    {
        return $this->sug_count;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_suggest';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinSuggest[]|\Phalcon\Mvc\Model\ResultsetInterface
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinSuggest|\Phalcon\Mvc\Model
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}

