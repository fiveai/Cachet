<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Notifications\Incident;

use CachetHQ\Cachet\Models\Incident;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

/**
 * This is the new incident notification class.
 *
 * @author James Brooks <james@alt-three.com>
 */
class NewIncidentNotification extends Notification
{
    use Queueable;

    /**
     * The incident.
     *
     * @var \CachetHQ\Cachet\Models\Incident
     */
    protected $incident;

    /**
     * Create a new notification instance.
     *
     * @param \CachetHQ\Cachet\Models\Incident $incident
     *
     * @return void
     */
    public function __construct(Incident $incident)
    {
        $this->incident = AutoPresenter::decorate($incident);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable
     *
     * @return string[]
     */
    public function via($notifiable)
    {
        return ['mail', 'nexmo', 'slack', 'mattermost'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $manageUrl = URL::signedRoute(cachet_route_generator('subscribe.manage'), ['code' => $notifiable->verify_code]);

        $content = trans('notifications.incident.new.mail.content', [
            'name' => $this->incident->name,
        ]);

        return (new MailMessage())
                    ->subject(trans('notifications.incident.new.mail.subject'))
                    ->markdown('notifications.incident.new', [
                        'incident'               => $this->incident,
                        'content'                => $content,
                        'actionText'             => trans('notifications.incident.new.mail.action'),
                        'actionUrl'              => cachet_route('incident', [$this->incident]),
                        'unsubscribeText'        => trans('cachet.subscriber.unsubscribe'),
                        'unsubscribeUrl'         => cachet_route('subscribe.unsubscribe', $notifiable->verify_code),
                        'manageSubscriptionText' => trans('cachet.subscriber.manage_subscription'),
                        'manageSubscriptionUrl'  => $manageUrl,
                    ]);
    }

    /**
     * Get the Nexmo / SMS representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage())->content(trans('notifications.incident.new.sms.content', [
            'name' => $this->incident->name,
        ]));
    }

    /**
     * Get the Slack representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return \Illuminate\Notifications\Messages\SlackMessage
     */
    public function toSlack($notifiable)
    {
        $content = trans('notifications.incident.new.slack.content', [
            'app_name' => Config::get('setting.app_name'),
        ]);

        $status = 'info';

        if ($this->incident->status === Incident::FIXED) {
            $status = 'success';
        } elseif ($this->incident->status === Incident::WATCHED) {
            $status = 'warning';
        } else {
            $status = 'error';
        }

        return (new SlackMessage())
                    ->$status()
                    ->content($content)
                    ->attachment(function ($attachment) {
                        $attachment->title(trans('notifications.incident.new.slack.title', ['name' => $this->incident->name]))
                                   ->timestamp($this->incident->getWrappedObject()->occurred_at)
                                   ->fields(array_filter([
                                       'ID'   => "#{$this->incident->id}",
                                       'Link' => $this->incident->permalink,
                                   ]));
                    });
    }

    /**
     * Get the Mattermost representation of the notification.
     *
     * @param mixed $notifiable
     *
     * @return an array meant to be converted to a json payload
     */
    public function toMattermost($notifiable)
    {
        if ($this->incident->status === Incident::FIXED) {
            $status_color = '#00D11C'; // Green
        } elseif ($this->incident->status === Incident::WATCHED) {
            $status_color = '#FF8000'; // Orange
        } else {
            $status_color = '#FF4400'; // Red
        }

        $component_name = '';
        $component_status = '';
        if ($this->incident->component) {
            $component_name = $this->incident->component->name;
            $component_status = trans("cachet.components.status.{$this->incident->component->status}");
        }

        $final_data = [
            'text'        => trans('notifications.incident.new.mattermost.title'),
            'attachments' => [
                array_filter([
                    'fallback'    => trans('notifications.incident.new.mattermost.content', [
                        'name' => $this->incident->name,
                        'app_name' => Config::get('setting.app_name')
                    ]),
                    'color'       => $status_color,
                    'title'       => $this->incident->name,
                    'text'        => $this->incident->message."\n[".trans('notifications.incident.new.mattermost.action')."](".cachet_route('incident', [$this->incident]).")",
                    'author_name' => Config::get('setting.app_name'),
                    'author_icon' => asset('img/cachet-icon.png'),
                    'footer'      => "#{$this->incident->id}",
                    'fields' => array_filter([
                        [
                            'short' => true,
                            'title' => trans('notifications.incident.new.mattermost.component'),
                            'value' => $component_name,
                        ],
                        [
                            'short' => true,
                            'title' => trans('notifications.incident.new.mattermost.status'),
                            'value' => $component_status,
                        ],
                    ],
                    function($array) { return !empty($array['value']); }),
                ])
            ],
        ];

        return $final_data;
    }
}
