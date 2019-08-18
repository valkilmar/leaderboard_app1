<?php

namespace AppVal;

class App {
    
    const STATUS_APP_STOPPED = 1;
    const STATUS_APP_POLLING = 2;
    
    const ACTION_START = 'start';
    const ACTION_STOP = 'stop';
    const ACTION_RESET = 'reset';
    const ACTION_LEADERBORD = 'leaderboard';
    const ACTION_STATUS = 'status';
    const ACTION_TOTAL_COUNT = 'total-count';
    
    public function handeRequest()
    {
        $response = '';
        $action = trim(parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH), '/');

        switch ($action) {
            
            case self::ACTION_START:
                $page = (int)Utils::getValue($_GET, 'page', 1);
                $limit = (int)Utils::getValue($_GET, 'limit', -1);
                $this->startPolling($page, $limit);
                break;

            case self::ACTION_STOP:
                $this->stopPolling();
                break;

            case self::ACTION_RESET:
                $this->reset();
                break;

            case self::ACTION_STATUS:
                $response = json_encode(['status' => ($this->isPolling() ? 1 : 0) ]);
                break;

            case self::ACTION_TOTAL_COUNT:
                $response = json_encode(['total' => Player::getTotalCount() ]);
                break;
            
            case self::ACTION_LEADERBORD:
                $page = (int)Utils::getValue($_GET, 'page', 1);
                $limit = (int)Utils::getValue($_GET, 'limit', 10);

                $response = json_encode([
                    'leaderboard' => Player::getList($page, $limit, false),
                    'total' => Player::getTotalCount()
                ]);
                break;
            
            default:
                $response = json_encode([
                    'error' => 'Service action unsupported: ' . $action
                ]);
        }
        
        return $response;
    }
    
    
    /**
     * Start polling process
     * 
     * @param int $page
     * @param int $limit
     */
    private function startPolling($page = 1, $limit = -1)
    {
        /*
        if (Storage::getInstance()->getApplicationStatus() === self::STATUS_APP_POLLING) {
            return;
        }
        */
        
        Storage::getInstance()->setApplicationStatus(self::STATUS_APP_POLLING);
        
        $currentTimeLimit = (int)ini_get('max_execution_time');
        set_time_limit(0);

        while (Storage::getInstance()->getApplicationStatus() === self::STATUS_APP_POLLING) {
            Player::simulatePlaying($page, $limit);
            $delay = rand(Utils::getConfig('min_polling_delay'), Utils::getConfig('max_polling_delay'));
            sleep($delay);
        }
        

        set_time_limit($currentTimeLimit);
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
        Player::resetAll();
    }
    
    
    
    /**
     * Check polling status
     * 
     * @return boolean
     */
    private function isPolling()
    {
        return (Storage::getInstance()->getApplicationStatus() === self::STATUS_APP_POLLING);
    }
    
}