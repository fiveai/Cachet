<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Handlers\Commands\Subscriber;

use CachetHQ\Cachet\Bus\Commands\Subscriber\SubscribeSubscriberCommand;
use CachetHQ\Cachet\Bus\Commands\Subscriber\VerifySubscriberCommand;
use CachetHQ\Cachet\Bus\Events\Subscriber\SubscriberHasSubscribedEvent;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Subscriber;
use CachetHQ\Cachet\Models\Subscription;
use CachetHQ\Cachet\Notifications\Subscriber\VerifySubscriptionNotification;

/**
 * This is the subscribe subscriber command handler.
 *
 * @author James Brooks <james@alt-three.com>
 * @author Joseph Cohen <joe@alt-three.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class SubscribeSubscriberCommandHandler
{
    /**
     * Handle the subscribe subscriber command.
     *
     * @param \CachetHQ\Cachet\Bus\Commands\Subscriber\SubscribeSubscriberCommand $command
     *
     * @return boolean (true if created else false)
     */
    public function handle(SubscribeSubscriberCommand $command)
    {
        if (Subscriber::where('name', '=', $command->name)->first()) {
            return false;
        }

        if ($command->email and Subscriber::where('email', '=', $command->email)->first()) {
            return false;
        }

        $subscriber = Subscriber::create([
            'name' => $command->name,
            'email' => $command->email,
            'mattermost_webhook_url' => $command->webhook_url
        ]);

        // Decide what to subscribe the subscriber to.
        if ($subscriptions = $command->subscriptions) {
            $components = Component::whereIn('id', $subscriptions)->get();
        } else {
            $components = Component::all();
        }

        $components->each(function ($component) use ($subscriber) {
            Subscription::create([
                'subscriber_id' => $subscriber->id,
                'component_id'  => $component->id,
            ]);
        });

        if ($command->verified) {
            execute(new VerifySubscriberCommand($subscriber));
        } else {
            $subscriber->notify(new VerifySubscriptionNotification());
        }

        event(new SubscriberHasSubscribedEvent($subscriber));

        $subscriber->load('subscriptions');

        return true;
    }
}
