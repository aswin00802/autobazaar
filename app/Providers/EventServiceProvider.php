<?php

namespace App\Providers;

// use Illuminate\Support\ServiceProvider;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use SocialiteProviders\Manager\SocialiteWasCalled;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SocialiteWasCalled::class => [
            // Extra socialite providers
            'SocialiteProviders\\Twitter\\TwitterExtendSocialite@handle',
            'SocialiteProviders\\LinkedIn\\LinkedInExtendSocialite@handle',
        ],
    ];

    public function boot(): void
    {
        parent::boot();
    }
}
