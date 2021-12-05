<?php

namespace Tests\Unit\Models;

use App\Models\Competitor;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public function testIsCompetitorAttribute(): void
    {
        $competitor = Competitor::factory()->create();
        $userNotCompetitor = User::factory()->create();
        $userIsCompetitor = User::factory()->create(['wca_id' => $competitor->wca_id]);

        $this->assertEquals(false, $userNotCompetitor->isCompetitor);
        $this->assertEquals(true, $userIsCompetitor->isCompetitor);
    }

    public function testIsStaffAttribute(): void
    {
        $staff = Staff::factory()->create();
        $userNotStaff = User::factory()->create();
        $userIsStaff = User::factory()->create(['wca_id' => $staff->wca_id]);

        $this->assertEquals(false, $userNotStaff->isStaff);
        $this->assertEquals(true, $userIsStaff->isStaff);
    }
}
