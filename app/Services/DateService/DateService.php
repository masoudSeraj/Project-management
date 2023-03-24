<?php namespace App\Services\DateService;

use Illuminate\Support\Collection;

class DateService
{
    protected array $timestamps;
    protected $count = 3;
    protected $customKeys = ['start', 'paused', 'end'];
    public function randomTimeFromTimestamp(?int $count=null) :self
    {
        $timestamps = array_map(function($value){
            return rand(1661757940, 1773084574);
        }, $count ? range(1, $count) : range(1, $this->count));
        $this->timestamps = $timestamps;
        return $this;
    }
    public function getTimeStamps()
    {
        return $this->timestamps;
    }

    public function sortAsc()
    {
        $this->timestamps = collect($this->timestamps)->sort()->values()->toArray();
        return $this;
    }
    public function setCustomKeys(?array $customKeys=null) :Collection
    {
        return collect($customKeys ?? $this->customKeys)->combine($this->timestamps);
    }
}