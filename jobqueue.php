<?php
require_once 'predis_0.7.0-dev.phar';

class Queue
{
    protected $name;
    protected $predis;

    public function __construct($name)
    {
        $this->name = $name;
        $this->predis = new Predis\Client();
    }

    public function push(Array $job)
    {
        $this->predis->lpush($this->name, serialize($job));
    }

    public function pop($block = false)
    {
        $job = null;
        if ($block) {
            $data = $this->predis->brpop($this->name, 0);
            $job = $data[1];
        } else {
            $job = $this->predis->rpop($this->name);
        }
        if ($job) {
            return unserialize($job);
        }
    }

}

$q = new Queue('test_queue');
for ($c = 0; $c < 1000; $c++) {
    $q->push(array("num" => rand()));
}

while ($job = $q->pop(false)) {
    echo "Processing job: " . $job["num"] . PHP_EOL;
}