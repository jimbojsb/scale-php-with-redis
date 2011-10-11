<?php
class Item
{
    public function hasFreeShipping()
    {
        return $this->redis->sIsMember('item:45:attributes', 'free shipping');
    }

    public function addAttribute($name, $value)
    {
        //traditional mysql stuff here still
        $this->redis->sadd('item:45:attributes', 'free shipping');
    }
}
