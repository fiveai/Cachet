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

/**
 * This is the subscribe subscriber command.
 *
 * @author James Brooks <james@alt-three.com>
 */
final class SubscribeSubscriberCommand
{
    /**
     * The subscriber name (identifier).
     *
     * @var string
     */
    public $name;

    /**
     * The subscriber email.
     *
     * @var string
     */
    public $email;

    /**
     * The url of the Mattermost webhook.
     *
     * @var string
     */
    public $webhook_url;

    /**
     * The subscriber auto verification.
     *
     * @var bool
     */
    public $verified;

    /**
     * The list of subscriptions to set the subscriber up with.
     *
     * @var array|null
     */
    public $subscriptions;

    /**
     * The validation rules.
     *
     * @var array
     */
    public $rules = [
        'email'       => 'nullable|email',
        'webhook_url' => 'nullable|url',
    ];

    /**
     * Create a new subscribe subscriber command instance.
     *
     * @param string     $name
     * @param string     $email
     * @param string     $webhook_url
     * @param bool       $verified
     * @param array|null $subscriptions
     *
     * @return void
     */
    public function __construct($name, $email, $webhook_url, $verified = false, $subscriptions = null)
    {
        $this->name = $name;
        $this->email = $email;
        $this->webhook_url = $webhook_url;
        $this->verified = $verified;
        $this->subscriptions = $subscriptions;
    }
}
