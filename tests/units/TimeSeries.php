<?php

/**
 * PHP library to store time series analytics data into Redis
 *
 * (c) Arnaud Costes <arnaud.costes@gmail.com>
 *
 * MIT License
 */

namespace RedisAnalytics\tests\units;

use atoum;
use RedisAnalytics\TimeSeries as testedClass;

/**
 * Tests case of TimeSeries library
 *
 * @license MIT
 * @package RedisAnalytics
 * @author Arnaud Costes <arnaud.costes@gmail.com>
 */
class TimeSeries extends atoum
{
    public function testConnect()
    {
        $ts = new testedClass();
        $this->object($ts->connect())->isInstanceOf('Predis\Client');
        $this->exception(
            function () {
                $redis = new testedClass('127.0.0.1', 6378);
                $redis->connect();
            }
        )->isInstanceOf('\Exception');
    }

    public function testAdd()
    {
        $ts = new testedClass();
        $result = $ts->add('test', time(), rand());
        $this->variable($result)->isEqualTo(1);
    }

    public function testGet()
    {
        $ts = new testedClass();
        $key = 'test';
        $date = time();
        $ts->add($key, $date, rand());
        $result = $ts->get($key, $date, $date);
        $this->array($result)->size->isEqualTo(1);
        $this->array($result)->hasKey($date);
    }
    
    public function testRemove()
    {
        $ts = new testedClass();
        $key = 'test';
        $dates = [time(), time() + 1, time() + 2];
        array_map(function ($date) use ($ts, $key) { $ts->add($key, $date, rand()); }, $dates);
        $result = $ts->get($key, $dates[0], $dates[2]);
        $this->array($result)->size->isEqualTo(count($dates));
        array_map(function ($date) use ($result) { $this->array($result)->hasKey($date); }, $dates);
        $ts->remove($key, $dates[0], $dates[1]);
        $result = $ts->get($key, $dates[0], $dates[2]);
        $this->array($result)->size->isEqualTo(1);
        $this->array($result)->hasKey($dates[2]);
    }
}
