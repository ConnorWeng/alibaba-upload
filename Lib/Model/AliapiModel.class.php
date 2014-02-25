<?php

class AliapiModel extends ApiModel {

    protected $tableName = 'aliapi';
    private $apiCount = 4;

    // Override
    protected function getFirstId($taobaoItemId) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = $id % $this->apiCount;
        return $id;
    }

}

?>