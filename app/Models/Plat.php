<?php

namespace App\Models;

use App\Observers\PlatObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use \Sagalbot\Encryptable\Encryptable;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

#[ObservedBy([PlatObserver::class])]
class Plat extends Model
{
    use HasFactory, HasSlug, Encryptable;
    /**
     * The table associated with the model.
     *
     * @var string
     */
   // protected $table = 'plats';

    protected $fillable = [
        'name',
        'description',
        'image',
        'user_id'
    ];


    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('name')
            ->saveSlugsTo('slug');
    }

    /**
     * The attributes that should be encrypted when stored.
     *
     * @var array
     */
    protected $encryptable = [ 'description' ];


    public function favoriteByUsers() : BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
