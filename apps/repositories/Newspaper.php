<?php

namespace Score\Repositories;

use Score\Models\ScNewspaper;
use Phalcon\Mvc\User\Component;
class Newspaper extends Component
{
    public static function checkKeyword($newspaper_keyword,$newspaper_id) {
        return ScNewspaper::findFirst(
            [
                'newspaper_keyword = :keyword: AND newspaper_id != :newspaperid:',
                'bind' => [
                    'keyword' => $newspaper_keyword,
                    'newspaperid' => $newspaper_id,
                ],
            ]
        );
    }
    public static function findFirstById($id)
    {
        return ScNewspaper::findFirst(array(
            'newspaper_id = :id:',
            'bind' => array('id' => $id)
        ));
    }

    public static function getNewspaperCombobox($id)
    {
        $newspaper = ScNewspaper::find();
        $output = '';
        foreach ($newspaper as $value)
        {
            $selected = '';
            if($value->getNewspaperId() == $id)
            {
                $selected = 'selected';
            }
            $output.= "<option ".$selected." value='".$value->getNewspaperId()."'>".$value->getNewspaperName()."</option>";
        }
        return $output;
    }

    public static function getNameByID($id)
    {
        $result = self::findFirstById($id);
        return $result ? $result->getNewspaperName() : '';
    }

}