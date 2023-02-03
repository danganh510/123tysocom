<?php


namespace Bincg\Models;


use Phalcon\Mvc\Model;

class BaseModel extends Model
{
    public function initialize()
    {
        if ((stripos($_SERVER['REQUEST_URI'], '/cron/') === false) &&
        (stripos($_SERVER['REQUEST_URI'], '/dashboard/') === false)) {
            // write operation using db connection
            $this->setWriteConnectionService('db');
            // read operation using the connection dbSlave
            $this->setReadConnectionService('dbSlave');
        }
    }
}