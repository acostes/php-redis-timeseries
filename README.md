# PHP Redis TimeSeries [![Build Status](https://travis-ci.org/acostes/php-redis-timeseries.png?branch=master)](https://travis-ci.org/acostes/php-redis-timeseries) #

PHP library to store time series analytics data into Redis

## Install ##

```
    composer require acostes/php-redis-timeseries
```

## Usage ##

```php
    use RedisAnalytics\TimeSeries;

    // You also can add parameters to the constructor to connect to your redis intance
    // __construct($host = '127.0.0.1', $port = '6379', $database = 0)
    $ts = new TimeSeries();

    // Add a new entry to you key at a specific timestamp
    $ts->add($myKey, $timestamp, $value);

    // Retrieve all data for a specific interval $from / $to
    $ts->get($myKey, $from, $to);
```

## Dependencies ##
- PHP >= 5.6
- Redis >= 2.6

## Author ##

- Arnaud Costes ([twitter](http://twitter.com/acostes))

## License ##

The code for Redistats is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).