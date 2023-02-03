<?php

namespace Bincg\Repositories;

use Bincg\Models\BinApply;
use Phalcon\Mvc\User\Component;

class Apply extends Component {

    public static function getByLimit($limit){
        return BinApply::find(array(
            "order"      => "apply_insert_time DESC",
            "limit"      => $limit,
        ));
    }
}
