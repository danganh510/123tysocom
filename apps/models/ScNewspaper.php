<?php

namespace Score\Models;

class ScNewspaper extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $newspaper_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $newspaper_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $newspaper_logo;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $newspaper_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $newspaper_title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $newspaper_meta_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $newspaper_meta_description;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $newspaper_meta_image;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $newspaper_link;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $newspaper_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $newspaper_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $newspaper_insert_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $newspaper_update_time;

    /**
     * Method to set the value of field newspaper_id
     *
     * @param integer $newspaper_id
     * @return $this
     */
    public function setNewspaperId($newspaper_id)
    {
        $this->newspaper_id = $newspaper_id;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_name
     *
     * @param string $newspaper_name
     * @return $this
     */
    public function setNewspaperName($newspaper_name)
    {
        $this->newspaper_name = $newspaper_name;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_logo
     *
     * @param string $newspaper_logo
     * @return $this
     */
    public function setNewspaperLogo($newspaper_logo)
    {
        $this->newspaper_logo = $newspaper_logo;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_keyword
     *
     * @param string $newspaper_keyword
     * @return $this
     */
    public function setNewspaperKeyword($newspaper_keyword)
    {
        $this->newspaper_keyword = $newspaper_keyword;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_title
     *
     * @param string $newspaper_title
     * @return $this
     */
    public function setNewspaperTitle($newspaper_title)
    {
        $this->newspaper_title = $newspaper_title;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_meta_keyword
     *
     * @param string $newspaper_meta_keyword
     * @return $this
     */
    public function setNewspaperMetaKeyword($newspaper_meta_keyword)
    {
        $this->newspaper_meta_keyword = $newspaper_meta_keyword;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_meta_description
     *
     * @param string $newspaper_meta_description
     * @return $this
     */
    public function setNewspaperMetaDescription($newspaper_meta_description)
    {
        $this->newspaper_meta_description = $newspaper_meta_description;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_meta_image
     *
     * @param string $newspaper_meta_image
     * @return $this
     */
    public function setNewspaperMetaImage($newspaper_meta_image)
    {
        $this->newspaper_meta_image = $newspaper_meta_image;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_link
     *
     * @param string $newspaper_link
     * @return $this
     */
    public function setNewspaperLink($newspaper_link)
    {
        $this->newspaper_link = $newspaper_link;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_order
     *
     * @param integer $newspaper_order
     * @return $this
     */
    public function setNewspaperOrder($newspaper_order)
    {
        $this->newspaper_order = $newspaper_order;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_active
     *
     * @param string $newspaper_active
     * @return $this
     */
    public function setNewspaperActive($newspaper_active)
    {
        $this->newspaper_active = $newspaper_active;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_insert_time
     *
     * @param integer $newspaper_insert_time
     * @return $this
     */
    public function setNewspaperInsertTime($newspaper_insert_time)
    {
        $this->newspaper_insert_time = $newspaper_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field newspaper_update_time
     *
     * @param integer $newspaper_update_time
     * @return $this
     */
    public function setNewspaperUpdateTime($newspaper_update_time)
    {
        $this->newspaper_update_time = $newspaper_update_time;

        return $this;
    }

    /**
     * Returns the value of field newspaper_id
     *
     * @return integer
     */
    public function getNewspaperId()
    {
        return $this->newspaper_id;
    }

    /**
     * Returns the value of field newspaper_name
     *
     * @return string
     */
    public function getNewspaperName()
    {
        return $this->newspaper_name;
    }

    /**
     * Returns the value of field newspaper_logo
     *
     * @return string
     */
    public function getNewspaperLogo()
    {
        return $this->newspaper_logo;
    }

    /**
     * Returns the value of field newspaper_keyword
     *
     * @return string
     */
    public function getNewspaperKeyword()
    {
        return $this->newspaper_keyword;
    }

    /**
     * Returns the value of field newspaper_title
     *
     * @return string
     */
    public function getNewspaperTitle()
    {
        return $this->newspaper_title;
    }

    /**
     * Returns the value of field newspaper_meta_keyword
     *
     * @return string
     */
    public function getNewspaperMetaKeyword()
    {
        return $this->newspaper_meta_keyword;
    }

    /**
     * Returns the value of field newspaper_meta_description
     *
     * @return string
     */
    public function getNewspaperMetaDescription()
    {
        return $this->newspaper_meta_description;
    }

    /**
     * Returns the value of field newspaper_meta_image
     *
     * @return string
     */
    public function getNewspaperMetaImage()
    {
        return $this->newspaper_meta_image;
    }

    /**
     * Returns the value of field newspaper_link
     *
     * @return string
     */
    public function getNewspaperLink()
    {
        return $this->newspaper_link;
    }

    /**
     * Returns the value of field newspaper_order
     *
     * @return integer
     */
    public function getNewspaperOrder()
    {
        return $this->newspaper_order;
    }

    /**
     * Returns the value of field newspaper_active
     *
     * @return string
     */
    public function getNewspaperActive()
    {
        return $this->newspaper_active;
    }

    /**
     * Returns the value of field newspaper_insert_time
     *
     * @return integer
     */
    public function getNewspaperInsertTime()
    {
        return $this->newspaper_insert_time;
    }

    /**
     * Returns the value of field newspaper_update_time
     *
     * @return integer
     */
    public function getNewspaperUpdateTime()
    {
        return $this->newspaper_update_time;
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
        return 'sc_newspaper';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScNewspaper[]|ScNewspaper
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScNewspaper
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findAllNewspaper($limit=null)
    {
        return ScNewspaper::find(array(
            "newspaper_active = 'Y' ",
            'order' => 'newspaper_order ASC',
            'limit' => $limit
        ));
    }
    public static function findRelatedByNewspaperKey($keyword,$limit=null)
    {
        return ScNewspaper::find(array(
            "newspaper_active = 'Y' AND newspaper_keyword != :KEY:",
            'bind' => ['KEY'=>$keyword],
            'order' => 'newspaper_order ASC',
            'limit' => $limit
        ));
    }
    public static function findFirstNewspaperByKey($keyword)
    {
        return ScNewspaper::findFirst(array(
            "newspaper_active = 'Y' AND newspaper_keyword = :KEY: ",
            'bind' => ['KEY'=>$keyword]
        ));
    }

}
