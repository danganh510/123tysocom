<?php

namespace Bincg\Models;

class BinTemplateEmailLang extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $email_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $email_lang_code;

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
     * Method to set the value of field email_lang_code
     *
     * @param string $email_lang_code
     * @return $this
     */
    public function setEmailLangCode($email_lang_code)
    {
        $this->email_lang_code = $email_lang_code;

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
     * Returns the value of field email_id
     *
     * @return integer
     */
    public function getEmailId()
    {
        return $this->email_id;
    }

    /**
     * Returns the value of field email_lang_code
     *
     * @return string
     */
    public function getEmailLangCode()
    {
        return $this->email_lang_code;
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
        return 'bin_template_email_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinTemplateEmailLang[]|BinTemplateEmailLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinTemplateEmailLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findById($id)
    {
        return BinTemplateEmailLang::find(array(
            "email_id =:ID:",
            'bind' => array('ID' => $id)
        ));
    }
}
