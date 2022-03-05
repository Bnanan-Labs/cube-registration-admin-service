<?php

namespace Tests\Feature\GraphQL;

use App\Models\Event;
use App\Services\Wca\Enums\Event as EventEnum;
use App\Services\Wca\Wca;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\GraphQLTestCase;

class EventTest extends GraphQLTestCase
{
    use RefreshDatabase, WithFaker;

    public function testCanQueryEvent(): void
    {
        $event = Event::factory()->create();

        $this->graphQL(/** @lang GraphQL */ '
            query ($id: ID!){
                event(id: $id) {
                    id
                    wca_event_id
                    title
                    full_name
                    short_name
                    result_format
                    qualification_limit
                    cutoff_limit
                }
            }
        ', [
            'id' => $event->id
        ])->assertJSON([
            'data' => [
                'event' => [
                    'id' => $event->id,
                    'wca_event_id' => $event->wca_event_id,
                    'title' => $event->title,
                    'full_name' => $event->full_name,
                    'short_name' => $event->short_name,
                    'result_format' => $event->result_format,
                    'qualification_limit' => $event->qualification_limit,
                    'cutoff_limit' => $event->cutoff_limit,
                ],
            ],
        ]);
    }

    public function testCanQueryWcaEventFields(): void
    {
        $wcaService = new Wca();
        $wcaEvent333 = $wcaService->event(EventEnum::threeByThree);
        $wcaEventPyra = $wcaService->event(EventEnum::pyraminx);
        $event333 = Event::factory()->create(['wca_event_id' => $wcaEvent333->id]);
        $eventPyra = Event::factory()->create(['wca_event_id' => $wcaEventPyra->id]);

        $query = /** @lang GraphQL */ '
            query ($id: ID!){
                event(id: $id) {
                    id
                    wca_event_id
                    title
                    full_name
                    short_name
                    result_format
                }
            }
        ';

        $this->graphQL($query, [
            'id' => $event333->id
        ])->assertJSON([
            'data' => [
                'event' => [
                    'id' => $event333->id,
                    'wca_event_id' => $event333->wca_event_id,
                    'title' => $event333->title,
                    'full_name' => $wcaEvent333->fullName,
                    'short_name' => $wcaEvent333->shortName,
                    'result_format' => $wcaEvent333->resultFormat->value,
                ],
            ],
        ]);

        $this->graphQL($query, [
            'id' => $eventPyra->id
        ])->assertJSON([
            'data' => [
                'event' => [
                    'id' => $eventPyra->id,
                    'wca_event_id' => $wcaEventPyra->id,
                    'title' => $eventPyra->title,
                    'full_name' => $wcaEventPyra->fullName,
                    'short_name' => $wcaEventPyra->shortName,
                    'result_format' => $wcaEventPyra->resultFormat->value,
                ],
            ],
        ]);
    }
}
