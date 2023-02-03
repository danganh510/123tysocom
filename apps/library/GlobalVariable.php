<?php

use Phalcon\Mvc\User\Component;

class GlobalVariable extends Component
{
	public $acceptUploadTypes;
	public $acceptUploadCV;
	public $timeZone;
    public $curTime;
    public $localTime;
    public $defaultLocation;
    public $defaultLanguage;
    public $typeInformationId;
    public $typeAboutusId;
    public $typeServicesId;
    public $typeCorporateSocialResponsibilityId;
    public $typeNewsId;
    public $typeInternalNewsId;
    public $typePressCornerNewsId;
    public $typePublicHolidaysId;
    public $typeEconomyId;
    public $typeInvestingId;
    public $typeMediaMarketingId;
    public $typeTravelsId;
    public $typePulicationsId;
    public $typeCareersId;
    public $typeNewsroom;
    public $maximumSizeUploadFile;
    public $urlFlag;
    public $cronToken;
    public $defaultCountry;
    public $programmableSearchEngineCxKey;
    public $website;
	public function __construct()
	{
        date_default_timezone_set('UTC');//default for Application - NOT ONLY for current script
        $this->timeZone = 7*3600;
        $this->curTime = time();
        $this->localTime = time() + $this->timeZone;
        $this->gtmStr = 'GMT+7';
        $this->defaultLocation ='gx';
        $this->defaultLanguage ='en';
        $this->defaultCountry = "gb";
        $this->urlFlag = 'https://d3nqrmb1lqq5py.cloudfront.net/images/flag/';
	    //accept upload file types
		$this->acceptUploadTypes = array(
			"image/jpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/pjpeg" => array("type" => "image", "ext" => ".jpg"),
			"image/png" => array("type" => "image", "ext" => ".png"),
			"image/bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-windows-bmp" => array("type" => "image", "ext" => ".bmp"),
			"image/x-icon" => array("type" => "image", "ext" => ".ico"),
			"image/ico" => array("type" => "image", "ext" => ".ico"),
			"image/gif" => array("type" => "image", "ext" => ".gif"),
			"text/plain" => array("type" => "file", "ext" => ".txt"),
			"application/msword" => array("type" => "file", "ext" => ".doc"),
			"application/vnd.openxmlformats-officedocument.wordprocessingml.document" => array("type" => "file", "ext" => ".docx"),
			"application/vnd.ms-excel" => array("type" => "file", "ext" => ".xls"),
			"application/vnd.openxmlformats-officedocument.spreadsheetml.sheet" => array("type" => "file", "ext" => ".xlsx"),
			"application/pdf" => array("type" => "file", "ext" => ".pdf"),
		);

        $this->acceptUploadCV = array(
            "application/msword" => array("type" => "file", "ext" => ".doc"),
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document" => array("type" => "file", "ext" => ".docx"),
            "application/pdf" => array("type" => "file", "ext" => ".pdf"),
            "application/zip" => array("type" => "file", "ext" => ".zip"),
            "application/x-zip-compressed" => array("type" => "file", "ext" => ".zip"),
        );

        $acceptUploadCVExts = array();
        foreach ($this->acceptUploadCV as $key => $data) {
            $ext = trim(strtolower(str_replace('.', '', $data['ext'])));
            if (!in_array($ext, $acceptUploadCVExts)) {
                $acceptUploadCVExts[] = $ext;
            }
        }
        $this->acceptUploadCVExts = $acceptUploadCVExts;

            //accept upload file types
        $this->contentTypeImages = array(
            'png' => 'image/png',
            'jpe' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            'txt' => 'text/plain',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'pdf'  => 'application/pdf',
        );
        $this->linkS3FlagLanguage = 'https://d3nqrmb1lqq5py.cloudfront.net/images/flag/';
        $this->maximumSizeUploadFile = 20971520;
        $this->cronToken = "ThanhLongScCorp0292";
        $this->programmableSearchEngineCxKey = '9fdc6b14b9ffedf47';
        $this->typeInformationId = 1;
        $this->typeAboutUsId = 2;
        $this->typeServicesId = 3;
        $this->typeCorporateSocialResponsibilityId = 4;
        $this->typeNewsId = 5;
        $this->typeCareersId = 6;
        $this->typeInternalNewsId = 7;
        $this->typePressCornerNewsId = 8;
        $this->typePublicHolidaysId = 9;
        $this->typeEconomyId = 10;
        $this->typeInvestingId = 11;
        $this->typeMediaMarketingId = 12;
        $this->typeTravelsId = 13;
        $this->typePulicationsId = 14;
        $this->typeNewsroom = 15;
        $this->website = 'bincg.com';
	}
}