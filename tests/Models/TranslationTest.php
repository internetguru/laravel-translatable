<?php

namespace Tests\Models;

use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InternetGuru\LaravelTranslatable\Models\Translation;
use Tests\TestCase;

class TranslationTest extends TestCase
{
    use RefreshDatabase;

    public function test_translation_creation()
    {
        $translation = Translation::factory()->create([
            'translatable_id' => 1,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Description Traduit',
        ]);

        $this->assertDatabaseHas('translations', [
            'translatable_id' => 1,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Description Traduit',
        ]);
    }

    public function test_translation_relationship()
    {
        $room = Room::factory()->create();
        $translation = Translation::factory()->create([
            'translatable_id' => $room->id,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Description Traduit',
        ]);

        $this->assertInstanceOf(Room::class, $translation->translatable);
        $this->assertEquals($room->id, $translation->translatable->id);
    }
}
