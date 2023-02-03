<?php

namespace Bincg\Models;

class BinImageLang extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $image_id;

    /**
     *
     * @var string
     * @Primary
     * @Column(type="string", length=5, nullable=false)
     */
    protected $image_lang_code;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $image_description;

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
     * Method to set the value of field image_lang_code
     *
     * @param string $image_lang_code
     * @return $this
     */
    public function setImageLangCode($image_lang_code)
    {
        $this->image_lang_code = $image_lang_code;

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
     * Returns the value of field image_id
     *
     * @return integer
     */
    public function getImageId()
    {
        return $this->image_id;
    }

    /**
     * Returns the value of field image_lang_code
     *
     * @return string
     */
    public function getImageLangCode()
    {
        return $this->image_lang_code;
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
     * Initialize method for model.
     */
//    public function initialize()
////    {
////        $this->setSchema("bincgcom");
////    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_image_lang';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinImageLang[]|BinImageLang
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinImageLang
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findById($image_id)
    {
        return BinImageLang::find(array(
            "image_id =:ID:",
            'bind' => array('ID' => $image_id)
        ));
    }

}
