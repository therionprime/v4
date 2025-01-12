<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Laravel\Nova\Actions\Actionable;

class Itms extends Model
{
    use Actionable;

    protected $fillable = [
        'name', 'url',
    ];

    protected $touches = ['apps'];

    public function providers()
    {
        return $this->belongsToMany(Provider::class)->using(Link::class);
    }

    public function apps()
    {
        return $this->belongsToMany(App::class);
    }

    public function getAppAttribute()
    {
        return $this->apps->first() ?? null;
    }

    public function getProviderAttribute()
    {
        return $this->providers->first() ?? Provider::unknown();
    }

    public function getProviderAvatarAttribute()
    {
        return $this->provider->avatar ?? null;
    }

    public function getProviderNameAttribute()
    {
        return $this->provider->name ?? null;
    }
}
