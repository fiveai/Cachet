<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Models;

use AltThree\Validator\ValidatingTrait;
use CachetHQ\Cachet\Models\Traits\HasMeta;
use CachetHQ\Cachet\Presenters\SubscriberPresenter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use McCool\LaravelAutoPresenter\HasPresenter;

/**
 * This is the subscriber model.
 *
 * @author Joseph Cohen <joe@alt-three.com>
 * @author James Brooks <james@alt-three.com>
 * @author Graham Campbell <graham@alt-three.com>
 */
class Subscriber extends Model implements HasPresenter
{
    use HasMeta;
    use Notifiable;
    use ValidatingTrait;

    /**
     * The attributes that should be casted to native types.
     *
     * @var string[]
     */
    protected $casts = [
        'name'                   => 'string',
        'email'                  => 'string',
        'phone_number'           => 'string',
        'slack_webhook_url'      => 'string',
        'mattermost_webhook_url' => 'string',
        'verify_code'            => 'string',
        'verified_at'            => 'date',
        'global'                 => 'bool',
        'component_status'       => 'bool',
        'maintenance_schedules'  => 'bool',
    ];

    /**
     * The fillable properties.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'phone_number',
        'slack_webhook_url',
        'mattermost_webhook_url',
        'verified_at',
        'global',
        'component_status',
        'maintenance_schedules',
    ];

    /**
     * The validation rules.
     *
     * @var string[]
     */
    public $rules = [
        'name'                   => 'string',
        'email'                  => 'nullable|email',
        'phone_number'           => 'nullable|string',
        'slack_webhook_url'      => 'nullable|url',
        'mattermost_webhook_url' => 'nullable|url',
    ];

    /**
     * The relations to eager load on every query.
     *
     * @var string[]
     */
    protected $with = ['subscriptions'];

    /**
     * Overrides the models boot method.
     *
     * @return void
     */
    public static function boot()
    {
        parent::boot();

        self::creating(function ($user) {
            if (!$user->verify_code) {
                $user->verify_code = self::generateVerifyCode();
            }
        });
    }

    /**
     * Get the subscriptions relation.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Scope verified subscribers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsVerified(Builder $query)
    {
        return $query->whereNotNull('verified_at');
    }

    /**
     * Scope global subscribers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsGlobal(Builder $query)
    {
        return $query->where('global', '=', true);
    }

    /**
     * Scope subscribers subscribed to status updates.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsSubscribedToStatus(Builder $query)
    {
        return $query->where('component_status', '=', true);
    }

    /**
     * Scope subscribers subscribed to maintenance schedules.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsSubscribedToSchedules(Builder $query)
    {
        return $query->where('maintenance_schedules', '=', true);
    }

    /**
     * Scope Mattermost subscribers.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeIsMattermost(Builder $query)
    {
        return $query->whereNotNull('mattermost_webhook_url');
    }

    /**
     * Finds all verified subscriptions for a component.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int                                   $component_id
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeForComponent(Builder $query, $component_id)
    {
        return $query->select('subscribers.*')
            ->join('subscriptions', 'subscribers.id', '=', 'subscriptions.subscriber_id')
            ->where('subscriptions.component_id', '=', $component_id);
    }

    /**
     * Determines if the subscriber is verified.
     *
     * @return bool
     */
    public function getIsVerifiedAttribute()
    {
        return $this->verified_at !== null;
    }

    /**
     * Returns an new verify code.
     *
     * @return string
     */
    public static function generateVerifyCode()
    {
        return Str::random(42);
    }

    /**
     * Route notifications for the Nexmo channel.
     *
     * @return string
     */
    public function routeNotificationForNexmo()
    {
        return $this->phone_number;
    }

    /**
     * Route notifications for the Slack channel.
     *
     * @return string
     */
    public function routeNotificationForSlack()
    {
        return $this->slack_webhook_url;
    }

    /**
     * Route notifications for the Mattermost channel.
     *
     * @return string
     */
    public function routeNotificationForMattermost()
    {
        return $this->mattermost_webhook_url;
    }

    /**
     * Get the presenter class.
     *
     * @return string
     */
    public function getPresenterClass()
    {
        return SubscriberPresenter::class;
    }
}
