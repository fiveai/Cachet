<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Notifications\IncidentUpdate;

use CachetHQ\Cachet\Models\Incident;
use CachetHQ\Cachet\Models\IncidentUpdate;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

/**
 * This is the incident updated notification class.
 *
 * @author James Brooks <james@alt-three.com>
 */
class IncidentUpdatedNotification extends Notification
{
    use Queueable;

    /**
     * The incident update.
     *
     * @var \CachetHQ\Cachet\Models\IncidentUpdate
     */
    protected $update;

    /**
     * Create a new notification instance.
     *
     * @param \CachetHQ\Cachet\Models\IncidentUpdate $update
     *
     * @return void
     */
    public function __construct(IncidentUpdate $update)
    {
        $this->update = AutoPresenter::decorate($update);
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

        $content = trans('notifications.incident.update.mail.content', [
            'name'    => $this->update->incident->name,
            'time'    => $this->update->created_at_diff,
        ]);

        return (new MailMessage())
            ->subject(trans('notifications.incident.update.mail.subject'))
            ->markdown('notifications.incident.update', [
                'incident'               => $this->update->incident,
                'update'                 => $this->update,
                'content'                => $content,
                'actionText'             => trans('notifications.incident.new.mail.action'),
                'actionUrl'              => cachet_route('incident', [$this->update->incident]),
                'incidentName'           => $this->update->incident->name,
                'newStatus'              => $this->update->human_status,
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
        $content = trans('notifications.incident.update.sms.content', [
            'name' => $this->update->incident->name,
        ]);

        return (new NexmoMessage())->content($content);
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
        $content = trans('notifications.incident.update.slack.content', [
            'name'       => $this->update->incident->name,
            'new_status' => $this->update->human_status,
        ]);

        $status = 'info';

        if ($this->update->status === Incident::FIXED) {
            $status = 'success';
        } elseif ($this->update->status === Incident::WATCHED) {
            $status = 'warning';
        } else {
            $status = 'error';
        }

        return (new SlackMessage())
                    ->$status()
                    ->content($content)
                    ->attachment(function ($attachment) use ($notifiable) {
                        $attachment->title(trans('notifications.incident.update.slack.title', [
                            'name'       => $this->update->incident->name,
                            'new_status' => $this->update->human_status,
                        ]))
                                   ->timestamp($this->update->getWrappedObject()->created_at)
                                   ->fields(array_filter([
                                       'ID'   => "#{$this->update->id}",
                                       'Link' => $this->update->permalink,
                                   ]))
                                   ->footer(trans('cachet.subscriber.unsubscribe', ['link' => cachet_route('subscribe.unsubscribe', $notifiable->verify_code)]));
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
        if ($this->update->status === Incident::FIXED) {
            $status_color = '#00D11C'; // Green
        } elseif ($this->update->status === Incident::WATCHED) {
            $status_color = '#FF8000'; // Orange
        } else {
            $status_color = '#FF4400'; // Red
        }

        $component_name = '';
        $component_status = '';
        if ($this->update->incident->component) {
            $component_name = $this->update->incident->component->name;
            $component_status = trans("cachet.components.status.{$this->update->incident->component->status}");
        }


        $final_data = [
            'text'        => trans('notifications.incident.update.mattermost.title'),
            'attachments' => [
                array_filter([
                    'fallback'    => trans('notifications.incident.update.mattermost.content', ['name' => $this->update->incident->name, 'new_status' => $this->update->human_status]),
                    'color'       => $status_color,
                    'title'       => '['.$this->update->human_status.'] '.$this->update->incident->name,
                    'text'        => $this->update->message."\n[".trans('notifications.incident.update.mattermost.action')."](".cachet_route('incident', [$this->update->incident]).")",
                    'author_name' => Config::get('setting.app_name'),
                    'author_icon' => asset('img/cachet-icon.png'),
                    'footer'      => "#{$this->update->id}",
                    'fields' => array_filter([
                        [
                            'short' => true,
                            'title' => trans('notifications.incident.update.mattermost.component'),
                            'value' => $component_name,
                        ],
                        [
                            'short' => true,
                            'title' => trans('notifications.incident.update.mattermost.status'),
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
