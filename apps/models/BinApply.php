<?php

namespace Bincg\Models;

use Phalcon\Db\RawValue;

class BinApply extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $apply_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $apply_career_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $apply_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $apply_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $apply_tel;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $apply_notes;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $apply_cv;

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $apply_communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $apply_communication_channel_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $apply_communication_channel_number;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=true)
     */
    protected $apply_insert_time;

    /**
     * Method to set the value of field apply_id
     *
     * @param integer $apply_id
     * @return $this
     */
    public function setApplyId($apply_id)
    {
        $this->apply_id = $apply_id;

        return $this;
    }

    /**
     * Method to set the value of field apply_career_id
     *
     * @param integer $apply_career_id
     * @return $this
     */
    public function setApplyCareerId($apply_career_id)
    {
        $this->apply_career_id = $apply_career_id;

        return $this;
    }

    /**
     * Method to set the value of field apply_name
     *
     * @param string $apply_name
     * @return $this
     */
    public function setApplyName($apply_name)
    {
        $this->apply_name = $apply_name;

        return $this;
    }

    /**
     * Method to set the value of field apply_email
     *
     * @param string $apply_email
     * @return $this
     */
    public function setApplyEmail($apply_email)
    {
        $this->apply_email = $apply_email;

        return $this;
    }

    /**
     * Method to set the value of field apply_tel
     *
     * @param string $apply_tel
     * @return $this
     */
    public function setApplyTel($apply_tel)
    {
        $this->apply_tel = $apply_tel;

        return $this;
    }

    /**
     * Method to set the value of field apply_notes
     *
     * @param string $apply_notes
     * @return $this
     */
    public function setApplyNotes($apply_notes)
    {
        $this->apply_notes = $apply_notes;

        return $this;
    }

    /**
     * Method to set the value of field apply_cv
     *
     * @param string $apply_cv
     * @return $this
     */
    public function setApplyCv($apply_cv)
    {
        $this->apply_cv = $apply_cv;

        return $this;
    }

    /**
     * Returns the value of field apply_communication_channel_id
     *
     * @param integer $apply_communication_channel_id
     * @return $this
     */
    public function setApplyCommunicationChannelId($apply_communication_channel_id)
    {
        $this->apply_communication_channel_id = $apply_communication_channel_id;

        return $this;
    }

    /**
     * Returns the value of field apply_communication_channel_name
     *
     * @param string $apply_communication_channel_name
     * @return $this
     */
    public function setApplyCommunicationChannelName($apply_communication_channel_name)
    {
        $this->apply_communication_channel_name = $apply_communication_channel_name;

        return $this;
    }

    /**
     * Returns the value of field apply_communication_channel_number
     *
     * @param string $apply_communication_channel_number
     * @return $this
     */
    public function setApplyCommunicationChannelNumber($apply_communication_channel_number)
    {
        $this->apply_communication_channel_number = $apply_communication_channel_number;

        return $this;
    }

    /**
     * Method to set the value of field apply_insert_time
     *
     * @param integer $apply_insert_time
     * @return $this
     */
    public function setApplyInsertTime($apply_insert_time)
    {
        $this->apply_insert_time = $apply_insert_time;

        return $this;
    }

    /**
     * Returns the value of field apply_id
     *
     * @return integer
     */
    public function getApplyId()
    {
        return $this->apply_id;
    }

    /**
     * Returns the value of field apply_career_id
     *
     * @return integer
     */
    public function getApplyCareerId()
    {
        return $this->apply_career_id;
    }

    /**
     * Returns the value of field apply_name
     *
     * @return string
     */
    public function getApplyName()
    {
        return $this->apply_name;
    }

    /**
     * Returns the value of field apply_email
     *
     * @return string
     */
    public function getApplyEmail()
    {
        return $this->apply_email;
    }

    /**
     * Returns the value of field apply_tel
     *
     * @return string
     */
    public function getApplyTel()
    {
        return $this->apply_tel;
    }

    /**
     * Returns the value of field apply_notes
     *
     * @return string
     */
    public function getApplyNotes()
    {
        return $this->apply_notes;
    }

    /**
     * Returns the value of field apply_cv
     *
     * @return string
     */
    public function getApplyCv()
    {
        return $this->apply_cv;
    }

    /**
     * Returns the value of field apply_communication_channel_id
     *
     * @return integer
     */
    public function getApplyCommunicationChannelId()
    {
        return $this->apply_communication_channel_id;
    }

    /**
     * Returns the value of field apply_communication_channel_name
     *
     * @return string
     */
    public function getApplyCommunicationChannelName()
    {
        return $this->apply_communication_channel_name;
    }

    /**
     * Returns the value of field apply_communication_channel_number
     *
     * @return string
     */
    public function getApplyCommunicationChannelNumber()
    {
        return $this->apply_communication_channel_number;
    }
    
    /**
     * Returns the value of field apply_insert_time
     *
     * @return integer
     */
    public function getApplyInsertTime()
    {
        return $this->apply_insert_time;
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
        return 'bin_apply';
    }
    public function beforeValidation()
    {
        if (empty($this->apply_notes)) {
            $this->apply_notes = new RawValue('\'\'');
        }
    }
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinApply[]|BinApply
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinApply
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById ($id) {
        return BinApply::findFirst(array(
            'apply_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
