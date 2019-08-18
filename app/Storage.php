<?php

namespace AppVal;

use Predis\Client;

class Storage {
    
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
        $this->client->zadd(Utils::getConfig('storage_key_leaderboard'), [$key => $value] );
    }
    
    
    /**
     * 
     * @param string $key
     */
    public function remove($key)
    {
        $this->client->zrem(Utils::getConfig('storage_key_leaderboard'), $key);
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
        $result = $this->client->zscore(Utils::getConfig('storage_key_leaderboard'), $key);
        return $result;
    }
    
    /**
     * Search in players database
     * 
     * @param int $page
     * @param int $limit
     * @param int $extendRange
     * @return array
     */
    public function search($page = 1, $limit = -1, $extendRange = 0)
    {
        if ($limit < 0) {
            $startIndex = 0;
            $endIndex = -1;
        } else {
            $startIndex = ($page - 1) * $limit;
            $endIndex = $startIndex + $limit - 1 + $extendRange;
        }

        $result = $this->client->zRevRange(
                Utils::getConfig('storage_key_leaderboard'),
                $startIndex,
                $endIndex,
                ['withscores' => true]);
        
        return $result;
    }
    
    /**
     * Set Application status
     * 
     * @param string $status
     */
    public function setApplicationStatus($status)
    {
        $this->client->set(Utils::getConfig('storage_key_app_status'), $status);
    }
    
    
    /**
     * Get current Application status
     * 
     */
    public function getApplicationStatus()
    {
        $result = $this->client->get(Utils::getConfig('storage_key_app_status'));
        return (is_numeric($result) ? (int)$result : null);
    }
    
    
    /**
     * Notify subscribers that Players Leaderboard has changed
     * 
     * @param string $status
     */
    public function notifyLeaderboardChanged($message)
    {
        if (!is_string($message)) {
            $message = json_encode($message);
        }
        $this->client->publish(Utils::getConfig('channel_leaderboard'), $message);
    }


    /**
     * Returns total count of items in leaderboard set
     * 
     */
    public function countItems() {
        return $this->client->zCount(Utils::getConfig('storage_key_leaderboard'), 0, PHP_INT_MAX);
    }


    /**
     * Returns data for $maxCount items, using pseudo random generated indexes
     * 
     * @param int $maxCount
     * @param int $minPlayerIndex
     * @param int $maxPlayerIndex
     */
    public function getRandomItems($maxCount, $minPlayerIndex, $manPlayerIndex) {

        $response = [];
        
        for ($i = 0; $i < $maxCount; $i++) {
            $index = rand($minPlayerIndex, $maxPlayerIndex);
            $result = $this->client->zrange(Utils::getConfig('storage_key_leaderboard'), $index, $index, ['withscores' => true]);
            if (!empty($result)) {
                foreach($result as $k => $v) {
                    $response[$k] = $v;
                }
            }
        }

        return $response;
    }
}