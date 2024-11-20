<?php

namespace Tests\Traits;

use App\Models\Room;
use Illuminate\Support\Facades\Cache;
use InternetGuru\LaravelTranslatable\Models\Translation;
use Tests\TestCase;

class TranslatableTest extends TestCase
{
    public function test_translate_function()
    {
        $room = Room::factory()->create();
        $room->setAttribute('description', 'Original Description');
        $this->assertEquals('Original Description', $room->translate('description', 'fr', 'en'));

        app()->setLocale('fr');
        $room->setAttribute('description', 'Description Traduit');
        $this->assertEquals('Description Traduit', $room->translate('description', 'fr', 'en'));
        $this->assertEquals('Original Description', $room->translate('description', 'en', 'en'));
    }

    public function test_translate_not_found()
    {
        $room = Room::factory()->create();
        $this->assertNull($room->translate('description', 'fr', 'en'));
    }

    public function test_translate_function_not_fallback_to_first()
    {
        $room = Room::factory()->create();
        app()->setLocale('fr');
        $room->setAttribute('description', 'Description Traduit');

        app()->setLocale('cs');
        $room->setAttribute('description', 'Přeložený popis');

        app()->setLocale('en');
        $this->assertEquals('Description Traduit', $room->translate('description', 'fr', 'en'));
        $this->assertEquals(null, $room->translate('description', 'en', 'en'));
    }

    public function test_implicit_get_attribute_with_translation()
    {
        $room = Room::factory()->create();
        $room->setAttribute('description', 'Original Description');
        $translation = Translation::factory()->create([
            'translatable_id' => $room->id,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Description Traduit',
        ]);

        app()->setLocale('fr');
        app()->setFallbackLocale('en');
        $this->assertEquals('Description Traduit', $room->description);

        app()->setLocale('en');
        app()->setFallbackLocale('en');
        $this->assertEquals('Original Description', $room->description);
    }

    public function test_set_attribute_with_translation()
    {
        $room = Room::factory()->create();
        app()->setLocale('fr');
        app()->setFallbackLocale('en');
        $room->setAttribute('description', 'Description Traduit');

        $this->assertDatabaseHas('translations', [
            'translatable_id' => $room->id,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Description Traduit',
        ]);

        Cache::shouldReceive('forget')
            ->once()
            ->with("translation_App\\Models\\Room_{$room->id}_description_fr_en");

        $room->setAttribute('description', 'Nouvelle Description Traduit');
        $this->assertDatabaseHas('translations', [
            'translatable_id' => $room->id,
            'translatable_type' => Room::class,
            'locale' => 'fr',
            'attribute' => 'description',
            'value' => 'Nouvelle Description Traduit',
        ]);
    }
}
