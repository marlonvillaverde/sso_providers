<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

use SocialiteProviders\Manager\SocialiteWasCalled;
use SocialiteProviders\Twitter\TwitterExtendSocialite;
use SocialiteProviders\Auth0\Auth0ExtendSocialite;
use SocialiteProviders\Google\GoogleExtendSocialite;
use SocialiteProviders\Facebook\FacebookExtendSocialite;
use SocialiteProviders\Azure\AzureExtendSocialite;
use SocialiteProviders\Saml2\Saml2ExtendSocialite;
use SocialiteProviders\Cognito\CognitoExtendSocialite;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

       SocialiteWasCalled::class => [
            TwitterExtendSocialite::class.'@handle',
            GoogleExtendSocialite::class.'@handle',
            Auth0ExtendSocialite::class.'@handle',
            FacebookExtendSocialite::class.'@handle',
            AzureExtendSocialite::class.'@handle',
            Saml2ExtendSocialite::class.'@handle',
            CognitoExtendSocialite::class.'@handle',
        ],

    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
