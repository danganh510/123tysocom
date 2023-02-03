<?php

namespace Bincg\Models;

class BinCommunicationChannel extends BaseModel
{
    const TYPE_TEXT = 'Text';
    const TYPE_OTHER = 'Other';
    const TYPE_PHONE = 'Phone';

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $communication_channel_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $communication_channel_icon;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $communication_channel_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $communication_channel_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $communication_channel_active;

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
     * Method to set the value of field communication_channel_icon
     *
     * @param string $communication_channel_icon
     * @return $this
     */
    public function setCommunicationChannelIcon($communication_channel_icon)
    {
        $this->communication_channel_icon = $communication_channel_icon;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_type
     *
     * @param string $communication_channel_type
     * @return $this
     */
    public function setCommunicationChannelType($communication_channel_type)
    {
        $this->communication_channel_type = $communication_channel_type;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_order
     *
     * @param integer $communication_channel_order
     * @return $this
     */
    public function setCommunicationChannelOrder($communication_channel_order)
    {
        $this->communication_channel_order = $communication_channel_order;

        return $this;
    }

    /**
     * Method to set the value of field communication_channel_active
     *
     * @param string $communication_channel_active
     * @return $this
     */
    public function setCommunicationChannelActive($communication_channel_active)
    {
        $this->communication_channel_active = $communication_channel_active;

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
     * Returns the value of field communication_channel_name
     *
     * @return string
     */
    public function getCommunicationChannelName()
    {
        return $this->communication_channel_name;
    }

    /**
     * Returns the value of field communication_channel_icon
     *
     * @return string
     */
    public function getCommunicationChannelIcon()
    {
        return $this->communication_channel_icon;
    }

    /**
     * Returns the value of field communication_channel_type
     *
     * @return string
     */
    public function getCommunicationChannelType()
    {
        return $this->communication_channel_type;
    }

    /**
     * Returns the value of field communication_channel_order
     *
     * @return integer
     */
    public function getCommunicationChannelOrder()
    {
        return $this->communication_channel_order;
    }

    /**
     * Returns the value of field communication_channel_active
     *
     * @return string
     */
    public function getCommunicationChannelActive()
    {
        return $this->communication_channel_active;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_communication_channel';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCommunicationChannel[]|BinCommunicationChannel
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCommunicationChannel
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function allTypes()
    {
        return array(
            self::TYPE_TEXT => self::TYPE_TEXT,
            self::TYPE_OTHER => self::TYPE_OTHER,
            self::TYPE_PHONE => self::TYPE_PHONE,
        );
    }

    public static function findFirstById($id)
    {
        return BinCommunicationChannel::findFirst(array(
            "communication_channel_id=:ID:",
            'bind' => array('ID' => $id)
        ));
    }
}
