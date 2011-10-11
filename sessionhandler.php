<?php
class SessionHandler
{
    protected $_set = 'PHPSESSIONS:';
    protected $_lifetime = 3600;
    protected $_redis;

    public function open($save_path, $name)
    {
        return true;
    }

    public function close()
    {
        return true;
    }

    public function read($id)
    {
        return $this->_redis->get($this->_getKeyName($id));
    }

    public function write($id, $data)
    {
        //O(log(N))
        $timestamp = time();
        $this->_redis->zadd($this->_set, $timestamp, $id);
        
        //O(1)
        return $this->_redis->setex($this->_getKeyName($id), $this->_lifetime, $data);
    }

    public function destroy($id)
    {
        //O(log(N))
        $this->_redis->zrem($this->_set, $id);
        return $this->_redis->del($this->_getKeyName($id));
    }

    public function gc($maxlifetime)
    {
        // O(log(N))
        return $this->_redis->zremrangebyscore($this->_set, 0, time() - $maxlifetime);
    }

    protected function _getKeyName($id)
    {
        return 'session:' . $id;
    }
}