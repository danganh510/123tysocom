<?php

namespace Score\Models;

class ScUser extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $user_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_email;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $user_password;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $user_role_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $user_insert_time;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $user_active;

    /**
     * Method to set the value of field user_id
     *
     * @param integer $user_id
     * @return $this
     */
    public function setUserId($user_id)
    {
        $this->user_id = $user_id;

        return $this;
    }

    /**
     * Method to set the value of field user_name
     *
     * @param string $user_name
     * @return $this
     */
    public function setUserName($user_name)
    {
        $this->user_name = $user_name;

        return $this;
    }

    /**
     * Method to set the value of field user_email
     *
     * @param string $user_email
     * @return $this
     */
    public function setUserEmail($user_email)
    {
        $this->user_email = $user_email;

        return $this;
    }

    /**
     * Method to set the value of field user_password
     *
     * @param string $user_password
     * @return $this
     */
    public function setUserPassword($user_password)
    {
        $this->user_password = $user_password;

        return $this;
    }

    /**
     * Method to set the value of field user_role_id
     *
     * @param integer $user_role_id
     * @return $this
     */
    public function setUserRoleId($user_role_id)
    {
        $this->user_role_id = $user_role_id;

        return $this;
    }

    /**
     * Method to set the value of field user_insert_time
     *
     * @param integer $user_insert_time
     * @return $this
     */
    public function setUserInsertTime($user_insert_time)
    {
        $this->user_insert_time = $user_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field user_active
     *
     * @param string $user_active
     * @return $this
     */
    public function setUserActive($user_active)
    {
        $this->user_active = $user_active;

        return $this;
    }

    /**
     * Returns the value of field user_id
     *
     * @return integer
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Returns the value of field user_name
     *
     * @return string
     */
    public function getUserName()
    {
        return $this->user_name;
    }

    /**
     * Returns the value of field user_email
     *
     * @return string
     */
    public function getUserEmail()
    {
        return $this->user_email;
    }

    /**
     * Returns the value of field user_password
     *
     * @return string
     */
    public function getUserPassword()
    {
        return $this->user_password;
    }

    /**
     * Returns the value of field user_role_id
     *
     * @return integer
     */
    public function getUserRoleId()
    {
        return $this->user_role_id;
    }

    /**
     * Returns the value of field user_insert_time
     *
     * @return integer
     */
    public function getUserInsertTime()
    {
        return $this->user_insert_time;
    }

    /**
     * Returns the value of field user_active
     *
     * @return string
     */
    public function getUserActive()
    {
        return $this->user_active;
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
        return 'sc_user';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScUser[]|ScUser
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScUser
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

    public static function findFirstByEmail($email)
    {
        return self::findFirst(array(
            'user_email = :user_email: AND user_active="Y"',
            'bind' => array('user_email' => $email)
        ));
    }

    public static function findFirstByRole($role_id){
        return self::findFirst(array(
            'user_role_id = :user_role: ',
            'bind' => array('user_role' => $role_id)
        ));
    }
    public  static function findFirstById($id){
        return self::findFirst(array(
            'user_id = :user_id: ',
            'bind' => array('user_id' => $id)
        ));
    }
}
