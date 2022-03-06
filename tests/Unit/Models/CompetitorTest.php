<?php

namespace Tests\Unit\Models;

use App\Models\Competitor;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CompetitorTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanGetWcaTeamsAttribute(): void
    {
        $competitor = Competitor::factory()->create();
        $teams = ['cats', 'dogs'];

        $competitor->update(['wca_teams' => $teams]);
        $this->assertEquals(collect($teams), $competitor->wca_teams);

        $competitor->update(['wca_teams' => []]);
        $this->assertEquals(collect(), $competitor->wca_teams);
    }

    public function testCanGetGuestsAttribute(): void
    {
        $competitor = Competitor::factory()->create();
        $guests = ['cats', 'dogs'];

        $competitor->update(['guests' => $guests]);
        $this->assertEquals(collect($guests), $competitor->guests);

        $competitor->update(['guests' => []]);
        $this->assertEquals(collect(), $competitor->guests);
    }
}
