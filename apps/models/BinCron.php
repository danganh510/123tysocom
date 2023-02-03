<?php

namespace Score\Models;

class ScCron extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $cron_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $cron_type;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $cron_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $cron_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $cron_total_time;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $cron_user;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $cron_status;

    /**
     * Method to set the value of field cron_id
     *
     * @param integer $cron_id
     * @return $this
     */
    public function setCronId($cron_id)
    {
        $this->cron_id = $cron_id;

        return $this;
    }

    /**
     * Method to set the value of field cron_type
     *
     * @param string $cron_type
     * @return $this
     */
    public function setCronType($cron_type)
    {
        $this->cron_type = $cron_type;

        return $this;
    }

    /**
     * Method to set the value of field cron_insert_time
     *
     * @param integer $cron_insert_time
     * @return $this
     */
    public function setCronInsertTime($cron_insert_time)
    {
        $this->cron_insert_time = $cron_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field cron_active
     *
     * @param string $cron_active
     * @return $this
     */
    public function setCronActive($cron_active)
    {
        $this->cron_active = $cron_active;

        return $this;
    }

    /**
     * Method to set the value of field cron_total_time
     *
     * @param integer $cron_total_time
     * @return $this
     */
    public function setCronTotalTime($cron_total_time)
    {
        $this->cron_total_time = $cron_total_time;

        return $this;
    }

    /**
     * Method to set the value of field cron_user
     *
     * @param string $cron_user
     * @return $this
     */
    public function setCronUser($cron_user)
    {
        $this->cron_user = $cron_user;

        return $this;
    }

    /**
     * Method to set the value of field cron_status
     *
     * @param string $cron_status
     * @return $this
     */
    public function setCronStatus($cron_status)
    {
        $this->cron_status = $cron_status;

        return $this;
    }

    /**
     * Returns the value of field cron_id
     *
     * @return integer
     */
    public function getCronId()
    {
        return $this->cron_id;
    }

    /**
     * Returns the value of field cron_type
     *
     * @return string
     */
    public function getCronType()
    {
        return $this->cron_type;
    }

    /**
     * Returns the value of field cron_insert_time
     *
     * @return integer
     */
    public function getCronInsertTime()
    {
        return $this->cron_insert_time;
    }

    /**
     * Returns the value of field cron_active
     *
     * @return string
     */
    public function getCronActive()
    {
        return $this->cron_active;
    }

    /**
     * Returns the value of field cron_total_time
     *
     * @return integer
     */
    public function getCronTotalTime()
    {
        return $this->cron_total_time;
    }

    /**
     * Returns the value of field cron_user
     *
     * @return string
     */
    public function getCronUser()
    {
        return $this->cron_user;
    }

    /**
     * Returns the value of field cron_status
     *
     * @return string
     */
    public function getCronStatus()
    {
        return $this->cron_status;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sc_cron';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCron[]|ScCron
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCron
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById($id)
    {
        return parent::findFirst(array(
            "cron_id = :cron_id:",
            'bind' => array(
                'cron_id' => $id
            )
        ));
    }
    public static function findFirstByType($type)
    {
        return parent::findFirst(array(
            "cron_type = :cron_type: and cron_active = 'Y' ",
            'bind' => array(
                'cron_type' => $type
            )
        ));
    }
    public static function findFirstByActive($active)
    {
        return parent::findFirst(array(
            "cron_active = :cron_active: ",
            'bind' => array(
                'cron_active' => $active
            )
        ));
    }
    public function  getStringLanguageCode()
    {
        $result="";
        $list_cron_detail = ScCronDetail::find(array(
            "detail_cron_id = :detail_cron_id: and detail_active = 'N'",
            'bind' => array(
                'detail_cron_id' => $this->cron_id
            )
        ));
        $arr_cron_detail = array();
        foreach ($list_cron_detail as $cron_detail)
        {
            $arr_cron_detail[]="'".$cron_detail->getDetailLangCode()."'";
        }
        if(count($arr_cron_detail)>0)
        {
            $result=implode(',', $arr_cron_detail);
        }
        return $result;
    }
}
