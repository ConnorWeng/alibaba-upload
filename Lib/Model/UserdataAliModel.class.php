<?php

class UserdataAliModel extends Model {

    protected $tableName = 'userdata_ali';

    public function uploadCount($nick, $ip) {
        $where['nick'] = $nick;
        $where['ip'] = $ip;
        if (!$this->where($where)->setInc('upCnt')) {
            $this->add($where);
            $this->where($where)->setInc('upCnt');
        }
    }

}

?>