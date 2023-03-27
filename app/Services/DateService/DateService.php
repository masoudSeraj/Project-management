<?php namespace App\Services\DateService;

use Carbon\Carbon;
use Illuminate\Support\Collection;

class DateService
{
    protected array $timestamps;
    protected $count = 3;
    protected static $sort = 'ASC';
    protected static array $customKeys = ['start', 'paused', 'end'];
    protected static array $nullableDates = ['start', 'paused'];
    public function randomTimestamp(?int $count=null)
    {
        $timestamps = array_map(function($value){
            return rand(1661757940, 1973084574);
        }, $count ? range(1, $count) : range(1, $this->count));

        $this->timestamps = $this->sort($timestamps);

        return $this->combine();
    }
    public function getRandomDatetimeString(?int $count=null) :Collection
    {
        $nullables = self::$nullableDates;
        return $this->randomTimestamp($count)
            ->map(function($timestamp, $key) use($nullables){
                return in_array($key, $nullables) ? $this->getNullableDate($timestamp) : $this->getDate($timestamp);
            });
    }
    
    protected function getDate($timestamp)
    {
        return Carbon::createFromTimestamp($timestamp)->toDateTimeString();
    }

    protected function getNullableDate($timestamp)
    {
        return rand(0, 1) ? $this->getDate($timestamp) : null;
    }

    public function getTimeStamps()
    {
        return $this->timestamps;
    }
    public static function setCustomKeys(array $customKeys)
    {
        self::$customKeys = $customKeys;
    }
    public function combine() :Collection
    {
        return collect(self::$customKeys)->combine($this->timestamps);
    }

    public function sort($timestamps) :array
    {
        return $this->doSort($timestamps)->values()->toArray();
    }

    protected function doSort($timestamps) :Collection
    {
        return self::$sort === 'ASC' ? collect($timestamps)->sort() : collect($timestamps)->sortByDesc(null);
    }


    public static function setNullableDates(array $dates)
    {
        self::$nullableDates = $dates;
    }

}