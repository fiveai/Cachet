<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Handlers\Events\Component;

use CachetHQ\Cachet\Bus\Events\Component\ComponentStatusWasChangedEvent;
use CachetHQ\Cachet\Integrations\Contracts\System;
use CachetHQ\Cachet\Models\Component;
use CachetHQ\Cachet\Models\Subscriber;
use CachetHQ\Cachet\Notifications\Component\ComponentStatusChangedNotification;

class SendComponentUpdateEmailNotificationHandler
{
    /**
     * The system instance.
     *
     * @var \CachetHQ\Cachet\Integrations\Contracts\System
     */
    protected $system;

    /**
     * The subscriber instance.
     *
     * @var \CachetHQ\Cachet\Models\Subscriber
     */
    protected $subscriber;

    /**
     * Create a new send incident email notification handler.
     *
     * @param \CachetHQ\Cachet\Models\Subscriber $subscriber
     *
     * @return void
     */
    public function __construct(System $system, Subscriber $subscriber)
    {
        $this->system = $system;
        $this->subscriber = $subscriber;
    }

    /**
     * Handle the event.
     *
     * @param \CachetHQ\Cachet\Bus\Events\Component\ComponentStatusWasChangedEvent $event
     *
     * @return void
     */
    public function handle(ComponentStatusWasChangedEvent $event)
    {
        $component = $event->component;

        // If we're silent or the notifications are suppressed don't send this.
        if ($event->silent || !$this->system->canNotifySubscribers()) {
            return;
        }

        // Don't email anything if the status hasn't changed.
        if ($event->original_status === $event->new_status) {
            return;
        }

        // Notify subscribers subscribed to the component and to status updates.
        $componentSubscribers = $this->subscriber
            ->isVerified()
            ->isSubscribedToStatus()
            ->forComponent($component->id)
            ->get();

        $componentSubscribers->each(function ($subscriber) use ($component, $event) {
            $subscriber->notify(new ComponentStatusChangedNotification($component, $event->new_status));
        });
    }
}
