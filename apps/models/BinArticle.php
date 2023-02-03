<?php

namespace Bincg\Models;

class BinArticle extends BaseModel
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
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_type_id;


    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_name;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_email_support;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_icon;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_icon_large;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_icon_large_mobile;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_logo;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_logo_active;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=true)
     */
    protected $article_meta_image;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_title;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_meta_keyword;

    /**
     *
     * @var string
     * @Column(type="string", length=255, nullable=false)
     */
    protected $article_meta_description;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $article_summary;

    /**
     *
     * @var string
     * @Column(type="string", nullable=true)
     */
    protected $article_content;

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
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_is_home;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_is_header;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_is_horizontal;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_is_footer;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    protected $article_full_style;

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
     * Method to set the value of field article_type_id
     *
     * @param string $article_type_id
     * @return $this
     */
    public function setArticleTypeId($article_type_id)
    {
        $this->article_type_id = $article_type_id;

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
     * Method to set the value of field article_email_support
     *
     * @param string $article_email_support
     * @return $this
     */
    public function setArticleEmailSupport($article_email_support)
    {
        $this->article_email_support = $article_email_support;

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
     * Method to set the value of field article_icon_large
     *
     * @param string $article_icon_large
     * @return $this
     */
    public function setArticleIconLarge($article_icon_large)
    {
        $this->article_icon_large = $article_icon_large;

        return $this;
    }

    /**
     * Method to set the value of field article_icon_large_mobile
     *
     * @param string $article_icon_large_mobile
     * @return $this
     */
    public function setArticleIconLargeMobile($article_icon_large_mobile)
    {
        $this->article_icon_large_mobile = $article_icon_large_mobile;

        return $this;
    }

    /**
     * Method to set the value of field article_logo
     *
     * @param string $article_logo
     * @return $this
     */
    public function setArticleLogo($article_logo)
    {
        $this->article_logo = $article_logo;

        return $this;
    }

    /**
     * Method to set the value of field article_logo_active
     *
     * @param string $article_logo_active
     * @return $this
     */
    public function setArticleLogoActive($article_logo_active)
    {
        $this->article_logo_active = $article_logo_active;

        return $this;
    }

    /**
     * Method to set the value of field article_meta_image
     *
     * @param string $article_meta_image
     * @return $this
     */
    public function setArticleMetaImage($article_meta_image)
    {
        $this->article_meta_image = $article_meta_image;

        return $this;
    }

    /**
     * Method to set the value of field article_keyword
     *
     * @param string $article_keyword
     * @return $this
     */
    public function setArticleKeyword($article_keyword)
    {
        $this->article_keyword = $article_keyword;

        return $this;
    }

    /**
     * Method to set the value of field article_title
     *
     * @param string $article_title
     * @return $this
     */
    public function setArticleTitle($article_title)
    {
        $this->article_title = $article_title;

        return $this;
    }

    /**
     * Method to set the value of field article_meta_keyword
     *
     * @param string $article_meta_keyword
     * @return $this
     */
    public function setArticleMetaKeyword($article_meta_keyword)
    {
        $this->article_meta_keyword = $article_meta_keyword;

        return $this;
    }

    /**
     * Method to set the value of field article_meta_description
     *
     * @param string $article_meta_description
     * @return $this
     */
    public function setArticleMetaDescription($article_meta_description)
    {
        $this->article_meta_description = $article_meta_description;

        return $this;
    }

    /**
     * Method to set the value of field article_summary
     *
     * @param string $article_summary
     * @return $this
     */
    public function setArticleSummary($article_summary)
    {
        $this->article_summary = $article_summary;

        return $this;
    }

    /**
     * Method to set the value of field article_content
     *
     * @param string $article_content
     * @return $this
     */
    public function setArticleContent($article_content)
    {
        $this->article_content = $article_content;

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
     * Method to set the value of field article_is_home
     *
     * @param string $article_is_home
     * @return $this
     */
    public function setArticleIsHome($article_is_home)
    {
        $this->article_is_home = $article_is_home;

        return $this;
    }

    /**
     * Method to set the value of field article_is_header
     *
     * @param string $article_is_header
     * @return $this
     */
    public function setArticleIsHeader($article_is_header)
    {
        $this->article_is_header = $article_is_header;

        return $this;
    }

    /**
     * Method to set the value of field article_is_horizontal
     *
     * @param string $article_is_horizontal
     * @return $this
     */
    public function setArticleIsHorizontal($article_is_horizontal)
    {
        $this->article_is_horizontal = $article_is_horizontal;

        return $this;
    }

    /**
     * Method to set the value of field article_is_footer
     *
     * @param string $article_is_footer
     * @return $this
     */
    public function setArticleIsFooter($article_is_footer)
    {
        $this->article_is_footer = $article_is_footer;

        return $this;
    }

    /**
     * Method to set the value of field article_full_style
     *
     * @param string $article_full_style
     * @return $this
     */
    public function setArticleFullStyle($article_full_style)
    {
        $this->article_full_style = $article_full_style;

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
     * Returns the value of field article_type_id
     *
     * @return string
     */
    public function getArticleTypeId()
    {
        return $this->article_type_id;
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
     * Returns the value of field article_email_support
     *
     * @return string
     */
    public function getArticleEmailSupport()
    {
        return $this->article_email_support;
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
     * Returns the value of field article_icon_large
     *
     * @return string
     */
    public function getArticleIconLarge()
    {
        return $this->article_icon_large;
    }

    /**
     * Returns the value of field article_icon_large_mobile
     *
     * @return string
     */
    public function getArticleIconLargeMobile()
    {
        return $this->article_icon_large_mobile;
    }

    /**
     * Returns the value of field article_logo
     *
     * @return string
     */
    public function getArticleLogo()
    {
        return $this->article_logo;
    }

    /**
     * Returns the value of field article_logo_active
     *
     * @return string
     */
    public function getArticleLogoActive()
    {
        return $this->article_logo_active;
    }

    /**
     * Returns the value of field article_meta_image
     *
     * @return string
     */
    public function getArticleMetaImage()
    {
        return $this->article_meta_image;
    }

    /**
     * Returns the value of field article_keyword
     *
     * @return string
     */
    public function getArticleKeyword()
    {
        return $this->article_keyword;
    }

    /**
     * Returns the value of field article_title
     *
     * @return string
     */
    public function getArticleTitle()
    {
        return $this->article_title;
    }

    /**
     * Returns the value of field article_meta_keyword
     *
     * @return string
     */
    public function getArticleMetaKeyword()
    {
        return $this->article_meta_keyword;
    }

    /**
     * Returns the value of field article_meta_description
     *
     * @return string
     */
    public function getArticleMetaDescription()
    {
        return $this->article_meta_description;
    }

    /**
     * Returns the value of field article_summary
     *
     * @return string
     */
    public function getArticleSummary($langString = null)
    {
        if (is_string($this->article_summary) && is_string($langString)) {
            return str_replace('|||LANG|||', $langString, $this->article_summary);
        }
        return $this->article_summary;
    }

    /**
     * Returns the value of field article_content
     *
     * @return string
     */
    public function getArticleContent($langString = null, $script = false)
    {
        $artical_content_langString = $this->article_content;
        if (is_string($artical_content_langString) && is_string($langString)) {
            $artical_content_langString = str_replace('|||LANG|||', $langString, $artical_content_langString);
        }
        if (is_string($artical_content_langString) && $script) {
            $artical_content_langString = str_replace(array('|||SCRIPTBEFORE|||', '|||SCRIPTAFTER|||', '|||NOSCRIPTBEFORE|||', '|||NOSCRIPTAFTER|||'),array('<script>', '</script>', '<noscript>', '</noscript>'),$artical_content_langString);
        }
        return $artical_content_langString;
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
     * Returns the value of field article_is_home
     *
     * @return string
     */
    public function getArticleIsHome()
    {
        return $this->article_is_home;
    }

    /**
     * Returns the value of field article_is_header
     *
     * @return string
     */
    public function getArticleIsHeader()
    {
        return $this->article_is_header;
    }

    /**
     * Returns the value of field article_is_horizontal
     *
     * @return string
     */
    public function getArticleIsHorizontal()
    {
        return $this->article_is_horizontal;
    }

    /**
     * Returns the value of field article_is_footer
     *
     * @return string
     */
    public function getArticleIsFooter()
    {
        return $this->article_is_footer;
    }

    /**
     * Returns the value of field article_full_style
     *
     * @return string
     */
    public function getArticleFullStyle()
    {
        return $this->article_full_style;
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
        return 'bin_article';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinArticle[]|BinArticle
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return BinArticle
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }


}
