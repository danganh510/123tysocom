<?php

namespace Bincg\Repositories;

use Phalcon\Mvc\User\Component;
use Bincg\Models\BinSuggest;

class Suggest extends Component
{
    const FILE_CACHED_CONFIG = "cached_suggest.txt";
    const FOLDER = "/../messages/";
    const SUG_NAME  = 'sug_name';
    const SUG_COUNT = 'sug_count';
    const SUG_LANG_CODE = 'sug_lang_code';
    const PERCENT = 'percent';
    function __construct()  {
        set_time_limit(0);
        ini_set('memory_limit', -1);
    }
    public static function getCache(){
        $sugArray = array();
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            $sugArray = unserialize(file_get_contents($cachedConfigFileName));
        }
        else {
            $list_suggest = BinSuggest::find("1 ORDER BY sug_count DESC");
            foreach($list_suggest as $suggest) {
                $item = array();
                $item[self::SUG_NAME] = $suggest->getSugName();
                $item[self::SUG_COUNT] = $suggest->getSugCount();
                $item[self::SUG_LANG_CODE] = $suggest->getSugLangCode();
                $sugArray[] = $item;
            }
            $folder =__DIR__.self::FOLDER;
            if (!is_dir($folder))  {
                mkdir($folder, 0777,TRUE);
            }
            file_put_contents($cachedConfigFileName, serialize($sugArray));
        }
        return $sugArray;
    }
    public static function deleteCache(){
        $cachedConfigFileName =  __DIR__.self::FOLDER.self::FILE_CACHED_CONFIG;
        if (file_exists($cachedConfigFileName)) {
            unlink($cachedConfigFileName);
        }
    }
    public static function update($name,$lang = 'en'){
        $name = str_replace("&quot;","",$name);
        $suggest = BinSuggest::findFirst(array(
            'conditions' => 'sug_name = :NAME: AND sug_lang_code =:CODE:',
            'bind'       => array('NAME' => $name, 'CODE' => $lang)
        ));
        if($suggest){
            $suggest->setSugCount($suggest->getSugCount() +1);
            $suggest->update();
        }else{
            $suggest = new BinSuggest();
            $suggest->setSugName($name);
            $suggest->setSugLangCode($lang);
            $suggest->save();
        }
        self::deleteCache();
    }
    public static function search($name,$limit,$lang = 'en'){
        $suggest = new Suggest();
        $result_temp = $suggest->get_similar($name,$limit,$lang);
        $result= array();
        foreach ($result_temp as $sug){
            $result[] = $sug[self::SUG_NAME];
        }
        return $result;
    }
    function get_similar($search,$limit,$lang)
    {
        $result = self::filterByLanguage($lang);
        $result_temp = array_filter($result, function ($item) use ($search) {
            if (stripos($item[self::SUG_NAME], $search) !== false){
                return true;
            }
            return false;
        });
        $result_sort = $this->array_sort($result_temp, self::SUG_COUNT, SORT_DESC);
        $result_search = array();
        $no=1;
        foreach ($result_sort as $item){
            $result_search[] = $item;
            $no++;
            if($no > $limit){
                break;
            }
        }
        return $result_search;
    }
    function filterByLanguage($lang){
        $list_suggest = self::getCache();
        $results = array_filter($list_suggest, function ($item) use ($lang) {
            if (stripos($item[self::SUG_LANG_CODE], $lang) !== false){
                return true;
            }
            return false;
        });
        return $results ;
    }
    function array_sort($array, $on, $order = SORT_ASC)
    {
        $new_array = array();
        $sortable_array = array();
        if (count($array) > 0) {
            foreach ($array as $k => $v) {
                if (is_array($v)) {
                    foreach ($v as $k2 => $v2) {
                        if ($k2 == $on) {
                            $sortable_array[$k] = $v2;
                        }
                    }
                }else {
                    $sortable_array[$k] = $v;
                }
            }
            switch ($order) {
                case SORT_ASC:
                    asort($sortable_array);
                    break;
                case SORT_DESC:
                    arsort($sortable_array);
                    break;
            }
            foreach ($sortable_array as $k => $v) {
                $new_array[$k] = $array[$k];
            }
        }
        return $new_array;
    }
}
