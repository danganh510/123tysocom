<?php

namespace Bincg\Models;

class BinTemplateEmail extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $email_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $email_type;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $email_subject;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $email_content;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $email_status;

    /**
     * Method to set the value of field email_id
     *
     * @param integer $email_id
     * @return $this
     */
    public function setEmailId($email_id)
    {
        $this->email_id = $email_id;

        return $this;
    }

    /**
     * Method to set the value of field email_type
     *
     * @param string $email_type
     * @return $this
     */
    public function setEmailType($email_type)
    {
        $this->email_type = $email_type;

        return $this;
    }

    /**
     * Method to set the value of field email_subject
     *
     * @param string $email_subject
     * @return $this
     */
    public function setEmailSubject($email_subject)
    {
        $this->email_subject = $email_subject;

        return $this;
    }

    /**
     * Method to set the value of field email_content
     *
     * @param string $email_content
     * @return $this
     */
    public function setEmailContent($email_content)
    {
        $this->email_content = $email_content;

        return $this;
    }

    /**
     * Method to set the value of field email_status
     *
     * @param string $email_status
     * @return $this
     */
    public function setEmailStatus($email_status)
    {
        $this->email_status = $email_status;

        return $this;
    }

    /**
     * Returns the value of field email_id
     *
     * @return integer
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * Returns the value of field email_type
     *
     * @return string
     */
    public function getEmailType()
    {
        return $this->email_type;
    }

    /**
     * Returns the value of field email_subject
     *
     * @return string
     */
    public function getEmailSubject()
    {
        return $this->email_subject;
    }

    /**
     * Returns the value of field email_content
     *
     * @return string
     */
    public function getEmailContent()
    {
        return $this->email_content;
    }

    /**
     * Returns the value of field email_status
     *
     * @return string
     */
    public function getEmailStatus()
    {
        return $this->email_status;
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
        return 'bin_template_email';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinTemplateEmail[]|BinTemplateEmail
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinTemplateEmail
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById($id)
    {
        return BinTemplateEmail::findFirst(array(
            "email_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }
}
