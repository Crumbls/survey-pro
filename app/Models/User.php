<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\ClientService;
use App\Services\TenantService;
use App\Traits\HasSubscriptions;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

use Laravolt\Avatar\Avatar;

use Silber\Bouncer\BouncerFacade;
use App\Models\Concerns\HasRoles;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Silber\Bouncer\Database\HasRolesAndAbilities;
class User extends Authenticatable implements FilamentUser, HasMedia
{
    use HasApiTokens,
        HasFactory,
//        HasRolesAndAbilities,
        HasRoles,
        InteractsWithMedia,
        Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function booted() : void {
        static::created(function(Model $record) {
        });

        static::deleting(function(Model $record) {
            TenantUserRole::where('user_id', $record->getKey())
                ->get()
                ->each(function(Model $record) {
                    $record->delete();
                });
        });
    }

    /**
     * TODO: Change this to a permission.
     * @param Panel $panel
     * @return bool
     */
    public function canAccessPanel(Panel $panel): bool {
        return str_ends_with($this->email, ['@crumbls.com', '@o2group.com']);// && $this->hasVerifiedEmail();
    }


    public function tenants() : BelongsToMany
    {
        return $this->belongsToMany(Tenant::class, 'tenant_user')
            ->withPivot('role_id')
            ->using(TenantUserRole::class);
    }

    public function surveys() : HasMany
    {
        return $this->hasMany(Survey::class);
    }

    /**
     * Get the user's avatar URL with efficient retrieval and generation.
     *
     * @param int $size Size of the avatar (default 250)
     * @return string Avatar URL
     */
    public function getAvatar(int $size = 250): string
    {
        // Check if avatar already exists using eager loading of media
        $avatarMedia = $this->getFirstMedia('avatar');

        if ($avatarMedia) {
            return $avatarMedia->getUrl();
        }

        // Generate avatar if not exists
        return $this->generateAndStoreAvatar($size);
    }

    /**
     * Generate and store avatar for the user.
     *
     * @param int $size Size of the avatar
     * @return string Avatar URL
     */
    public function generateAndStoreAvatar(int $size = 250): string
    {
        // Generate avatar using Laravolt
        $avatarImage = new Avatar();

        $avatarImage->create($this->name)
            ->setDimension($size)
            ->setFontSize($size / 2)
            ->toBase64();

        // Convert base64 to image file
        $avatarContent = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $avatarImage));

        // Generate a unique filename
        $filename = 'avatar_' . $this->id . '_' . now()->timestamp . '.png';

        // Store the avatar using Media Library
        $this->addMediaFromString($avatarContent)
            ->usingName($filename)
            ->usingFileName($filename)
            ->toMediaCollection('avatar');

        // Retrieve and return the URL of the stored avatar
        return $this->getFirstMediaUrl('avatar');
    }

    /**
     * Clear existing avatar and regenerate.
     *
     * @param int $size Size of the avatar
     * @return string New avatar URL
     */
    public function refreshAvatar(int $size = 250): string
    {
        // Clear existing avatar media
        $this->clearMediaCollection('avatar');

        // Generate and store new avatar
        return $this->generateAndStoreAvatar($size);
    }

    /**
     * Scope to eager load avatar media.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithAvatar($query)
    {
        return $query->with('media');
    }
    // Override Bouncer's default role checking to be tenant-aware
    public function getRoles()
    {
        dd(__LINE__);
        $tenantId = tenant()->id; // Adjust based on your tenant resolution
        return $this->tenantRoles()->wherePivot('tenant_id', $tenantId)->get();
    }


    public function currentTenantRole()
    {
        dd(BouncerFacade::scope()->get());
        return $this->tenantRoles()
            ->where('tenant_id', session('current_tenant_id'))
            ->first();
    }
}
