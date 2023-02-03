<?php

namespace Score\Models;

use Phalcon\Db\RawValue;

class ScCronDetail extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $detai_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $detail_cron_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $detail_table;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $detail_status;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $detail_data;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $detail_total;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $detail_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $detail_active;

    /**
     *
     * @var string
     * @Column(type="string", length=5, nullable=false)
     */
    protected $detail_lang_code;

    /**
     * Method to set the value of field detai_id
     *
     * @param integer $detai_id
     * @return $this
     */
    public function setDetaiId($detai_id)
    {
        $this->detai_id = $detai_id;

        return $this;
    }

    /**
     * Method to set the value of field detail_cron_id
     *
     * @param integer $detail_cron_id
     * @return $this
     */
    public function setDetailCronId($detail_cron_id)
    {
        $this->detail_cron_id = $detail_cron_id;

        return $this;
    }

    /**
     * Method to set the value of field detail_table
     *
     * @param string $detail_table
     * @return $this
     */
    public function setDetailTable($detail_table)
    {
        $this->detail_table = $detail_table;

        return $this;
    }

    /**
     * Method to set the value of field detail_status
     *
     * @param string $detail_status
     * @return $this
     */
    public function setDetailStatus($detail_status)
    {
        $this->detail_status = $detail_status;

        return $this;
    }

    /**
     * Method to set the value of field detail_data
     *
     * @param string $detail_data
     * @return $this
     */
    public function setDetailData($detail_data)
    {
        $this->detail_data = $detail_data;

        return $this;
    }

    /**
     * Method to set the value of field detail_total
     *
     * @param integer $detail_total
     * @return $this
     */
    public function setDetailTotal($detail_total)
    {
        $this->detail_total = $detail_total;

        return $this;
    }

    /**
     * Method to set the value of field detail_insert_time
     *
     * @param integer $detail_insert_time
     * @return $this
     */
    public function setDetailInsertTime($detail_insert_time)
    {
        $this->detail_insert_time = $detail_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field detail_active
     *
     * @param string $detail_active
     * @return $this
     */
    public function setDetailActive($detail_active)
    {
        $this->detail_active = $detail_active;

        return $this;
    }

    /**
     * Method to set the value of field detail_lang_code
     *
     * @param string $detail_lang_code
     * @return $this
     */
    public function setDetailLangCode($detail_lang_code)
    {
        $this->detail_lang_code = $detail_lang_code;

        return $this;
    }

    /**
     * Returns the value of field detai_id
     *
     * @return integer
     */
    public function getDetaiId()
    {
        return $this->detai_id;
    }

    /**
     * Returns the value of field detail_cron_id
     *
     * @return integer
     */
    public function getDetailCronId()
    {
        return $this->detail_cron_id;
    }

    /**
     * Returns the value of field detail_table
     *
     * @return string
     */
    public function getDetailTable()
    {
        return $this->detail_table;
    }

    /**
     * Returns the value of field detail_status
     *
     * @return string
     */
    public function getDetailStatus()
    {
        return $this->detail_status;
    }

    /**
     * Returns the value of field detail_data
     *
     * @return string
     */
    public function getDetailData()
    {
        return $this->detail_data;
    }

    /**
     * Returns the value of field detail_total
     *
     * @return integer
     */
    public function getDetailTotal()
    {
        return $this->detail_total;
    }

    /**
     * Returns the value of field detail_insert_time
     *
     * @return integer
     */
    public function getDetailInsertTime()
    {
        return $this->detail_insert_time;
    }

    /**
     * Returns the value of field detail_active
     *
     * @return string
     */
    public function getDetailActive()
    {
        return $this->detail_active;
    }

    /**
     * Returns the value of field detail_lang_code
     *
     * @return string
     */
    public function getDetailLangCode()
    {
        return $this->detail_lang_code;
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'sc_cron_detail';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCronDetail[]|ScCronDetail
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCronDetail
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstByCronId($cron_id)
    {
        return parent::findFirst(array(
            "detail_cron_id = :detail_cron_id: and detail_active = 'Y'",
            'bind' => array(
                'detail_cron_id' => $cron_id
            )
        ));
    }
    public static function  getTableByLangCode($lang_code,$cron_id)
    {
        $result = array();
        $list_cron_detail= ScCronDetail::find(array("detail_lang_code = :lang_code: and detail_cron_id = :cron_id:",
            'bind' => array(
                'lang_code' => $lang_code,
                'cron_id' =>$cron_id
            )
        ));
        foreach ($list_cron_detail as $cron_detail) {

            $result[] = $cron_detail->getDetailTable();
        }

        return $result;
    }
    public function getStringData()
    {
        $result="";
        if(!empty($this->detail_data))
        {
            $result=implode(',', json_decode($this->detail_data, true));
        }
        return $result;
    }
    public function beforeValidation()
    {
        if (empty($this->detail_data)) {
            $this->detail_data = new RawValue('\'\'');
        }
    }
}
