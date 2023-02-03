<?php
namespace Score\Repositories;
use Phalcon\Mvc\User\Component;

class AzureSearch extends Component
{
    const API_KEY = 'F613E39B2DB387599944AA197BA08E70';
    const BLOB_KEY = 'RbtBD6igyjKM7vViPb+rKPQne7vtamWqqfqxEoyKZitPeWfvTdFvIGS6VkZs5ZzUlJEk2AxNNZ0kWnwpUtOgbw==';
    const INDEX = 'bincgcom';
    const KEY_INDEX = 'ar_key_id';
    const DATASOURCES = 'bincgcom-data';
    const BLOB = 'bincgcom';
    const INDEXER = 'bincgcom-indexer';
    const STORAGE_ACCOUNTS = 'binholdings';
    const DATASOURCES_TYPE = 'azureblob';
    const API_VERSION = '2019-05-06';
    const URL = 'https://abc.search.windows.net';
    const PARSING_MODE = 'jsonArray';

    /**
     * @param string $url
     * @param array $data
     * @param string $method
     * @param boolean $jsonFormat
     * @return array
     */
    protected static function call($url, $data, $method, $jsonFormat = true)
    {
        $curl = curl_init($url);

        $httpHeader = array();
        if ($jsonFormat) {
            array_push($httpHeader, 'Content-Type: application/json');
        }
        array_push($httpHeader, 'api-key: ' . self::API_KEY);

        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);

