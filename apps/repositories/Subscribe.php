<?php

namespace Bincg\Repositories;

use Bincg\Models\BinSubscribe;
use Phalcon\Mvc\User\Component;

class Subscribe extends Component {
    public static function getByID($id) {
        return BinSubscribe::findFirst(array(
            'subscribe_id = :id:',
            'bind' => array('id' => $id)
        ));
    }
    public static function getByLimit($limit){
        return BinSubscribe::find(array(
            "order"      => "subscribe_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
