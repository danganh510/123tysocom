<?php

namespace Bincg\Models;

class BinCommunicationChannelLang extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $communication_channel_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $communication_channel_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $communication_channel_name;

    /**
     * Method to set the value of field communication_channel_id
     *
     * @param integer $communication_channel_id
     * @return $this
     */
    public function setCommunicationChannelId($communication_channel_id)
    {
        $this->communication_channel_id = $communication_channel_id;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_lang_code
     *
     * @param string $communication_channel_lang_code
     * @return $this
     */
    public function setCommunicationChannelLangCode($communication_channel_lang_code)
    {
        $this->communication_channel_lang_code = $communication_channel_lang_code;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_name
     *
     * @param string $communication_channel_name
     * @return $this
     */
    public function setCommunicationChannelName($communication_channel_name)
    {
        $this->communication_channel_name = $communication_channel_name;

        return $this;
    }

    /**
     * Returns the value of field communication_channel_id
     *
     * @return integer
     */
    public function getCommunicationChannelId()
    {
        return $this->communication_channel_id;
    }

    /**
     * Returns the value of field communication_channel_lang_code
     *
     * @return string
     */
    public function getCommunicationChannelLangCode()
    {
        return $this->communication_channel_lang_code;
    }

    /**
     * Returns the value of field communication_channel_name
     *
     * @return string
     */
    public function getCommunicationChannelName()
    {
        return $this->communication_channel_name;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_communication_channel_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCommunicationChannelLang[]|BinCommunicationChannelLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCommunicationChannelLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findById($id)
    {
        return BinCommunicationChannelLang::find(array(
            "communication_channel_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }
}
