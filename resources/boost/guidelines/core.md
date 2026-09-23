# Laravel Translatable (internetguru/laravel-translatable)

Per-locale values for Eloquent attributes, stored in a polymorphic `translations` table (`translatable_type`, `translatable_id`, `locale`, `attribute`, `value`). The README is partly outdated; the trait below is the source of truth.

## Usage

- Add `use InternetGuru\LaravelTranslatable\Traits\Translatable;` to the model and list the attributes in `protected $translatable = ['name', 'description'];`. They are not columns of the model's table: no migration column, no `$fillable` entry, no cast.
- Reading `$model->name` returns the value for `app()->getLocale()`, falling back to `app()->getFallbackLocale()`, then `null`. `$model->getAttributeTranslations('name')` returns every locale in `config('languages')`.
- Write a value for a locale with `$model->setTranslation('name', 'cs', $value)`. Assigning `$model->name = $value` writes the **current** locale.

## Traps

- **Writes go straight to the database.** Assigning a translatable attribute calls `updateOrCreate` immediately, not on `save()`, so the model must already exist. Create and save the model first, then set its translations.
- **Values passed to the constructor are dropped.** Translatable keys given to `new Model([...])`, `Model::create([...])` or a factory's attribute array are ignored. Set them after creating the model, e.g. in a factory's `afterCreating()`.
- An empty value deletes that locale's translation rather than storing `''`.
- Values are cached per model, attribute and locale. `setTranslation()` clears the cache, but writing to the `translations` table directly does not, so always go through the trait.
- Each uncached read runs its own query per model, attribute and locale; eager-loading `translations` does not help, because the trait queries the relation rather than a loaded collection. Keep translated attributes out of large lists, or accept the queries.
