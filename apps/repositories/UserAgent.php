<?php

namespace Score\Repositories;

use Score\Models\ScUserAgent;
use Phalcon\Mvc\User\Component;

class UserAgent extends Component
{
    public static function getFirstUserAgentById($agent_id) {
        return ScUserAgent::findFirst(array(
            'agent_id = :agent_id:',
            'bind' => array('agent_id' => $agent_id)
        ));
    }
}


