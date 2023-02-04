<?php

namespace Score\Models;

class ScNewspaperArticle extends BaseModel
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $article_id;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $article_newspaper_id;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_icon;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_link;

    /**
     *
     * @var integer
     * @Column(type="integer", length=10, nullable=false)
     */
    protected $article_order;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_active;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $article_insert_time;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    protected $article_update_time;

    /**
     * Method to set the value of field article_id
     *
     * @param integer $article_id
     * @return $this
     */
    public function setArticleId($article_id)
    {
        $this->article_id = $article_id;

        return $this;
    }

    /**
     * Method to set the value of field article_newspaper_id
     *
     * @param string $article_newspaper_id
     * @return $this
     */
    public function setArticleNewspaperId($article_newspaper_id)
    {
        $this->article_newspaper_id = $article_newspaper_id;

        return $this;
    }

    /**
     * Method to set the value of field article_name
     *
     * @param string $article_name
     * @return $this
     */
    public function setArticleName($article_name)
    {
        $this->article_name = $article_name;

        return $this;
    }

    /**
     * Method to set the value of field article_icon
     *
     * @param string $article_icon
     * @return $this
     */
    public function setArticleIcon($article_icon)
    {
        $this->article_icon = $article_icon;

        return $this;
    }

    /**
     * Method to set the value of field article_link
     *
     * @param string $article_link
     * @return $this
     */
    public function setArticleLink($article_link)
    {
        $this->article_link = $article_link;

        return $this;
    }

    /**
     * Method to set the value of field article_order
     *
     * @param integer $article_order
     * @return $this
     */
    public function setArticleOrder($article_order)
    {
        $this->article_order = $article_order;

        return $this;
    }

    /**
     * Method to set the value of field article_active
     *
     * @param string $article_active
     * @return $this
     */
    public function setArticleActive($article_active)
    {
        $this->article_active = $article_active;

        return $this;
    }

    /**
     * Method to set the value of field article_insert_time
     *
     * @param integer $article_insert_time
     * @return $this
     */
    public function setArticleInsertTime($article_insert_time)
    {
        $this->article_insert_time = $article_insert_time;

        return $this;
    }

    /**
     * Method to set the value of field article_update_time
     *
     * @param integer $article_update_time
     * @return $this
     */
    public function setArticleUpdateTime($article_update_time)
    {
        $this->article_update_time = $article_update_time;

        return $this;
    }

    /**
     * Returns the value of field article_id
     *
     * @return integer
     */
    public function getArticleId()
    {
        return $this->article_id;
    }

    /**
     * Returns the value of field article_newspaper_id
     *
     * @return string
     */
    public function getArticleNewspaperId()
    {
        return $this->article_newspaper_id;
    }

    /**
     * Returns the value of field article_name
     *
     * @return string
     */
    public function getArticleName()
    {
        return $this->article_name;
    }

    /**
     * Returns the value of field article_icon
     *
     * @return string
     */
    public function getArticleIcon()
    {
        return $this->article_icon;
    }

    /**
     * Returns the value of field article_link
     *
     * @return string
     */
    public function getArticleLink()
    {
        return $this->article_link;
    }

    /**
     * Returns the value of field article_order
     *
     * @return integer
     */
    public function getArticleOrder()
    {
        return $this->article_order;
    }

    /**
     * Returns the value of field article_active
     *
     * @return string
     */
    public function getArticleActive()
    {
        return $this->article_active;
    }

    /**
     * Returns the value of field article_insert_time
     *
     * @return integer
     */
    public function getArticleInsertTime()
    {
        return $this->article_insert_time;
    }

    /**
     * Returns the value of field article_update_time
     *
     * @return integer
     */
    public function getArticleUpdateTime()
    {
        return $this->article_update_time;
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
        return 'sc_newspaper_article';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScNewspaperArticle[]|ScNewspaperArticle
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return ScNewspaperArticle
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }
}
