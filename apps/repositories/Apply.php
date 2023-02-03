<?php

namespace Score\Repositories;

use Score\Models\ScApply;
use Phalcon\Mvc\User\Component;

class Apply extends Component {

    public static function getByLimit($limit){
        return ScApply::find(array(
            "order"      => "apply_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
