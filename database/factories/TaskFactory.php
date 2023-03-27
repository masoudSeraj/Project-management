<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Models\Project;
use App\Services\DateService\DateService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Task>
 */
class TaskFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {   
        $timestamps = (new DateService)
            ->randomTimestamp();

        return [
            'project_id'    => Project::factory(),
            'title'         =>  fake()->text(rand(5, 8)),
            'description'   =>  fake()->text(rand(8, 15)),
            'status'        =>  ['active', 'completed', 'suspended'][rand(0,2)],
            'deadline_at'   =>  Carbon::createFromTimestamp($timestamps['end'])->toDateTimeString(),
            'started_at'    =>  rand(0, 1) ? Carbon::createFromTimestamp($timestamps['start'])->toDateTimeString() : null,
            'paused_at'     =>  rand(0, 1) ? Carbon::createFromTimestamp($timestamps['paused'])->toDateTimeString() : null,
            'sprint_id'     =>  null
        ];
    }
}
