<?php

namespace Score\Repositories;

use Score\Models\ScCareerOffice;
use Score\Models\ScOffice;
use Phalcon\Mvc\User\Component;

class CareerOffice extends Component {

    public static function getOfficesByCareer($career_id)
    {
        $list_office = ScCareerOffice::find(array(
            'co_career_id =:career_id:',
            'bind'=>['career_id' => $career_id])
        );
        $offices = array();
        foreach ($list_office as $value)
        {
            $offices[] = $value->getCoOfficeId();

        }
        return $offices;
    }
    public static function getNameOfficesByCareer($career_id)
    {
        $office_ids = self::getOfficesByCareer($career_id);
        $name_offices = array();
        foreach ($office_ids as $office_id)
        {
            $office = ScOffice::findFirstById($office_id);
            if($office) {
                $name_offices[] = $office->getOfficeName();
            }
        }
        return implode(', ',$name_offices);
    }
    public static function deleteByCareer($career_id)
    {
        $list_office = ScCareerOffice::find(array(
                'co_career_id =:career_id:',
                'bind'=>['career_id' => $career_id])
        );
        foreach ($list_office as $value)
        {
            $value->delete();
        }
    }
}
