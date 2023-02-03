<?php
namespace Bincg\Google;

use Google\Cloud\Translate\V3\GcsSource;
use Google\Cloud\Translate\V3\Glossary;
use Google\Cloud\Translate\V3\Glossary\LanguageCodesSet;
use Google\Cloud\Translate\V3\GlossaryInputConfig;
use Google\Cloud\Translate\V3\TranslateTextGlossaryConfig;
use Google\Cloud\Translate\V3\TranslationServiceClient;
use Phalcon\Mvc\User\Component;

class GoogleTranslate
{
    const LIMIT_CONTENT = 80000;
    const LIMIT_CUT_CONTENT = 25000;
    private $projectId = '503817953383';
    private $bucketName = 'bincgtranslations';
    private $fileNameGlosary = 'bincgcomglossary.csv';
    private $locationForGlosary = 'us-central1';
    private $locationFormat = 'global';
    private $glosaryIdPrefix = 'bincg_glossary_';
    private $glossary_id = '';
    public function setGlossaryId ($source='en',$tran_lang_code){
        $this->glossary_id = $this->glosaryIdPrefix.$tran_lang_code;

        //----- create all glossaries -----
//        $this->deletelistGlossaries();;
//        $repoLang = new LanguageSupportTranslate();
//        $data = $repoLang->getAll();
//        foreach ($data->toArray() as $key => $item) {
//            if ($item['language_code'] != 'en') {
//                $this->createGlossary($source, $item['language_code']);
//            }
//        }
    }
    public function translate($data, $target, $format = 'text/html', $source = 'en') {
        ini_set('max_execution_time', 300);
        if (defined('TRANSLATE_CLOUD_MODE') && TRANSLATE_CLOUD_MODE) {
            $count_data_lengh = strlen($data);
            $delay_time = self::process_delay_time($count_data_lengh);
            if($count_data_lengh > self::LIMIT_CUT_CONTENT){
                $arr_result = $this->translateMutliContent($data,$target,$delay_time,$format,$source);
            }else{
                $arr_result = $this->translateByApiSubtring($data,$target,$delay_time,$format,$source);
            }
            return $arr_result;
        } else {
            return array(
                "status" => "true",
                "data"   => $data,
            );
        }
    }
    private function translateByApiSubtring($data,$target,$delay_time,$format,$source){
        if(strlen($data)==0){
            $data = '';
            $arr_result = array(
                "status"       => "true",
                "data"         => $data
            );
            return $arr_result;
        }
        $response_data = '';
        $translationClient = new TranslationServiceClient();
        $projectId = $this->projectId;
        $glossaryPath = $translationClient->glossaryName(
            $projectId,
            $this->locationForGlosary,
            $this->glossary_id
        );
        $formattedParent = $translationClient->locationName(
            $projectId,
            $this->locationForGlosary
        );
        $glossaryConfig = new TranslateTextGlossaryConfig();
        $glossaryConfig->setGlossary($glossaryPath);
        try {
            $response = $translationClient->translateText(
                [$data],
                $target,
                $formattedParent,
                [
                    'sourceLanguageCode' => $source,
                    'glossaryConfig' => $glossaryConfig,
                    'mimeType' => $format
                ]
            );
            foreach ($response->getGlossaryTranslations() as $translation) {
                $response_data .= $translation->getTranslatedText();
            }
            sleep($delay_time);
        } finally {
            $translationClient->close();
        }
//        if(strlen($glossary_id) > 0){
//
//        }else{
//            $formattedParent = $translationClient->locationName($projectId, 'global');
//            try {
//                $response = $translationClient->translateText(
//                    [$data],
//                    $target,
//                    $formattedParent
//                );
//                foreach ($response->getTranslations() as $translation) {
//                    $response_data .= $translation->getTranslatedText();
//                }
//            } finally {
//                $translationClient->close();
//            }
//        }
        $arr_result = array(
            "status"       => "true",
            "errorcode"    => '',
            "errormessage" => '',
            "responsecode" => '',
            "data"         => $response_data
        );
        return $arr_result;
    }
    private function cutText($text)
    {
        $pos=0;
        $result=array();
        while(strpos($text, "</p>") !== false) {
            $pos =  strpos($text, "</p>");
            $pos += 4;
            $result[]=substr($text, 0, $pos);
            $text = substr($text, $pos);
        }
        if(strlen($text)>0)
        {
            $result[] =$text;
        }
        return $result;
    }
    private function translateMutliContent($content,$code_lang,$delay_time,$format,$source)
    {
        $message_error = "";
        $array_charater = array();
        $is_check = true;
        $status = "true";
        $temp="";
        $array_cuttext = self::cutText($content);
        foreach ($array_cuttext as $item)
        {
            if(strlen($temp)+strlen($item) < self::LIMIT_CUT_CONTENT )
            {
                $temp .= $item;
            }
            else
            {
                $array_charater[] =$temp;
                $temp = $item;
            }
        }
        if (strlen($temp)>0)
        {
            $array_charater[] = $temp;
        }
        $content_translate="";
        foreach ($array_charater as $content_item)
        {
            $ar_content = $this->translateByApiSubtring($content_item,$code_lang,$delay_time,$format,$source);
            if ($ar_content["status"] == "fail") {
                $message_error .= " Content  = " . $ar_content["errorcode"] . " - " . $ar_content["errormessage"] . $ar_content["responsecode"] . "<br>";
                $is_check = false;
                $status = "fail";
                break;
            }
            if($is_check)
            {
                $content_translate .= $ar_content["data"];
            }
        }
        $ar_status = array(
            "status"       => $status,
            "errorcode"    => '',
            "errormessage" => $message_error,
            "responsecode" => '',
            "data"         => $content_translate
        );
        return $ar_status;
    }
    private function process_delay_time($string_lenght)
    {
        $result = 0;
        if($string_lenght >= 25000)
        {
            $result = 6;
        }
        else if (15000 <= $string_lenght && $string_lenght < 25000)
        {
            $result = 5;
        }
        else if (10000 <= $string_lenght && $string_lenght < 15000)
        {
            $result = 2;

        }
        else if ($string_lenght < 10000 )
        {
            $result = 1;
        }
        return  $result ;
    }
    public function listLanguage($target){
        $arr_code_result = array();
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName($this->projectId, 'us-central1');
        try {
            $response = $translationServiceClient->getSupportedLanguages(
                $formattedParent,
                ['displayLanguageCode' => $target]
            );
            foreach ($response->getLanguages() as $language) {
                $arr_code_result[$language->getLanguageCode()] = $language->getDisplayName();
            }
        } finally {
            $translationServiceClient->close();
        }
        return $arr_code_result;
    }
    public function createGlossary($languageCodesElement = 'en',$languageCodesElement2){
        $translationServiceClient = new TranslationServiceClient();
        $projectId = $this->projectId;
        $glossaryId = $this->glosaryIdPrefix.$languageCodesElement2;
        $inputUri = 'gs://'.$this->bucketName.'/'.$this->fileNameGlosary;
        $formattedParent = $translationServiceClient->locationName(
            $projectId,
            $this->locationForGlosary
        );
        $formattedName = $translationServiceClient->glossaryName(
            $projectId,
            $this->locationForGlosary,
            $glossaryId
        );
        $languageCodes = [$languageCodesElement, $languageCodesElement2];
        $languageCodesSet = new LanguageCodesSet();
        $languageCodesSet->setLanguageCodes($languageCodes);
        $gcsSource = (new GcsSource())
            ->setInputUri($inputUri);
        $inputConfig = (new GlossaryInputConfig())
            ->setGcsSource($gcsSource);
        $glossary = (new Glossary())
            ->setName($formattedName)
            ->setLanguageCodesSet($languageCodesSet)
            ->setInputConfig($inputConfig);
//        $operationResponse = $translationServiceClient->createGlossary(
//            $formattedParent,
//            $glossary
//        );
        try {
            $operationResponse = $translationServiceClient->createGlossary(
                $formattedParent,
                $glossary
            );
            //$operationResponse->pollUntilComplete();
            if ($operationResponse->operationSucceeded()) {
                $response = $operationResponse->getResult();

                printf('Created Glossary.' . PHP_EOL);
                printf('Glossary name: %s' . PHP_EOL, $response->getName());
                printf('Entry count: %s' . PHP_EOL, $response->getEntryCount());
                printf(
                    'Input URI: %s' . PHP_EOL,
                    $response->getInputConfig()
                        ->getGcsSource()
                        ->getInputUri()
                );
            }
            else {
//                echo 'abc';exit();
                $error = $operationResponse->getError();
                // handleError($error)
            }
        }
        finally {
            $translationServiceClient->close();
        }
    }
    public function getlistGlossaries(){
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                printf('<br>');
                printf('Glossary name: %s' . PHP_EOL, $responseItem->getName());
                printf('Entry count: %s' . PHP_EOL, $responseItem->getEntryCount());
                printf(
                    'Input URI: %s' . PHP_EOL,
                    $responseItem->getInputConfig()
                        ->getGcsSource()
                        ->getInputUri()
                );
            }
        } finally {
            $translationServiceClient->close();
        }
    }
    public function getFirstGlossary(){
        $glossary_id = '';
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            $key = 0;
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                $key ++;
                if($key == 1){
                    $arr_name = explode('/',$responseItem->getName());
                    $glossary_id = $arr_name[count($arr_name)-1];
                }
            }
        } finally {
            $translationServiceClient->close();
        }
        return $glossary_id;
    }
    public function deletelistGlossaries(){
        $translationServiceClient = new TranslationServiceClient();
        $formattedParent = $translationServiceClient->locationName(
            $this->projectId,
            $this->locationForGlosary
        );
        try {
            $pagedResponse = $translationServiceClient->listGlossaries($formattedParent);
            foreach ($pagedResponse->iterateAllElements() as $responseItem) {
                $arr_name = explode('/',$responseItem->getName());
                $glossary_id = $arr_name[count($arr_name)-1];
                $this->deleteGlossary($glossary_id);
            }
        }
        finally {
            $translationServiceClient->close();
        }
    }
    public function deleteGlossary($glossaryId){
        $translationServiceClient = new TranslationServiceClient();
        $formattedName = $translationServiceClient->glossaryName(
            $this->projectId,
            $this->locationForGlosary,
            $glossaryId
        );
        try {
            $operationResponse = $translationServiceClient->deleteGlossary($formattedName);
            //$operationResponse->pollUntilComplete();
            if ($operationResponse->operationSucceeded()) {
                $response = $operationResponse->getResult();
                printf('Deleted Glossary.' . PHP_EOL);
            } else {
                $error = $operationResponse->getError();
            }
        } finally {
            $translationServiceClient->close();
        }
    }
}