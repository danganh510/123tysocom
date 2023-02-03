<?php

namespace Bincg\Repositories;

use Bincg\Models\BinContactus;
use Phalcon\Mvc\User\Component;

class ContactUs extends Component {
    public static function getByID($contact_id) {
        return BinContactus::findFirst(array(
            'contact_id = :contact_id:',
            'bind' => array('contact_id' => $contact_id)
        ));
    }
    public static function getByLimit($limit){
        return BinContactus::find(array(
            "order"      => "contact_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
