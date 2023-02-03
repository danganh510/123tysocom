<?php

namespace Bincg\Models;

use Phalcon\Db\RawValue;

class BinOffice extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $office_image;

    /**
     *
     * @var string
     * @Column(type="string", length=3, nullable=false)
     */
    protected $office_country_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_country_name;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_position_x;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_position_y;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $office_address;

    /**
     *
     * @var string
     * @Column(type="string", length=20, nullable=true)
     */
    protected $office_postal_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $office_phone;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $office_fax;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $office_working_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $office_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $office_active;

    /**
     * Method to set the value of field office_id
     *
     * @param integer $office_id
     * @return $this
     */
    public function setOfficeId($office_id)
    {
        $this->office_id = $office_id;

        return $this;
    }

    /**
     * Method to set the value of field office_name
     *
     * @param string $office_name
     * @return $this
     */
    public function setOfficeName($office_name)
    {
        $this->office_name = $office_name;

        return $this;
    }

    /**
     * Method to set the value of field office_image
     *
     * @param string $office_image
     * @return $this
     */
    public function setOfficeImage($office_image)
    {
        $this->office_image = $office_image;

        return $this;
    }

    /**
     * Method to set the value of field office_country_code
     *
     * @param string $office_country_code
     * @return $this
     */
    public function setOfficeCountryCode($office_country_code)
    {
        $this->office_country_code = $office_country_code;

        return $this;
    }

    /**
     * Method to set the value of field office_country_name
     *
     * @param string $office_country_name
     * @return $this
     */
    public function setOfficeCountryName($office_country_name)
    {
        $this->office_country_name = $office_country_name;

        return $this;
    }

    /**
     * Method to set the value of field office_position_x
     *
     * @param integer $office_position_x
     * @return $this
     */
    public function setOfficePositionX($office_position_x)
    {
        $this->office_position_x = $office_position_x;

        return $this;
    }

    /**
     * Method to set the value of field office_position_y
     *
     * @param integer $office_position_y
     * @return $this
     */
    public function setOfficePositionY($office_position_y)
    {
        $this->office_position_y = $office_position_y;

        return $this;
    }

    /**
     * Method to set the value of field office_address
     *
     * @param string $office_address
     * @return $this
     */
    public function setOfficeAddress($office_address)
    {
        $this->office_address = $office_address;

        return $this;
    }

    /**
     * Method to set the value of field office_postal_cod
     *
     * @param string $office_postal_code
     * @return $this
     */
    public function setOfficePostalCode($office_postal_code)
    {
        $this->office_postal_code = $office_postal_code;

        return $this;
    }

    /**
     * Method to set the value of field office_email
     *
     * @param string $office_email
     * @return $this
     */
    public function setOfficeEmail($office_email)
    {
        $this->office_email = $office_email;

        return $this;
    }

    /**
     * Method to set the value of field office_phone
     *
     * @param string $office_phone
     * @return $this
     */
    public function setOfficePhone($office_phone)
    {
        $this->office_phone = $office_phone;

        return $this;
    }

    /**
     * Method to set the value of field office_fax
     *
     * @param string $office_fax
     * @return $this
     */
    public function setOfficeFax($office_fax)
    {
        $this->office_fax = $office_fax;

        return $this;
    }

    /**
     * Method to set the value of field office_working_time
     *
     * @param string $office_working_time
     * @return $this
     */
    public function setOfficeWorkingTime($office_working_time)
    {
        $this->office_working_time = $office_working_time;

        return $this;
    }

    /**
     * Method to set the value of field office_order
     *
     * @param integer $office_order
     * @return $this
     */
    public function setOfficeOrder($office_order)
    {
        $this->office_order = $office_order;

        return $this;
    }

    /**
     * Method to set the value of field office_active
     *
     * @param string $office_active
     * @return $this
     */
    public function setOfficeActive($office_active)
    {
        $this->office_active = $office_active;

        return $this;
    }

    /**
     * Returns the value of field office_id
     *
     * @return integer
     */
    public function getOfficeId()
    {
        return $this->office_id;
    }

    /**
     * Returns the value of field office_name
     *
     * @return string
     */
    public function getOfficeName()
    {
        return $this->office_name;
    }

    /**
     * Returns the value of field office_image
     *
     * @return string
     */
    public function getOfficeImage()
    {
        return $this->office_image;
    }

    /**
     * Returns the value of field office_country_code
     *
     * @return string
     */
    public function getOfficeCountryCode()
    {
        return $this->office_country_code;
    }

    /**
     * Returns the value of field office_country_name
     *
     * @return string
     */
    public function getOfficeCountryName()
    {
        return $this->office_country_name;
    }

    /**
     * Returns the value of field office_position_x
     *
     * @return integer
     */
    public function getOfficePositionX()
    {
        return $this->office_position_x;
    }

    /**
     * Returns the value of field office_position_y
     *
     * @return integer
     */
    public function getOfficePositionY()
    {
        return $this->office_position_y;
    }

    /**
     * Returns the value of field office_address
     *
     * @return string
     */
    public function getOfficeAddress()
    {
        return $this->office_address;
    }

    /**
     * Returns the value of field office_postal_code
     *
     * @return string
     */
    public function getOfficePostalCode()
    {
        return $this->office_postal_code;
    }

    /**
     * Returns the value of field office_email
     *
     * @return string
     */
    public function getOfficeEmail()
    {
        return $this->office_email;
    }

    /**
     * Returns the value of field office_phone
     *
     * @return string
     */
    public function getOfficePhone()
    {
        return $this->office_phone;
    }

    /**
     * Returns the value of field office_fax
     *
     * @return string
     */
    public function getOfficeFax()
    {
        return $this->office_fax;
    }

    /**
     * Returns the value of field office_working_time
     *
     * @return string
     */
    public function getOfficeWorkingTime()
    {
        return $this->office_working_time;
    }

    /**
     * Returns the value of field office_order
     *
     * @return integer
     */
    public function getOfficeOrder()
    {
        return $this->office_order;
    }

    /**
     * Returns the value of field office_active
     *
     * @return string
     */
    public function getOfficeActive()
    {
        return $this->office_active;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("dsbcf_com_vn");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_office';
    }

    public function beforeValidation()
    {
        if (empty($this->office_email)) {
            $this->office_email = new RawValue('\'\'');
        }
        if (empty($this->office_address)) {
            $this->office_address = new RawValue('\'\'');
        }
        if (empty($this->office_fax)) {
            $this->office_fax = new RawValue('\'\'');
        }
        if (empty($this->office_phone)) {
            $this->office_phone = new RawValue('\'\'');
        }
        if (empty($this->office_working_time)) {
            $this->office_working_time = new RawValue('\'\'');
        }
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinOffice[]|BinOffice
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinOffice
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById($id){
        return BinOffice::findFirst(array(
            'office_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
}
