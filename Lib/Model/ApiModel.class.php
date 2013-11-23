<?php

class ApiModel extends Model {

    public function getAppKey($taobaoItemId, $oldAppKey = null) {
        if ($oldAppKey == null) {
            $where['id'] = $this->getFirstId($taobaoItemId);
            $rs = $this->where($where)->select();
        } else {
            $sql = 'SELECT * from '.C('DB_PREFIX').$this->tableName.' where overflow = 0 ORDER BY RAND() LIMIT 1';
            $rs = $this->query($sql);
        }

        if (count($rs) > 0) {
            return array('appkey' => $rs[0]['appkey'],
                         'appsecret' => $rs[0]['appscret'],
                         'id' => $rs[0]['id']);
        } else {
            return array('appkey' => C('taobao_app_key'),
                         'appsecret' => C('taobao_secret_key'),
                         'id' => '');
        }
    }

    public function appKeyFail($id) {
        $where['id'] = $id;
        $this->where($where)->setInc('overflow');
    }

    public function appKeySuccess($id) {
        $where['id'] = $id;
        $this->where($where)->setField('overflow', '0');
    }

    protected function getFirstId($taobaoItemId) {
        $len = strlen($taobaoItemId);
        $id = substr($taobaoItemId, $len-2, $len);
        $id = floor(($id) / 5);
        return $id;
    }

}

?>