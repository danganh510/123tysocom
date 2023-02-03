<?php

namespace Score\Repositories;

use Score\Models\ScContactus;
use Phalcon\Mvc\User\Component;

class ContactUs extends Component {
    public static function getByID($contact_id) {
        return ScContactus::findFirst(array(
            'contact_id = :contact_id:',
            'bind' => array('contact_id' => $contact_id)
        ));
    }
    public static function getByLimit($limit){
        return ScContactus::find(array(
            "order"      => "contact_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
