<?php
namespace Score\Utils;
use Phalcon\Mvc\User\Component;

class NumVerify extends Component {

    static $access_key = '916a97be91e1ddf42ce88f157a3f8bb0';
    static $api = "https://apilayer.net/api/validate";
    static $options = array(
        CURLOPT_RETURNTRANSFER => true,     // return web page
        CURLOPT_SSL_VERIFYPEER => false     // Disabled SSL Cert checks
    );

    public $valid, $number, $local_format, $international_format, $country_prefix, $country_code, $country_name, $location, $carrier, $line_type;

    public static function info($phone_number){
        $phone_number = trim($phone_number);
        $result = new static;
        if(strlen($phone_number) > 0){

            $url = self::$api. '?access_key='. self::$access_key. '&number='. str_replace (' ', '', $phone_number.'').'&format=0';

            $ch = curl_init($url);
            curl_setopt_array( $ch, self::$options);

            // Store the data:
            $json = curl_exec($ch);
            curl_close($ch);

            $data = json_decode($json);

            if ($data) {
                $result->status = "true";
                $result->message = 'Success';
                foreach($data as $key => $val) {
                    $result->$key = $val;
                }
            }
            else {
                //data is null
                $result->status = "fail";
                $result->message = 'Cannot connect to numverify server!';
            }
        }else{
            //phone num is empty
            $result->status = "fail";
            $result->message = 'Phone number is empty!';
        }
        return $result;
    }

    /**
     * @return string[]
     */
    public static function getDbKeys() {
        return array('valid', 'number', 'local_format', 'international_format', 'country_prefix', 'country_code', 'country_name', 'location', 'carrier', 'line_type');
    }
}