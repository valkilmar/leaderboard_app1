<?php

namespace AppVal;

use AppVal\Storage;

class Player {
    
    private $username;
    
    private $score = 0;
    
    /**
     * 
     * @param string $username
     * @param int $score
     */
    public function __construct($username, $score)
    {
        $this->username = $username;
        $this->score = $score;
    }
    
    /**
     * 
     * @param int $score
     */
    public function updateScore($score)
    {
        $this->score += $score;
    }
    
    /**
     * 
     * @return string
     */
    public function getUsername()
    {
        return $this->username;
    }
    
    /**
     * 
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }
    
    /**
     * 
     */
    public function save()
    {
        /* @var $storage Storage */
        $storage = Storage::getInstance();
        $storage->store($this->username, $this->score);
    }
    
    
    /**
     * 
     * @param boolean $returnObjects
     * @return array
     */
    public static function getAll($returnObjects = true)
    {
        $response = [];
        
        /* @var $storage Storage */
        $storage = Storage::getInstance();
        $data = $storage->search();
        
        if (!empty($data)) {
            foreach($data as $key => $value) {
                if ($returnObjects) {
                    $player = new static($key, (int)$value);
                    $response[] = $player;
                } else {
                    $response[$key] = (int)$value;
                }
            }
        }
        
        return $response;
    }
}