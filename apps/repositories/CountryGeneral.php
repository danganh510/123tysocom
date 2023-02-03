<?php

namespace Score\Repositories;

use General\Models\Country;
use Phalcon\Mvc\User\Component;

class CountryGeneral extends Component
{
    public static function findAll()
    {
        return Country::find(array(
            'country_active = "Y" ',
            "order" => "country_name ASC",
        ));
    }
    public static function getCountryCombobox($code)
    {
        $country = self::findAll();
        $output = '';
        foreach ($country as $value)
        {
            $selected = '';
            if($value->getCountryIsoAlpha2() == $code)
            {
                $selected = 'selected';
            }
            $output.= "<option ".$selected." value='".$value->getCountryIsoAlpha2()."'>".$value->getCountryName()."</option>";

        }
        return $output;
    }
    public static function getNameByCode($code)
    {
        $result = Country::findFirst(array(
            'country_iso_alpha2 = :code:  AND country_active= "Y"',
            'bind'      => array('code' => $code),
        ));
        return $result ? $result->getCountryName() : '';
    }

}
