<?php

namespace AppVal;

use AppVal\Utils;

class Randomizer {
    const API_URL_INVOKE = 'https://api.random.org/json-rpc/2/invoke';
    
    private static $requestId = 1;
    
    /**
     * 
     * @param int $countNumbers
     * @param int $minValue
     * @param int $maxValue
     * @param boolean $uniqueValues
     * @return array|null
     */
    public static function getIntegers($countNumbers, $minValue, $maxValue, $uniqueValues = true)
    {
        
        $result = self::apiRequest('generateIntegers', [
            "n" => $countNumbers,
            "min" => $minValue,
            "max" =>  $maxValue,
            "replacement" => !$uniqueValues,
            "base" => 10
        ]);
        
        
        if (empty($result)) {
            return null;
        }
        
        $result = json_decode($result, true);
        
        return Utils::getValue($result, 'result.random.data');
    }
    
    
    /**
     * 
     * @param string $method
     * @param array $params
     * @return string
     */
    private static function apiRequest($method, $params)
    {
        $params['apiKey'] = Utils::getConfig('api_key_randomizer');
        
        $message = json_encode([
            "jsonrpc" => "2.0",
            'id' => self::$requestId++,
            'method' => $method,
            'params' => $params
        ]);

        $requestHeaders = [
            'Content-Type: application/json',
            'Content-Length: ' . strlen($message),
            'Content-Encoding: ' . 'gzip'
        ];

        $ch = curl_init(self::API_URL_INVOKE);
        
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $message);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $requestHeaders);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $result = curl_exec($ch);
    
        curl_close($ch);
        
        return $result;
    }


    public static function getIntegersLocal($countNumbers, $minValue, $maxValue, $uniqueValues = true)
    {
        $response = [];
        
        while ((count($response) < $countNumbers)) {
            $number = rand($minValue, $maxValue);
            if ($uniqueValues && in_array($number, $response)) {
                continue;
            }
            $response[] = $number;
        }

        return $response;
    }
}