<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Bus\Events\IncidentUpdate;

use CachetHQ\Cachet\Bus\Events\ActionInterface;
use CachetHQ\Cachet\Models\IncidentUpdate;
use CachetHQ\Cachet\Models\User;

/**
 * This is the incident update was reported event.
 *
 * @author James Brooks <james@alt-three.com>
 */
final class IncidentUpdateWasReportedEvent implements ActionInterface, IncidentUpdateEventInterface
{
    /**
     * The user who reported the incident update.
     *
     * @var \CachetHQ\Cachet\Models\User
     */
    public $user;

    /**
     * The incident update that has been reported.
     *
     * @var \CachetHQ\Cachet\Models\IncidentUpdate
     */
    public $update;

    /**
     * Whether to notify about the incident update or not.
     *
     * @var bool
     */
    public $notify;

    /**
     * Create a new incident update was reported event instance.
     *
     * @param \CachetHQ\Cachet\Models\User           $user
     * @param \CachetHQ\Cachet\Models\IncidentUpdate $update
     * @param bool                                   $notify
     *
     * @return void
     */
    public function __construct(User $user, IncidentUpdate $update, $notify = false)
    {
        $this->user = $user;
        $this->update = $update;
        $this->notify = $notify;
    }

    /**
     * Get the event description.
     *
     * @return string
     */
    public function __toString()
    {
        return 'Incident Update was reported.';
    }

    /**
     * Get the event action.
     *
     * @return array
     */
    public function getAction()
    {
        return [
            'user'        => $this->user,
            'description' => (string) $this,
        ];
    }
}
