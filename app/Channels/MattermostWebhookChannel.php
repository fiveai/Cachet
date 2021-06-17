<?php

namespace CachetHQ\Cachet\Channels;

use GuzzleHttp\Client as HttpClient;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;

class MattermostWebhookChannel
{
    /**
     * The HTTP client instance.
     *
     * @var \GuzzleHttp\Client
     */
    protected $http;

    /**
     * Create a new Mattermost channel instance.
     *
     * @param  \GuzzleHttp\Client  $http
     * @return void
     */
    public function __construct(HttpClient $http)
    {
        $this->http = $http;
    }

    /**
     * Send the given notification.
     *
     * @param  mixed  $notifiable
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return void
     */
    public function send($notifiable, Notification $notification)
    {
        if (! $url = $notifiable->routeNotificationFor('mattermost', $notification)) {
            return;
        }

        $this->http->post($url, [
            'json' => $notification->toMattermost($notifiable),
        ]);
    }
}
