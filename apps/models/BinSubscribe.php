<?php

namespace Bincg\Models;

class BinSubscribe extends \Phalcon\Mvc\Model
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $subscribe_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $subscribe_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $subscribe_ip;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $subscribe_insert_time;

    /**
     * Method to set the value of field subscribe_id
     *
     * @param integer $subscribe_id
     * @return $this
     */
    public function setSubscribeId($subscribe_id)
    {
        $this->subscribe_id = $subscribe_id;

        return $this;
    }

    /**
     * Method to set the value of field subscribe_email
     *
     * @param string $subscribe_email
     * @return $this
     */
    public function setSubscribeEmail($subscribe_email)
    {
        $this->subscribe_email = $subscribe_email;

        return $this;
    }

    /**
     * Method to set the value of field subscribe_ip
     *
     * @param string $subscribe_ip
     * @return $this
     */
    public function setSubscribeIp($subscribe_ip)
    {
        $this->subscribe_ip = $subscribe_ip;

        return $this;
    }

    /**
     * Method to set the value of field subscribe_insert_time
     *
     * @param integer $subscribe_insert_time
     * @return $this
     */
    public function setSubscribeInsertTime($subscribe_insert_time)
    {
        $this->subscribe_insert_time = $subscribe_insert_time;

        return $this;
    }

    /**
     * Returns the value of field subscribe_id
     *
     * @return integer
     */
    public function getSubscribeId()
    {
        return $this->subscribe_id;
    }

    /**
     * Returns the value of field subscribe_email
     *
     * @return string
     */
    public function getSubscribeEmail()
    {
        return $this->subscribe_email;
    }

    /**
     * Returns the value of field subscribe_ip
     *
     * @return string
     */
    public function getSubscribeIp()
    {
        return $this->subscribe_ip;
    }

    /**
     * Returns the value of field subscribe_insert_time
     *
     * @return integer
     */
    public function getSubscribeInsertTime()
    {
        return $this->subscribe_insert_time;
    }

    /**
     * Initialize method for model.
     */
//    public function initialize()
//    {
//        $this->setSchema("bincg_com");
//    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'bin_subscribe';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinSubscribe[]|BinSubscribe
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinSubscribe
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstByEmail($email)
    {
        return parent::findFirst([
            'conditions'  => 'subscribe_email = :email: ',
            'bind'        => ['email' => $email]
        ]);
    }
}
