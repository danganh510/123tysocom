<?php

namespace Bincg\Repositories;

use Bincg\Models\BinLocation;
use Phalcon\Mvc\User\Component;

class Location extends Component
{
    public static function checkCode($country_code,$language_code, $id)
    {
        return BinLocation::findFirst(
            array ('location_country_code = :COUNTRY: AND location_lang_code = :LANGUAGE: AND location_id != :ID:',
                'bind' => array('COUNTRY' => $country_code,
                    'LANGUAGE' => $language_code,
                    'ID' => $id),
            ));
    }

    public static function findAllLocationCountryCodes()
    {
        $countryCodes = array();

        $locations = BinLocation::find('location_active="Y"');
        foreach ($locations as $location) {
            if (!in_array($location->getLocationCountryCode(), $countryCodes)) {
                array_push($countryCodes, $location->getLocationCountryCode());
            }
        }

        return $countryCodes;
    }
    public static function findFirstLocationByCode($code)
    {
        return BinLocation::findFirst(array(
            'conditions' => 'location_active = "Y" AND location_country_code = :CODE:',
            'bind' => array("CODE" => $code),
            'order' => 'location_order'
        ));
    }

    public static function findFirstByCountryCodeAndLang($country_code, $language_code)
    {
        return BinLocation::findFirst(
            array('location_country_code = :COUNTRY: AND location_lang_code = :LANGUAGE: AND location_active = "Y"',
                'bind' => array(
                    'COUNTRY' =>  $country_code,
                    'LANGUAGE' => $language_code,
                )
            ));
    }
    public function findAllLocationDuplicate()
    {
        return BinLocation::find(array(
            'conditions' => 'location_active = "Y"',
            'order' => 'location_country_code'
        ));
    }
}