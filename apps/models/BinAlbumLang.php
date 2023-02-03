<?php

namespace Score\Models;

class ScAlbumLang extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $album_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $album_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $album_name;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $album_description;

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
     * Method to set the value of field album_lang_code
     *
     * @param string $album_lang_code
     * @return $this
     */
    public function setAlbumLangCode($album_lang_code)
    {
        $this->album_lang_code = $album_lang_code;

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
     * Returns the value of field album_id
     *
     * @return integer
     */
    public function getAlbumId()
    {
        return $this->album_id;
    }

    /**
     * Returns the value of field album_lang_code
     *
     * @return string
     */
    public function getAlbumLangCode()
    {
        return $this->album_lang_code;
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
     * Returns the value of field album_description
     *
     * @return string
     */
    public function getAlbumDescription()
    {
        return $this->album_description;
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
        return 'sc_album_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScAlbumLang[]|ScAlbumLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScAlbumLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findById($album_id)
    {
        return ScAlbumLang::find(array(
            "album_id =:ID:",
            'bind' => array('ID' => $album_id)
        ));
    }

}
