<?php

/**
 * PHP library to store time series analytics data into Redis
 *
 * (c) Arnaud Costes <arnaud.costes@gmail.com>
 *
 * MIT License
 */

namespace RedisAnalytics;

use Predis;

/**
 * TimeSerie class used for storing and getting Time Series data from redis
 *
 * @license MIT
 * @package RedisAnalytics
 * @author Arnaud Costes <arnaud.costes@gmail.com>
 */
class TimeSeries
{
    /**
     * Predis\Client
     */
    protected $client;

    /**
    * Constructor
    *
    * @param string $host
    * @param int $port
    * @param int $database
    */
    public function __construct($host = '127.0.0.1', $port = '6379', $database = 0)
    {
        $this->client = new Predis\Client(
            [
                'scheme'    => 'tcp',
                'host'      => $host,
                'port'      => $port,
                'database'  => $database,
            ]
        );
    }

    /**
     * Force connection to the redis server
     *
     * @return Predis\Client
     */
    public function connect()
    {
        try {
            if (!$this->client->isConnected()) {
                $this->client->connect();
            }
            return $this->client;
        } catch (Predis\PredisException $e) {
            throw new \Exception('Unable to connect to ' . $this->client->getConnection());
        }
    }

    /**
     * Add a $value for a specific $date
     *
     * @param string $key
     * @param int $date (timestamp)
     * @param float $value
     *
     * @return int number of elements added
     */
    public function add($key, $date, $value)
    {
        // Because the member of a sorted set is unique, add the date to the value
        $storedValue = "$value:$date";

        return $this->client->zAdd($key, $date, $storedValue);
    }

    /**
     * Get all the stored analytics for a specific interval
     *
     * @param string $key
     * @param int $from (timestamp)
     * @param int $to (timestamp)
     *
     * @return array
     */
    public function get($key, $from, $to)
    {
        $data = $this->client->zrangeByScore($key, $from, $to, 'WITHSCORES');
        $result = [];

        foreach ($data as $member => $score) {
            list($value) = explode(':', $member);
            $result[$score] = $value;
        }
        return $result;
    }
    
    /**
     * Removes all the stored analytics for a specific interval
     *
     * @param string $key
     * @param int $from (timestamp)
     * @param int $to (timestamp)
     *
     * @return void
     */
    public function remove($key, $from, $to)
    {
        $this->client->zremrangebyscore($key, $from, $to);
    }
}