        if ($method == 'POST' || $method == 'PUT') {
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($data));
        }

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $httpHeader);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $str = json_decode(curl_exec($curl),true);
        $result = array();
        $status = 'fail';
        $message = '';
        if (isset($str['error']['message']))
        {
            $message = $str['error']['message'];
        }
        if (isset($str['Message']))
        {
            $message = $str['Message'];
        }
        if(empty($message)){
            $result = $str;
            $status = 'success';
            if (isset($str['value']))
            {
                $results = array_filter($str['value'], function ($item) {
                    if (!empty($item['errorMessage'])) {
                        return true;
                    }
                    return false;
                });
                if (!empty($results)) {
                    $message = array_shift($results)['errorMessage'];
                    $status = 'fail';
                }
            }
        }
        curl_close($curl);

        return array('result' => $result,
                    'status' => $status,
                    'message' => $message);
    }

    /**
     * @param string $nameIndexes
     * @param string $keyAzureDocs
     * @param array $dataIndexes
     * @return array
     */
    protected static function callCreateIndexes($nameIndexes, $keyAzureDocs, $dataIndexes)
    {
        $fields = array();
        foreach ($dataIndexes as $key)
        {
            foreach ($key as $arr)
            {
                $item['searchable'] = true;
                $item['sortable'] = true;
                $item['retrievable'] = true;
                $item['facetable'] = true;
                $item['filterable'] = true;

                $item['name'] = $arr['name'];
                switch ($arr['type'])
                {
                    case 'Edm.String':
                        $item['type'] = 'Edm.String';
                        $item['analyzer'] = 'vi.microsoft';
                        break;
                    case 'Collection(Edm.String)':
                        $item['type'] = 'Collection(Edm.String)';
                        $item['analyzer'] = 'vi.microsoft';
                        $item['sortable'] = false;
                        break;
                    case 'Edm.Double':
                        $item['type'] = 'Edm.Double';
                        $item['searchable'] = false;
                        unset($item['analyzer']);
                        break;
                    case 'Edm.Boolean':
                        $item['type'] = 'Edm.Boolean';
                        $item['searchable'] = false;
                        unset($item['analyzer']);
                        break;
                    default:
                        $item['type'] = 'Edm.Int32';
                        $item['searchable'] = false;
                        unset($item['analyzer']);
                        break;
                }

                if($arr['name'] == $keyAzureDocs)
                {
                    $item['key'] = true;
                    unset($item['analyzer']);
                }
                else {
                    unset($item['key']);
                }
                $fields[] = $item;
            }
        }

        $data = array(
            'name' => $nameIndexes,
            'fields' => $fields
        );
        return $data;
    }

    public static function getAzureSearch($key_word,$lang_code,$type_id, $current_page, $limit)
    {
        $start=($current_page-1)*$limit;
        $key_words = str_replace(" ","+",$key_word);
        $search_indexes = self::URL."/indexes/".self::INDEX."/docs?api-version=".self::API_VERSION."&search=$key_words&searchMode=all&\$count=true&\$top=$limit&\$skip=$start&\$filter=lang+eq+'$lang_code'";

        if(!empty($type_id)) {
            $search_indexes .= "+and+ar_type_id+eq+'$type_id'";
        }

        $response = self::call($search_indexes, array(), 'GET',true);
        return $response;
    }

    public static function createDatasources()
    {
        $data = array (
            'name' => self::DATASOURCES,
            'type' => self::DATASOURCES_TYPE,
            'credentials' =>
                array (
                    'connectionString' => 'DefaultEndpointsProtocol=https;AccountName='.self::STORAGE_ACCOUNTS.';AccountKey='.self::BLOB_KEY,
                ),
            'container' =>
                array (
                    'name' => self::BLOB
                ),
        );

        return self::call(self::URL.'/datasources?api-version='.self::API_VERSION, $data, 'POST',true);
    }

    public static function deleteDatasources()
    {
        return self::call(self::URL.'/datasources/'.self::DATASOURCES.'?api-version='.self::API_VERSION, null,'DELETE');
    }

    public static function createIndexes()
    {
        $nameIndexes = self::INDEX;
        $keyAzureDocs = self::KEY_INDEX;
        $dataIndexes = array (
            'fields' =>
                array (
                    0 =>
                        array (
                            'name' => SearchAzure::AR_TYPE,
                            'type' => 'Edm.String'
                        ),
                    1 =>
                        array (
                            'name' => SearchAzure::AR_URL,
                            'type' => 'Edm.String'
                        ),
                    2 =>
                        array (
                            'name' => SearchAzure::AR_NAME,
                            'type' => 'Edm.String'
                        ),
                    3 =>
                        array (
                            'name' => SearchAzure::AR_TITLE,
                            'type' => 'Edm.String'
                        ),
                    4 =>
                        array (
                            'name' => SearchAzure::AR_META_KEYWORD,
                            'type' => 'Edm.String'
                        ),
                    5 =>
                        array (
                            'name' => SearchAzure::AR_META_DESCRIPTION,
                            'type' => 'Edm.String'
                        ),
                    6 =>
                        array (
                            'name' => SearchAzure::AR_CONTENT,
                            'type' => 'Edm.String'
                        ),
                    7 =>
                        array (
                            'name' => SearchAzure::AR_KEY_ID,
                            'type' => 'Edm.String'
                        ),
                    8 =>
                        array (
                            'name' => SearchAzure::LANG,
                            'type' => 'Edm.String'
                        )
                )
        );

        $data = self::callCreateIndexes($nameIndexes, $keyAzureDocs, $dataIndexes);

        return self::call(self::URL.'/indexes?api-version='.self::API_VERSION, $data, 'POST',true);
    }

    public static function deleteIndexes()
    {
        return self::call(self::URL.'/indexes/'.self::INDEX.'?api-version='.self::API_VERSION, null,'DELETE');
    }

    /**
     * @param array $valueData
     * @return array
     */
    public static function updateIndexesDocs($valueData)
    {
        $data = array ('value' => $valueData);
        return self::call(self::URL.'/indexes/'.self::INDEX.'/docs/index?api-version='.self::API_VERSION, $data,'POST',true);
    }

    /**
     * @param array $id
     * @return array
     */
    public static function deleteIndexesDocs($id)
    {
        $data = array();
        foreach ($id as $multiId) {
            $data[] = array(
                '@search.action' => 'delete',
                self::KEY_INDEX => $multiId,
            );
        }
        $data_delete = array(
            'value' => $data
        );
        return self::call(self::URL.'/indexes/'.self::INDEX.'/docs/index?api-version='.self::API_VERSION, $data_delete,'POST',true);
    }

    public static function createIndexers()
    {
        $data = array (
            'name' => self::INDEXER,
            'dataSourceName' => self::DATASOURCES,
            'targetIndexName' => self::INDEX,
            'parameters' =>
                array (
                    'configuration' =>
                        array (
                            'parsingMode' => self::PARSING_MODE,
                        ),
                ),
        );

        return self::call(self::URL.'/indexers?api-version='.self::API_VERSION, $data, 'POST',true);
    }

    public static function deleteIndexers()
    {
        return self::call(self::URL.'/indexers/'.self::INDEXER.'?api-version='.self::API_VERSION, null,'DELETE');
    }
}

