<?php

namespace Bincg\Models;

class BinContactus extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $contact_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_number;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $contact_subject;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $contact_content;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $contact_topics;

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=true)
     */
    protected $contact_communication_channel_id;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $contact_communication_channel_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $contact_communication_channel_number;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $contact_file;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $contact_insert_time;

    /**
     * Method to set the value of field contact_id
     *
     * @param integer $contact_id
     * @return $this
     */
    public function setContactId($contact_id)
    {
        $this->contact_id = $contact_id;

        return $this;
    }

    /**
     * Method to set the value of field contact_name
     *
     * @param string $contact_name
     * @return $this
     */
    public function setContactName($contact_name)
    {
        $this->contact_name = $contact_name;

        return $this;
    }

    /**
     * Method to set the value of field contact_number
     *
     * @param string $contact_number
     * @return $this
     */
    public function setContactNumber($contact_number)
    {
        $this->contact_number = $contact_number;

        return $this;
    }

    /**
     * Method to set the value of field contact_email
     *
     * @param string $contact_email
     * @return $this
     */
    public function setContactEmail($contact_email)
    {
        $this->contact_email = $contact_email;

        return $this;
    }

    /**
     * Method to set the value of field contact_subject
     *
     * @param string $contact_subject
     * @return $this
     */
    public function setContactSubject($contact_subject)
    {
        $this->contact_subject = $contact_subject;

        return $this;
    }

    /**
     * Method to set the value of field contact_content
     *
     * @param string $contact_content
     * @return $this
     */
    public function setContactContent($contact_content)
    {
        $this->contact_content = $contact_content;

        return $this;
    }

    /**
     * Method to set the value of field contact_topics
     *
     * @param string $contact_topics
     * @return $this
     */
    public function setContactTopics($contact_topics)
    {
        $this->contact_topics = $contact_topics;

        return $this;
    }

    /**
     * Returns the value of field contact_communication_channel_id
     *
     * @param integer $contact_communication_channel_id
     * @return $this
     */
    public function setContactCommunicationChannelId($contact_communication_channel_id)
    {
        $this->contact_communication_channel_id = $contact_communication_channel_id;

        return $this;
    }

    /**
     * Returns the value of field contact_communication_channel_name
     *
     * @param string $contact_communication_channel_name
     * @return $this
     */
    public function setContactCommunicationChannelName($contact_communication_channel_name)
    {
        $this->contact_communication_channel_name = $contact_communication_channel_name;

        return $this;
    }

    /**
     * Returns the value of field contact_communication_channel_number
     *
     * @param string $contact_communication_channel_number
     * @return $this
     */
    public function setContactCommunicationChannelNumber($contact_communication_channel_number)
    {
        $this->contact_communication_channel_number = $contact_communication_channel_number;

        return $this;
    }

    /**
     * Method to set the value of field contact_file
     *
     * @param string $contact_file
     * @return $this
     */
    public function setContactFile($contact_file)
    {
        $this->contact_file = $contact_file;

        return $this;
    }

    /**
     * Method to set the value of field contact_insert_time
     *
     * @param integer $contact_insert_time
     * @return $this
     */
    public function setContactInsertTime($contact_insert_time)
    {
        $this->contact_insert_time = $contact_insert_time;

        return $this;
    }

    /**
     * Returns the value of field contact_id
     *
     * @return integer
     */
    public function getContactId()
    {
        return $this->contact_id;
    }

    /**
     * Returns the value of field contact_name
     *
     * @return string
     */
    public function getContactName()
    {
        return $this->contact_name;
    }

    /**
     * Returns the value of field contact_number
     *
     * @return string
     */
    public function getContactNumber()
    {
        return $this->contact_number;
    }

    /**
     * Returns the value of field contact_email
     *
     * @return string
     */
    public function getContactEmail()
    {
        return $this->contact_email;
    }

    /**
     * Returns the value of field contact_subject
     *
     * @return string
     */
    public function getContactSubject()
    {
        return $this->contact_subject;
    }

    /**
     * Returns the value of field contact_content
     *
     * @return string
     */
    public function getContactContent()
    {
        return $this->contact_content;
    }

    /**
     *  Returns the value of field contact_topics
     *
     * @return string
     */
    public function getContactTopics()
    {
        return $this->contact_topics;
    }

    /**
     * Returns the value of field contact_communication_channel_id
     *
     * @return integer
     */
    public function getContactCommunicationChannelId()
    {
        return $this->contact_communication_channel_id;
    }

    /**
     * Returns the value of field contact_communication_channel_name
     *
     * @return string
     */
    public function getContactCommunicationChannelName()
    {
        return $this->contact_communication_channel_name;
    }

    /**
     * Returns the value of field contact_communication_channel_number
     *
     * @return string
     */
    public function getContactCommunicationChannelNumber()
    {
        return $this->contact_communication_channel_number;
    }

    /**
     * Returns the value of field contact_file
     *
     * @return string
     */
    public function getContactFile()
    {
        return $this->contact_file;
    }

    /**
     * Returns the value of field contact_insert_time
     *
     * @return integer
     */
    public function getContactInsertTime()
    {
        return $this->contact_insert_time;
    }

//    /**
//     * Initialize method for model.
//     */
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
        return 'bin_contactus';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinContactus[]|BinContactus
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinContactus
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById ($id)
    {
        return BinContactus::findFirst(array(
            'contact_id = :id:',
            'bind' => array('id' => $id)
        ));
    }

}
