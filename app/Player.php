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
        if ($this->score < 0) {
            $this->score = 0;
        }
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
     * Get total count of players
     * 
     */
    public static function getTotalCount()
    {
        /* @var $storage Storage */
        $storage = Storage::getInstance();
        return $storage->countItems();
    }
    
    
    /**
     * Get set of players
     * 
     * @param int $page
     * @param int $limit 
     * @param boolean $returnObjects
     * @param int $extendRange 
     * @return array
     */
    public static function getList($page = 1, $limit = -1, $returnObjects = true, $extendRange = 0)
    {
        $response = [];
        
        /* @var $storage Storage */
        $storage = Storage::getInstance();
        $data = $storage->search($page, $limit, $extendRange);
        
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


    /**
     * Simulate player activity, e.g. accumulate scores to random number of players
     *
     * @param int $page
     * @param int $limit
     */
    public static function simulatePlaying($page = 1, $limit = -1)
    {
        if ($limit <= 0) {
            $limit = rand(Utils::getConfig('player_update_min_count'), Utils::getConfig('player_update_max_count'));
        }
        
        $scores = Randomizer::getIntegers($limit, Utils::getConfig('player_update_min_value'), Utils::getConfig('player_update_max_value'), false);
        // $scores = Randomizer::getIntegersLocal($limit, Utils::getConfig('player_update_min_value'), Utils::getConfig('player_update_max_value'), false);

        $updated = [];

        // Continuous set of players
        $players = self::getList($page, $limit, true, 5);

        $index = 0;
        $totalScores = count($scores);
        
        foreach($players as $player) {
            
            $tobeOrNot = rand(1, 3);
            if ($tobeOrNot > 2) {
                continue;
            }

            if ($index === $limit) {
                break;
            }
            
            $scoreIndex = rand(0, $totalScores - 1);
            $scoreToAdd = $scores[$scoreIndex];

            if ($scoreToAdd === 0) {
                continue;
            }

            $player->updateScore($scoreToAdd);
            $player->save();
            $updated[$player->getUsername()] = $player->getScore(); 
            
            $index++;
        }

        if (!empty($updated)) {
            $event = [
                'type' => 'update',
                'updated' => $updated
            ];
            
            Storage::getInstance()->notifyLeaderboardChanged($event);
        }
    }


    /**
     * Reset player scores
     * 
     */
    public static function resetAll()
    {
        $names = Utils::getConfig('names');
        $names = explode('|', $names);
        $players = [];
        foreach($names as $name) {
            $players[$name] = 0;
        }

        Storage::getInstance()->feedData($players, true);

        $event = [
            'type' => 'reset'
        ];
        
        Storage::getInstance()->notifyLeaderboardChanged($event);
    }
}