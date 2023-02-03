<?php

namespace Bincg\Repositories;

use Bincg\Models\BinUserAgent;
use Phalcon\Mvc\User\Component;

class UserAgent extends Component
{
    public static function getFirstUserAgentById($agent_id) {
        return BinUserAgent::findFirst(array(
            'agent_id = :agent_id:',
            'bind' => array('agent_id' => $agent_id)
        ));
    }
}


