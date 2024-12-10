<?php

namespace InternetGuru\LaravelTranslatable\Traits;

use Illuminate\Support\Facades\Cache;
use InternetGuru\LaravelTranslatable\Models\Translation;

trait Translatable
{
    const NULL_PLACEHOLDER = '__NULL__';

    const TRANSLATABLE_CACHE_TTL = 60 * 60 * 24;

    protected bool $isInitializing = true;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->isInitializing = false;
    }

    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    public function getTranslatableAttributes()
    {
        return $this->translatable ?? [];
    }

    public function getAttribute($key)
    {
        if (! in_array($key, $this->getTranslatableAttributes())) {
            return parent::getAttribute($key);
        }

        return $this->translate($key, app()->getLocale(), app()->getFallbackLocale());
    }

    public function getAttributeTranslations($key, $useFallbackLocale = true)
    {
        foreach (config('languages') as $locale => $language) {
            $fallbackLocale = $useFallbackLocale ? app()->getFallbackLocale() : $locale;
            $translations[$locale] = $this->translate($key, $locale, $fallbackLocale);
        }

        return $translations;
    }

    public function setAttribute($key, $value)
    {
        if (! in_array($key, $this->getTranslatableAttributes())) {
            parent::setAttribute($key, $value);

            return $this;
        }

        $locale = app()->getLocale();
        // do not set translations into database during initialization phase
        if (! $this->isInitializing) {
            $this->setTranslation($key, $locale, $value);
        }

        return $this;
    }

    public function translate($attribute, $locale, $fallbackLocale)
    {
        $cacheKey = $this->getTranslationCacheKey($attribute, $locale, $fallbackLocale);
        $cachedValue = Cache::get($cacheKey);

        // use NULL_PLACEHOLDER to cache null values
        if ($cachedValue === self::NULL_PLACEHOLDER) {
            return null;
        }
        if ($cachedValue !== null) {
            return $cachedValue;
        }

        $value = $this->computeTranslation($attribute, $locale, $fallbackLocale);
        Cache::put($cacheKey, $value === null ? self::NULL_PLACEHOLDER : $value, self::TRANSLATABLE_CACHE_TTL);

        return $value;
    }

    protected function computeTranslation($attribute, $locale, $fallbackLocale)
    {
        $translations = $this->translations()->where('attribute', $attribute)->get();
        if ($translations->isEmpty()) {
            return null;
        }
        if ($translation = $translations->where('locale', $locale)->first()) {
            return $translation->value;
        }
        if ($translation = $translations->where('locale', $fallbackLocale)->first()) {
            return $translation->value;
        }

        return null;
    }

    public function setTranslation($attribute, $locale, $value)
    {
        if ($value) {
            $this->translations()->updateOrCreate(
                ['attribute' => $attribute, 'locale' => $locale],
                ['value' => $value]
            );
        } else {
            $this->translations()->where('attribute', $attribute)->where('locale', $locale)->delete();
        }

        $fallbackLocale = app()->getFallbackLocale();
        Cache::forget($this->getTranslationCacheKey($attribute, $locale, $fallbackLocale));
        Cache::forget($this->getTranslationCacheKey($attribute, $locale, $locale));
    }

    protected function getTranslationCacheKey($attribute, $locale, $fallbackLocale)
    {
        return 'translation_' . get_class($this) . "_{$this->id}_{$attribute}_{$locale}_{$fallbackLocale}";
    }
}
