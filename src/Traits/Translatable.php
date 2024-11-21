<?php

namespace InternetGuru\LaravelTranslatable\Traits;

use Illuminate\Support\Facades\Cache;
use InternetGuru\LaravelTranslatable\Models\Translation;

trait Translatable
{
    public function translations()
    {
        return $this->morphMany(Translation::class, 'translatable');
    }

    /**
     * Translate the given attribute to the given locale.
     * Fallback to the given fallback locale.
     * Fallback to null.
     */
    public function translate($attribute, $locale, $fallbackLocale)
    {
        $cacheKey = $this->getTraslationCacheKey($attribute, $locale, $fallbackLocale);

        return Cache::remember($cacheKey, 60, function () use ($attribute, $locale, $fallbackLocale) {
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
        });
    }

    /**
     * Get all translatable attributes.
     */
    public function getTranslatableAttributes()
    {
        return $this->translatable ?? [];
    }

    /**
     * Get the attribute from the model.
     * Use translation if exists.
     */
    public function getAttribute($key)
    {
        if (! in_array($key, $this->getTranslatableAttributes())) {
            return parent::getAttribute($key);
        }

        return $this->translate($key, app()->getLocale(), app()->getFallbackLocale());
    }

    /**
     * Return translations accroding to config.languages
     */
    public function getAttributeTranslations($key, $useFallbackLocale = true)
    {
        foreach (config('languages') as $locale => $language) {
            $fallbackLocale = $useFallbackLocale ? app()->getFallbackLocale() : $locale;
            $translations[$locale] = $this->translate($key, $locale, $fallbackLocale);
        }

        return $translations;
    }

    /**
     * Set the attribute to the model.
     * Save translation if attribute is translatable.
     */
    public function setAttribute($key, $value)
    {
        if (! in_array($key, $this->getTranslatableAttributes())) {
            parent::setAttribute($key, $value);

            return $this;
        }

        $locale = app()->getLocale();
        $this->setTranslation($key, $locale, $value);

        return $this;
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
        Cache::forget($this->getTraslationCacheKey($attribute, $locale, $fallbackLocale));
        Cache::forget($this->getTraslationCacheKey($attribute, $locale, $locale));
    }

    /**
     * Get the cache key for the translation.
     */
    private function getTraslationCacheKey($attribute, $locale, $fallbackLocale)
    {
        return 'translation_' . get_class($this) . "_{$this->id}_{$attribute}_{$locale}_{$fallbackLocale}";
    }
}
