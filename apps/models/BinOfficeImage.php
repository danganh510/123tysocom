<?php

namespace Bincg\Models;

class BinOfficeImage extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $image_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $image_url;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $image_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $image_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $image_office_id;

    /**
     * Method to set the value of field image_id
     *
     * @param integer $image_id
     * @return $this
     */
    public function setImageId($image_id)
    {
        $this->image_id = $image_id;

        return $this;
    }

    /**
     * Method to set the value of field image_url
     *
     * @param string $image_url
     * @return $this
     */
    public function setImageUrl($image_url)
    {
        $this->image_url = $image_url;

        return $this;
    }

    /**
     * Method to set the value of field image_order
     *
     * @param integer $image_order
     * @return $this
     */
    public function setImageOrder($image_order)
    {
        $this->image_order = $image_order;

        return $this;
    }

    /**
     * Method to set the value of field image_active
     *
     * @param string $image_active
     * @return $this
     */
    public function setImageActive($image_active)
    {
        $this->image_active = $image_active;

        return $this;
    }

    /**
     * Method to set the value of field image_office_id
     *
     * @param integer $image_office_id
     * @return $this
     */
    public function setImageOfficeId($image_office_id)
    {
        $this->image_office_id = $image_office_id;

        return $this;
    }

    /**
     * Returns the value of field image_id
     *
     * @return integer
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Returns the value of field image_url
     *
     * @return string
     */
    public function getImageUrl()
    {
        return $this->image_url;
    }

    /**
     * Returns the value of field image_order
     *
     * @return integer
     */
    public function getImageOrder()
    {
        return $this->image_order;
    }

    /**
     * Returns the value of field image_active
     *
     * @return string
     */
    public function getImageActive()
    {
        return $this->image_active;
    }

    /**
     * Returns the value of field image_office_id
     *
     * @return integer
     */
    public function getImageOfficeId()
    {
        return $this->image_office_id;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("oneibc_com");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_office_image';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinOfficeImage[]|BinOfficeImage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinOfficeImage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstById($id)
    {
        return BinOfficeImage::findFirst(array(
            'image_id = :ID:',
            'bind' => array('ID' => $id)
        ));
    }
    public static function findFirstByOfficeId($office_id){
        return BinOfficeImage::findFirst(array(
            'image_office_id = :office_id:',
            'bind' => array('office_id' => $office_id)
        ));
    }

}
