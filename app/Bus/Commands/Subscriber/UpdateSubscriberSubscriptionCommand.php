<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Commands\Subscriber;

use CachetHQ\Cachet\Models\Subscriber;

/**
 * This is the subscribe subscriber command.
 *
 * @author Joseph Cohen <joe@alt-three.com>
 */
final class UpdateSubscriberSubscriptionCommand
{
    /**
     * The subscriber email.
     *
     * @var \CachetHQ\Cachet\Models\Subscriber
     */
    public $subscriber;

    /**
     * The subscriptions that we want to add.
     *
     * @var array|null
     */
    public $subscriptions;

    /**
     * If the subscriber wants to subscribe to component status updates.
     *
     * @var bool
     */
    public $to_component_status;

    /**
     * If the subscriber wants to subscribe to maintenance schedules.
     *
     * @var bool
     */
    public $to_maintenance_schedules;

    /**
     * If the subscriber wants to subscribe to all incidents (component-indenpendent).
     *
     * @var bool
     */
    public $to_all_incidents;

    /**
     * Create a new subscribe subscriber command instance.
     *
     * @param \CachetHQ\Cachet\Models\Subscriber $subscriber
     * @param null|array                         $subscriptions
     * @param bool                               $to_component_status
     * @param bool                               $to_maintenance_schedules
     * @param bool                               $to_all_incidents
     *
     * @return void
     */
    public function __construct($subscriber, $subscriptions = null, $to_component_status = false, $to_maintenance_schedules = false, $to_all_incidents=false)
    {
        $this->subscriber = $subscriber;
        $this->subscriptions = $subscriptions;
        $this->to_component_status = $to_component_status;
        $this->to_maintenance_schedules = $to_maintenance_schedules;
        $this->to_all_incidents = $to_all_incidents;
    }
}
