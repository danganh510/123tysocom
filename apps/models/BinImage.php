<?php

namespace Score\Models;

class ScImage extends BaseModel
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
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $image_album_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $image_icon;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $image_description;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $image_order;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $image_insert_time;

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
     * Method to set the value of field image_album_id
     *
     * @param integer $image_album_id
     * @return $this
     */
    public function setImageAlbumId($image_album_id)
    {
        $this->image_album_id = $image_album_id;

        return $this;
    }

    /**
     * Method to set the value of field image_icon
     *
     * @param string $image_icon
     * @return $this
     */
    public function setImageIcon($image_icon)
    {
        $this->image_icon = $image_icon;

        return $this;
    }

    /**
     * Method to set the value of field image_description
     *
     * @param string $image_description
     * @return $this
     */
    public function setImageDescription($image_description)
    {
        $this->image_description = $image_description;

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
     * Method to set the value of field image_insert_time
     *
     * @param integer $image_insert_time
     * @return $this
     */
    public function setImageInsertTime($image_insert_time)
    {
        $this->image_insert_time = $image_insert_time;

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
     * Returns the value of field image_album_id
     *
     * @return integer
     */
    public function getImageAlbumId()
    {
        return $this->image_album_id;
    }

    /**
     * Returns the value of field image_icon
     *
     * @return string
     */
    public function getImageIcon()
    {
        return $this->image_icon;
    }

    /**
     * Returns the value of field image_description
     *
     * @return string
     */
    public function getImageDescription()
    {
        return $this->image_description;
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
     * Returns the value of field image_insert_time
     *
     * @return integer
     */
    public function getImageInsertTime()
    {
        return $this->image_insert_time;
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
        return 'sc_image';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScImage[]|ScImage
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScImage
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
