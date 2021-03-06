<?php

namespace Tests\Feature;

use App\Models\Language;
use App\Models\Message;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MessageValueTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function testPutMessageValueWithCorrectData(): void
    {
        [$project, $message] = $this->projectAndMessage();
        $language = factory(Language::class)->create();
        $project->languages()->attach($language);

        $value = $this->faker()->sentence();

        $this->actingAsUser()->put("/projects/$project->id/messages/$message->id/$language->code", [
            'value' => $value,
        ])
            ->assertSessionHasNoErrors()
            ->assertRedirect();

        $this->assertDatabaseHas('message_values', [
            'message_id' => $message->id,
            'language_id' => $language->id,
            'value' => $value,
        ]);
    }

    private function projectAndMessage(): array
    {
        $project = factory(Project::class)->create();
        $message = factory(Message::class)->create();
        $message->project()->associate($project)->save();

        return [$project, $message];
    }
}
