<?php

namespace CachetHQ\Cachet\Providers;

use CachetHQ\Cachet\Channels\MattermostWebhookChannel;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;

class MattermostChannelServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        Notification::extend('mattermost', function ($app) {
            return new MattermostWebHookChannel(new HttpClient);
        });
    }
}
