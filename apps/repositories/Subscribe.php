<?php

namespace Score\Repositories;

use Score\Models\ScSubscribe;
use Phalcon\Mvc\User\Component;

class Subscribe extends Component {
    public static function getByID($id) {
        return ScSubscribe::findFirst(array(
            'subscribe_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
    public static function getByLimit($limit){
        return ScSubscribe::find(array(
            "order"      => "subscribe_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
