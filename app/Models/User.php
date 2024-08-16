<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\enums\PrefixName;
use App\Events\UserSaved;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'prefixname' => PrefixName::class,
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        parent::booted();
        static::saved(function ($user) {
            UserSaved::dispatch($user);
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function details()
    {
        return $this->hasMany(Detail::class);
    }

    /**
     * Retrieve the default photo from storage.
     * Supply a base64 png image if the `photo` column is null.
     *
     * @return string
     */
    public function getAvatarAttribute(): string
    {
        if ($this->photo) {
            return asset('storage/' . $this->photo);
        }

        // Get the default image as a base64 string
        $path = 'public/avatar.png';
        if (Storage::exists($path)) {
            $image = Storage::get($path);
            $base64 = base64_encode($image);
            return 'data:image/png;base64,' . $base64;
        }

        return '';
    }

    /**
     * Retrieve the user's full name in the format:
     *  [firstname][ mi?][ lastname]
     * Where:
     *  [ mi?] is the optional middle initial.
     *
     * @return string
     */
    public function getFullnameAttribute(): string
    {
        return $this->firstname . ' ' . $this->middleInitial . ' ' . $this->lastname;
    }

    /**
     * Retrieve the user's middle initial in uppercase, followed by a period.
     * Returns an empty string if the middle name is not set.
     *
     * @return string
     */
    public function getMiddleinitialAttribute(): string
    {
        return $this->middlename ? strtoupper(substr($this->middlename, 0, 1)) . '.' : '';
    }

    /**
     * Retrieve the user's gender
     *
     * @return string
     */
    public function getGenderAttribute(): string
    {
        switch ($this->prefixname) {
            case PrefixName::Mr:
                return 'male';
            case PrefixName::Mrs:
            case PrefixName::Ms:
                return 'female';
            default:
                return '';
        }
    }

}
