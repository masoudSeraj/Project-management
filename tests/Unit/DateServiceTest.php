<?php

namespace Tests\Unit;

use App\Services\DateService\DateService;
use PHPUnit\Framework\TestCase;

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
            ->randomTimeFromTimestamp()
            ->sortAsc()
            ->setCustomKeys();

        $value = ($timestamps['end'] > $timestamps['paused']) && ($timestamps['paused'] > $timestamps['start']);
        $this->assertTrue($value);
        $this->assertArrayHasKey('start', $timestamps);
        $timestamps->each(function($timestamp, $key){
            $this->assertNotNull($timestamp);
        });
    }
    public function test_create_2_random_with_condition_of_changed_at_deadline_at()
    {
        $timestamps = (new DateService)
        ->randomTimeFromTimestamp(2)
        ->sortAsc()
        ->setCustomKeys(['changed_at', 'deadline_at']);

        $value = ($timestamps['deadline_at'] > $timestamps['changed_at']);
        $this->assertTrue($value);
        $this->assertArrayHasKey('changed_at', $timestamps);
        $timestamps->each(function($timestamp, $key){
            $this->assertNotNull($timestamp);
        });
    }
}
