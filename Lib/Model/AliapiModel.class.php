<?php

class AliapiModel extends ApiModel {

    private $apiCount = 4;

    protected function getFirstId($taobaoItemId) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = $id % $apiCount;
        return $id;
    }

}

?>