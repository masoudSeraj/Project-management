<?php

namespace Tests\Unit;

use Carbon\Carbon;
use PHPUnit\Framework\TestCase;
use App\Services\DateService\DateService;

class DateServiceTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_create_3_random_timestamp_with_the_condition_of_start_end_between_works()
    {
        $timestamps = (new DateService)
            ->randomTimestamp();

        $value = ($timestamps['end'] > $timestamps['paused']) && ($timestamps['paused'] > $timestamps['start']);
        $this->assertTrue($value);
        $this->assertArrayHasKey('start', $timestamps);
        $timestamps->each(function($timestamp, $key){
            $this->assertNotNull($timestamp);
        });
    }
    public function test_create_2_random_with_condition_of_changed_at_deadline_at()
    {
        DateService::setCustomKeys(['changed_at', 'deadline_at']);
        $timestamps = (new DateService)
            ->randomTimestamp(2);

        $value = ($timestamps['deadline_at'] > $timestamps['changed_at']);
        $this->assertTrue($value);
        $this->assertArrayHasKey('changed_at', $timestamps);
        $timestamps->each(function($timestamp, $key){
            $this->assertNotNull($timestamp);
        });
    }

    public function test_create_3_random_nullable_Dates_with_the_condition_of_start_end_between_works()
    {
        for($i=1; $i<=100; $i++){
            $dates = (new DateService)
                ->getRandomDatetimeString();

            if(isset($dates['start'])){
                $this->assertNotNull($dates['start']);
                $this->assertTrue(Carbon::create($dates['start'])->lessThan(Carbon::create($dates['end'])));
            }else{
                $this->assertNull($dates['start']);
            }

            if(isset($dates['paused'])){
                $this->assertNotNull($dates['paused']);
                $this->assertTrue(Carbon::create($dates['paused'])->lessThan(Carbon::create($dates['end'])));
            }else{
                $this->assertNull($dates['paused']);
            }

            if(isset($dates['start']) && isset($dates['paused'])){
                $this->assertTrue(Carbon::create($dates['start'])->lessThan(Carbon::create($dates['paused'])));
            }

            $this->assertNotNull($dates['end']);
        }
    }
}
