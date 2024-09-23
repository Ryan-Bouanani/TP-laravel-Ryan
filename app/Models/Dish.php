<?php

namespace App\Models;

use App\Observers\DishObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Crypt;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Sagalbot\Encryptable\Encryptable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([DishObserver::class])]
class Dish extends Model
{
    use HasFactory, HasSlug;
    /**
     * The table associated with the model.
     *
     * @var string
     */
   // protected $table = 'dishes';

    public function getRouteKeyName(): string
    {
        return 'slug';
    }
    protected $fillable = [
        'name',
        'description',
        'image',
        'user_id'
    ];

    /**
     * Add dish description encryptable
     */
    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn (string $value) => Crypt::decryptString($value),
            set: fn (string $value) => Crypt::encryptString($value),
        );
    }

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }


    public function favoriteByUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
