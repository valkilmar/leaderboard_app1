<?php

namespace AppVal;

class App {
    
    const STATUS_APP_STOPPED = 1;
    const STATUS_APP_POLLING = 2;
    
    const ACTION_START = 'start';
    const ACTION_STOP = 'stop';
    const ACTION_RESET = 'reset';
    const ACTION_LEADERBORD = 'leaderboard';
    
    public function handeRequest()
    {
        $response = '';
        $request = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        $action = end($request);

        switch ($action) {
            case self::ACTION_START:
                $this->startPolling();
                $response = json_encode(['leaderboard' => $this->getPlayers(false)]);
                break;

            case self::ACTION_STOP:
                $this->stopPolling();
                $response = json_encode(['leaderboard' => $this->getPlayers(false)]);
                break;

            case self::ACTION_RESET:
                $this->reset();
                $response = json_encode(['leaderboard' => $this->getPlayers(false)]);
                break;
            
            case self::ACTION_LEADERBORD:
                $response = json_encode(['leaderboard' => $this->getPlayers(false)]);
                break;
            
            default:
                $response = json_encode(['error' => 'Service action unsupported: ' . $action]);
        }
        
        return $response;
    }
    
    
    /**
     * Start polling process
     * 
     */
    private function startPolling()
    {
        Storage::getInstance()->setApplicationStatus(self::STATUS_APP_POLLING);
        $this->updatePlayerScores();
    }
    
    /**
     * Stop polling process
     * 
     */
    private function stopPolling()
    {
        Storage::getInstance()->setApplicationStatus(self::STATUS_APP_STOPPED);
    }
    
    
    
    
    /**
     * Stop polling process if any and reset player scores
     * 
     */
    private function reset()
    {
        Storage::getInstance()->setApplicationStatus(self::STATUS_APP_STOPPED);
        Storage::getInstance()->feedData(Utils::getConfig('players'), true);
    }
    
    
    /**
     * Accumulate players scores with new data, received by the Randomizer
     * 
     */
    private function updatePlayerScores()
    {
        $players = Player::getAll();
        
        $totalPlayers = count($players);
        
        $scores = Randomizer::getIntegers($totalPlayers, 0, Utils::getConfig('player_update_max_value'), false);
        
        foreach($players as $index => $player) {
            if (isset($scores[$index]) && is_numeric($scores[$index])) {
                $player->updateScore($scores[$index]);
                $player->save();
            }
        }
    }
    
    
    /**
     * 
     * @param boolean $returnObjects
     * @return array
     */
    public function getPlayers($returnObjects = true)
    {
        return Player::getAll($returnObjects);
    }
    
    
    
    
}