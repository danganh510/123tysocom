<?php

namespace Bincg\Models;

class BinCareerOffice extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $co_career_id;

    /**
     *
     * @var integer
     * @Primary
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $co_office_id;

    /**
     * Method to set the value of field co_career_id
     *
     * @param integer $co_career_id
     * @return $this
     */
    public function setCoCareerId($co_career_id)
    {
        $this->co_career_id = $co_career_id;

        return $this;
    }

    /**
     * Method to set the value of field co_office_id
     *
     * @param integer $co_office_id
     * @return $this
     */
    public function setCoOfficeId($co_office_id)
    {
        $this->co_office_id = $co_office_id;

        return $this;
    }

    /**
     * Returns the value of field co_career_id
     *
     * @return integer
     */
    public function getCoCareerId()
    {
        return $this->co_career_id;
    }

    /**
     * Returns the value of field co_office_id
     *
     * @return integer
     */
    public function getCoOfficeId()
    {
        return $this->co_office_id;
    }

    /**
     * Initialize method for model.
     */
   /* public function initialize()
    {
        $this->setSchema("offshorecompanycorp_com");
    }
*/
    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCareerOffice[]|BinCareerOffice
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinCareerOffice
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_career_office';
    }

}
