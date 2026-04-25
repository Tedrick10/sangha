<?php

namespace Tests\Feature;

use App\Models\Monastery;
use App\Models\Sangha;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MonasteryRejectedEditTest extends TestCase
{
    use RefreshDatabase;

    public function test_register_screen_with_edit_query_loads_for_rejected_sangha(): void
    {
        $this->seed();

        $monastery = Monastery::where('username', 'shwegu')->firstOrFail();
        $sangha = Sangha::create([
            'monastery_id' => $monastery->id,
            'exam_id' => null,
            'name' => 'Reject Test',
            'approved' => false,
            'rejection_reason' => 'Incomplete',
            'is_active' => true,
        ]);

        $response = $this->actingAs($monastery, 'monastery')->get(
            '/monastery?tab=main&screen=register&edit='.$sangha->id
        );

        $response->assertOk();
    }
}
