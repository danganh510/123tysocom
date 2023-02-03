<?php

namespace Bincg\Models;

class BinAlbum extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $album_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $album_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $album_keyword;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $album_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $album_description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $album_ishome;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $album_active;

    /**
     * Method to set the value of field album_id
     *
     * @param integer $album_id
     * @return $this
     */
    public function setAlbumId($album_id)
    {
        $this->album_id = $album_id;

        return $this;
    }

    /**
     * Method to set the value of field album_name
     *
     * @param string $album_name
     * @return $this
     */
    public function setAlbumName($album_name)
    {
        $this->album_name = $album_name;

        return $this;
    }

    /**
     * Method to set the value of field album_keyword
     *
     * @param string $album_keyword
     * @return $this
     */
    public function setAlbumKeyword($album_keyword)
    {
        $this->album_keyword = $album_keyword;

        return $this;
    }

    /**
     * Method to set the value of field album_insert_time
     *
     * @param integer $album_insert_time
     * @return $this
     */
    public function setAlbumInsertTime($album_insert_time)
    {
        $this->album_insert_time = $album_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field album_description
     *
     * @param string $album_description
     * @return $this
     */
    public function setAlbumDescription($album_description)
    {
        $this->album_description = $album_description;

        return $this;
    }

    /**
     * Method to set the value of field album_ishome
     *
     * @param string $album_ishome
     * @return $this
     */
    public function setAlbumIshome($album_ishome)
    {
        $this->album_ishome = $album_ishome;

        return $this;
    }

    /**
     * Method to set the value of field album_active
     *
     * @param string $album_active
     * @return $this
     */
    public function setAlbumActive($album_active)
    {
        $this->album_active = $album_active;

        return $this;
    }

    /**
     * Returns the value of field album_id
     *
     * @return integer
     */
    public function getAlbumId()
    {
        return $this->album_id;
    }

    /**
     * Returns the value of field album_name
     *
     * @return string
     */
    public function getAlbumName()
    {
        return $this->album_name;
    }

    /**
     * Returns the value of field album_keyword
     *
     * @return string
     */
    public function getAlbumKeyword()
    {
        return $this->album_keyword;
    }

    /**
     * Returns the value of field album_insert_time
     *
     * @return integer
     */
    public function getAlbumInsertTime()
    {
        return $this->album_insert_time;
    }

    /**
     * Returns the value of field album_description
     *
     * @return string
     */
    public function getAlbumDescription()
    {
        return $this->album_description;
    }

    /**
     * Returns the value of field album_ishome
     *
     * @return string
     */
    public function getAlbumIshome()
    {
        return $this->album_ishome;
    }

    /**
     * Returns the value of field album_active
     *
     * @return string
     */
    public function getAlbumActive()
    {
        return $this->album_active;
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
        return 'bin_album';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinAlbum[]|BinAlbum
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinAlbum
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
