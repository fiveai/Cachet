<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Notifications\Schedule;

use CachetHQ\Cachet\Models\Schedule;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\URL;
use McCool\LaravelAutoPresenter\Facades\AutoPresenter;

/**
 * This is the new schedule notification class.
 *
 * @author James Brooks <james@alt-three.com>
 */
class NewScheduleNotification extends Notification
{
    use Queueable;

    /**
     * The schedule.
     *
     * @var \CachetHQ\Cachet\Models\Schedule
     */
    protected $schedule;

    /**
     * Create a new notification instance.
     *
     * @param \CachetHQ\Cachet\Models\Schedule $schedule
     *
     * @return void
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = AutoPresenter::decorate($schedule);
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

        $content = trans('notifications.schedule.new.mail.content', [
            'name' => $this->schedule->name,
            'date' => $this->schedule->scheduled_at_formatted,
        ]);

        return (new MailMessage())
            ->subject(trans('notifications.schedule.new.mail.subject'))
            ->markdown('notifications.schedule.new', [
                'content'                => $content,
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
        $content = trans('notifications.schedule.new.sms.content', [
            'name' => $this->schedule->name,
            'date' => $this->schedule->scheduled_at_formatted,
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
        $content = trans('notifications.schedule.new.slack.content', [
            'name' => $this->schedule->name,
            'date' => $this->schedule->scheduled_at_formatted,
        ]);

        return (new SlackMessage())
                    ->content(trans('notifications.schedule.new.slack.title'))
                    ->attachment(function ($attachment) use ($content) {
                        $attachment->title($content)
                                   ->timestamp($this->schedule->getWrappedObject()->scheduled_at)
                                   ->fields(array_filter([
                                       'ID'     => "#{$this->schedule->id}",
                                       'Status' => $this->schedule->human_status,
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
        $fallback_content = trans('notifications.schedule.new.mattermost.content', [
            'name' => $this->schedule->name,
            'date' => $this->schedule->scheduled_at_formatted,
        ]);

        $final_data = [
            'text'        => trans('notifications.schedule.new.mattermost.title'),
            'attachments' => [
                array_filter([
                    'fallback'    => $fallback_content,
                    'title'       => $this->schedule->name,
                    'text'        => $this->schedule->message,
                    'author_name' => Config::get('setting.app_name'),
                    'author_icon' => asset('img/cachet-icon.png'),
                    'footer'      => "#{$this->schedule->id}",
                    'fields' => array_filter([
                        [
                            'short' => false,
                            'title' => trans('notifications.schedule.new.mattermost.status'),
                            'value' => $this->schedule->human_status,
                        ],
                        [
                            'short' => true,
                            'title' => trans('notifications.schedule.new.mattermost.start'),
                            'value' => $this->schedule->scheduled_at_formatted,
                        ],
                        [
                            'short' => true,
                            'title' => trans('notifications.schedule.new.mattermost.end'),
                            'value' => $this->schedule->completed_at_formatted,
                        ],
                    ],
                    function($array) { return !empty($array['value']); }),
                ])
            ],
        ];

        return $final_data;
    }
}
