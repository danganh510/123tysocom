<?php

namespace Score\Models;

class ScCareer extends BaseModel
{

    /**
     *
     * @var integer
     */
    protected $career_id;

    /**
     *
     * @var string
     */
    protected $career_name;

    /**
     *
     * @var string
     */
    protected $career_icon;

    /**
     *
     * @var string
     */
    protected $career_meta_image;

    /**
     *
     * @var string
     */
    protected $career_location;

    /**
     *
     * @var string
     */
    protected $career_keyword;

    /**
     *
     * @var string
     */
    protected $career_title;

    /**
     *
     * @var string
     */
    protected $career_meta_keyword;

    /**
     *
     * @var string
     */
    protected $career_meta_description;

    /**
     *
     * @var string
     */
    protected $career_summary;

    /**
     *
     * @var string
     */
    protected $career_content;

    /**
     *
     * @var integer
     */
    protected $career_order;

    /**
     *
     * @var string
     */
    protected $career_featured;

    /**
     *
     * @var string
     */
    protected $career_is_home;

    /**
     *
     * @var string
     */
    protected $career_expired;

    /**
     *
     * @var integer
     */
    protected $career_insert_time;

    /**
     *
     * @var integer
     */
    protected $career_update_time;

    /**
     *
     * @var string
     */
    protected $career_skill;

    /**
     *
     * @var string
     */
    protected $career_experience_requirements;

    /**
     *
     * @var string
     */
    protected $career_job_benefits;

    /**
     *
     * @var string
     */
    protected $career_base_salary_schema;

    /**
     *
     * @var string
     */
    protected $career_active;

    /**
     * Method to set the value of field career_id
     *
     * @param integer $career_id
     * @return $this
     */
    public function setCareerId($career_id)
    {
        $this->career_id = $career_id;

        return $this;
    }

    /**
     * Method to set the value of field career_name
     *
     * @param string $career_name
     * @return $this
     */
    public function setCareerName($career_name)
    {
        $this->career_name = $career_name;

        return $this;
    }

    /**
     * Method to set the value of field career_icon
     *
     * @param string $career_icon
     * @return $this
     */
    public function setCareerIcon($career_icon)
    {
        $this->career_icon = $career_icon;

        return $this;
    }

    /**
     * Method to set the value of field career_meta_image
     *
     * @param string $career_meta_image
     * @return $this
     */
    public function setCareerMetaImage($career_meta_image)
    {
        $this->career_meta_image = $career_meta_image;

        return $this;
    }

    /**
     * Method to set the value of field career_location
     *
     * @param string $career_location
     * @return $this
     */
    public function setCareerLocation($career_location)
    {
        $this->career_location = $career_location;

        return $this;
    }

    /**
     * Method to set the value of field career_keyword
     *
     * @param string $career_keyword
     * @return $this
     */
    public function setCareerKeyword($career_keyword)
    {
        $this->career_keyword = $career_keyword;

        return $this;
    }

    /**
     * Method to set the value of field career_title
     *
     * @param string $career_title
     * @return $this
     */
    public function setCareerTitle($career_title)
    {
        $this->career_title = $career_title;

        return $this;
    }

    /**
     * Method to set the value of field career_meta_keyword
     *
     * @param string $career_meta_keyword
     * @return $this
     */
    public function setCareerMetaKeyword($career_meta_keyword)
    {
        $this->career_meta_keyword = $career_meta_keyword;

        return $this;
    }

    /**
     * Method to set the value of field career_meta_description
     *
     * @param string $career_meta_description
     * @return $this
     */
    public function setCareerMetaDescription($career_meta_description)
    {
        $this->career_meta_description = $career_meta_description;

        return $this;
    }

    /**
     * Method to set the value of field career_summary
     *
     * @param string $career_summary
     * @return $this
     */
    public function setCareerSummary($career_summary)
    {
        $this->career_summary = $career_summary;

        return $this;
    }

    /**
     * Method to set the value of field career_content
     *
     * @param string $career_content
     * @return $this
     */
    public function setCareerContent($career_content)
    {
        $this->career_content = $career_content;

        return $this;
    }

    /**
     * Method to set the value of field career_order
     *
     * @param integer $career_order
     * @return $this
     */
    public function setCareerOrder($career_order)
    {
        $this->career_order = $career_order;

        return $this;
    }

    /**
     * Method to set the value of field career_featured
     *
     * @param string $career_featured
     * @return $this
     */
    public function setCareerFeatured($career_featured)
    {
        $this->career_featured = $career_featured;

        return $this;
    }

    /**
     * Method to set the value of field career_is_home
     *
     * @param string $career_is_home
     * @return $this
     */
    public function setCareerIsHome($career_is_home)
    {
        $this->career_is_home = $career_is_home;

        return $this;
    }

    /**
     * Method to set the value of field career_expired
     *
     * @param string $career_expired
     * @return $this
     */
    public function setCareerExpired($career_expired)
    {
        $this->career_expired = $career_expired;

        return $this;
    }

