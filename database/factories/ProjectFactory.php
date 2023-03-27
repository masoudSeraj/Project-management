<?php

namespace Database\Factories;

use Carbon\Carbon;
use App\Services\DateService\DateService;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        DateService::setCustomKeys(['changed_at', 'deadline_at']);

        $timestamps = (new DateService)
            ->randomTimestamp(2);
        
        return [
            'title' => fake()->text(rand(5, 8)),
            'description'   => fake()->text(rand(20, 40)),
            'status'    => ['active', 'completed', 'suspended'][rand(0,2)],
            'changed_at'    =>  rand(0, 1) ? Carbon::createFromTimestamp($timestamps['changed_at'])->toDateTimeString() : null,
            'deadline_at'   =>  Carbon::createFromTimestamp($timestamps['deadline_at'])->toDateTimeString()
        ];
    }
}
