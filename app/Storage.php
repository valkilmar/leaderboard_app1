<?php

namespace AppVal;

use Predis\Client;

class Storage {
    
    const KEY_APP_STATUS = 'app_status';
    const KEY_APP_STORAGE = 'leaderboard';
    
    // @var Storage
    private static $instance;
    
    // @var Predis\Client
    private $client;
    
    
    private function __construct()
    {
        $this->client = new Client(Utils::getConfig('redis'));
    }
    
    
    /**
     * 
     * @return Storage
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new static;
        }
        
        return self::$instance;
    }
    
    /**
     * 
     * @param string $key
     * @param int $value
     */
    public function store($key, $value)
    {
        $this->client->zadd(self::KEY_APP_STORAGE, [$key => $value] );
    }
    
    
    /**
     * 
     * @param string $key
     */
    public function remove($key)
    {
        $this->client->zrem(self::KEY_APP_STORAGE, $key);
    }
    
    
    /**
     * 
     */
    public function resetData()
    {
        $data = $this->search();
        
        if (!empty($data)) {
            foreach($data as $key => $value) {
                $this->remove($key);
            }
        }
    }
    
    
    /**
     * 
     * @param array $data
     * @param boolean $resetFirst
     */
    public function feedData($data, $resetFirst = true)
    {
        if ($resetFirst) {
            $this->resetData();
        }
        
        foreach($data as $key => $value) {
            $this->store($key, $value);
        }
    }


    /**
     * 
     * @param type $key
     * @return type
     */
    public function findByKey($key)
    {
        $result = $this->client->zscore(self::KEY_APP_STORAGE, $key);
        return $result;
    }
    
    /**
     * 
     * @param int $page
     * @param int $pageNum
     * @return array
     */
    public function search($page = 1, $pageNum = -1)
    {
        $startIndex = $page - 1;
        $endIndex = ($pageNum > 0) ? $startIndex + $pageNum : -1;
        $result = $this->client->zRevRange(
                self::KEY_APP_STORAGE,
                $startIndex,
                $endIndex,
                ['withscores' => true]);
        
        return $result;
    }
    
    
    public function setApplicationStatus($status)
    {
        $this->client->set(self::KEY_APP_STATUS, $status);
    }
    
    
    public function getApplicationStatus()
    {
        $result = $this->client->get(self::KEY_APP_STATUS);
        return (is_numeric($result) ? (int)$result : null);
    }
}