    /**
     * Method to set the value of field career_insert_time
     *
     * @param integer $career_insert_time
     * @return $this
     */
    public function setCareerInsertTime($career_insert_time)
    {
        $this->career_insert_time = $career_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field career_update_time
     *
     * @param integer $career_update_time
     * @return $this
     */
    public function setCareerUpdateTime($career_update_time)
    {
        $this->career_update_time = $career_update_time;

        return $this;
    }

    /**
     * Method to set the value of field career_skill
     *
     * @param string $career_skill
     * @return $this
     */
    public function setCareerSkill($career_skill)
    {
        $this->career_skill = $career_skill;

        return $this;
    }

    /**
     * Method to set the value of field career_experience_requirements
     *
     * @param string $career_experience_requirements
     * @return $this
     */
    public function setCareerExperienceRequirements($career_experience_requirements)
    {
        $this->career_experience_requirements = $career_experience_requirements;

        return $this;
    }

    /**
     * Method to set the value of field career_job_benefits
     *
     * @param string $career_job_benefits
     * @return $this
     */
    public function setCareerJobBenefits($career_job_benefits)
    {
        $this->career_job_benefits = $career_job_benefits;

        return $this;
    }

    /**
     * Method to set the value of field career_base_salary_schema
     *
     * @param string $career_base_salary_schema
     * @return $this
     */
    public function setCareerBaseSalarySchema($career_base_salary_schema)
    {
        $this->career_base_salary_schema = $career_base_salary_schema;

        return $this;
    }

    /**
     * Method to set the value of field career_active
     *
     * @param string $career_active
     * @return $this
     */
    public function setCareerActive($career_active)
    {
        $this->career_active = $career_active;

        return $this;
    }

    /**
     * Returns the value of field career_id
     *
     * @return integer
     */
    public function getCareerId()
    {
        return $this->career_id;
    }

    /**
     * Returns the value of field career_name
     *
     * @return string
     */
    public function getCareerName()
    {
        return $this->career_name;
    }

    /**
     * Returns the value of field career_icon
     *
     * @return string
     */
    public function getCareerIcon()
    {
        return $this->career_icon;
    }

    /**
     * Returns the value of field career_meta_image
     *
     * @return string
     */
    public function getCareerMetaImage()
    {
        return $this->career_meta_image;
    }

    /**
     * Returns the value of field career_location
     *
     * @return string
     */
    public function getCareerLocation()
    {
        return $this->career_location;
    }

    /**
     * Returns the value of field career_keyword
     *
     * @return string
     */
    public function getCareerKeyword()
    {
        return $this->career_keyword;
    }

    /**
     * Returns the value of field career_title
     *
     * @return string
     */
    public function getCareerTitle()
    {
        return $this->career_title;
    }

    /**
     * Returns the value of field career_meta_keyword
     *
     * @return string
     */
    public function getCareerMetaKeyword()
    {
        return $this->career_meta_keyword;
    }

    /**
     * Returns the value of field career_meta_description
     *
     * @return string
     */
    public function getCareerMetaDescription()
    {
        return $this->career_meta_description;
    }

    /**
     * Returns the value of field career_summary
     *
     * @return string
     */
    public function getCareerSummary($langString = null)
    {
        if (is_string($this->career_summary) && is_string($langString)) {
            return str_replace('|||LANG|||', $langString, $this->career_summary);
        }
        return $this->career_summary;
    }

    /**
     * Returns the value of field career_content
     *
     * @return string
     */
    public function getCareerContent($langString = null)
    {
        if (is_string($this->career_content) && is_string($langString)) {
            return str_replace('|||LANG|||', $langString, $this->career_content);
        }
        return $this->career_content;
    }

    /**
     * Returns the value of field career_order
     *
     * @return integer
     */
    public function getCareerOrder()
    {
        return $this->career_order;
    }

    /**
     * Returns the value of field career_featured
     *
     * @return string
     */
    public function getCareerFeatured()
    {
        return $this->career_featured;
    }

    /**
     * Returns the value of field career_is_home
     *
     * @return string
     */
    public function getCareerIsHome()
    {
        return $this->career_is_home;
    }

    /**
     * Returns the value of field career_expired
     *
     * @return string
     */
    public function getCareerExpired()
    {
        return $this->career_expired;
    }

    /**
     * Returns the value of field career_insert_time
     *
     * @return integer
     */
    public function getCareerInsertTime()
    {
        return $this->career_insert_time;
    }

    /**
     * Returns the value of field career_update_time
     *
     * @return integer
     */
    public function getCareerUpdateTime()
    {
        return $this->career_update_time;
    }

    /**
     * Returns the value of field career_skill
     *
     * @return string
     */
    public function getCareerSkill()
    {
        return $this->career_skill;
    }

    /**
     * Returns the value of field career_experience_requirements
     *
     * @return string
     */
    public function getCareerExperienceRequirements()
    {
        return $this->career_experience_requirements;
    }

    /**
     * Returns the value of field career_job_benefits
     *
     * @return string
     */
    public function getCareerJobBenefits()
    {
        return $this->career_job_benefits;
    }

    /**
     * Returns the value of field career_base_salary_schema
     *
     * @return string
     */
    public function getCareerBaseSalarySchema()
    {
        return $this->career_base_salary_schema;
    }

    /**
     * Returns the value of field career_active
     *
     * @return string
     */
    public function getCareerActive()
    {
        return $this->career_active;
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
        return 'sc_career';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCareer[]|ScCareer
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScCareer
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
    public static function findFirstById($career_id)
    {
        return self::findFirst(array(
            "career_id =:ID:",
            'bind' => array('ID' => $career_id)
        ));
    }
}