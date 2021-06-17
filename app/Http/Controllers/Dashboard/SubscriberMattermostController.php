<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Http\Controllers\Dashboard;

use AltThree\Validator\ValidationException;
use CachetHQ\Cachet\Bus\Commands\Subscriber\SubscribeSubscriberCommand;
use CachetHQ\Cachet\Bus\Commands\Subscriber\SubscribeMattermostHookCommand;
use CachetHQ\Cachet\Bus\Commands\Subscriber\UnsubscribeSubscriberCommand;
use CachetHQ\Cachet\Models\Subscriber;
use GrahamCampbell\Binput\Facades\Binput;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\View;
use Illuminate\Support\MessageBag;

class SubscriberMattermostController extends Controller
{
    /**
     * Array of sub-menu items.
     *
     * @var array
     */
    protected $subMenu = [];

    /**
     * Creates a new subscriber controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->subMenu = [
            'email' => [
                'title'  => trans('dashboard.subscribers.channel.email.name'),
                'url'    => cachet_route('dashboard.subscribers'),
                'icon'   => 'ion ion-ios-email-outline',
                'active' => false,
            ],
            'mattermost' => [
                'title'  => trans('dashboard.subscribers.channel.mattermost.name'),
                'url'    => cachet_route('dashboard.subscribers.mattermost'),
                'icon'   => 'ion ion-paper-airplane',
                'active' => false,
            ],
        ];

        View::share([
            'subMenu'  => $this->subMenu,
            'subTitle' => trans('dashboard.subscribers.subscribers'),
        ]);
    }

    /**
     * Shows the subscribers view (for Mattermost channels).
     *
     * @return \Illuminate\View\View
     */
    public function showMattermostSubscribers()
    {
        $this->subMenu['mattermost']['active'] = true;

        return View::make('dashboard.subscribers.mattermost.index')
            ->withPageTitle(trans('dashboard.subscribers.channel.email.subscribers').' - '.trans('dashboard.dashboard'))
            ->withSubscribers(Subscriber::whereNotNull('mattermost_webhook_url')->with('subscriptions.component')->get())
            ->withSubMenu($this->subMenu);
    }

    /**
     * Shows the add subscriber view (for Mattermost channels).
     *
     * @return \Illuminate\View\View
     */
    public function showAddMattermostSubscriber()
    {
        return View::make('dashboard.subscribers.mattermost.add')
            ->withPageTitle(trans('dashboard.subscribers.add.title').' - '.trans('dashboard.dashboard'));
    }

    /**
     * Creates a new Mattermost subscriber.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createMattermostSubscriberAction()
    {
        $subscriberData = Binput::get('subscriber');
        try {
            $created = execute(new SubscribeSubscriberCommand(
                $subscriberData['name'], // Name
                null,                    // Email
                $subscriberData['hook'], // Webhook url
                true                     // Verified
            ));
            if (!$created) {
                throw new ValidationException(new MessageBag([trans('dashboard.subscribers.add.name_exists')]));
            }
        } catch (ValidationException $e) {
            return cachet_redirect('dashboard.subscribers.mattermost.create')
                ->withInput(Binput::all())
                ->withTitle(sprintf('%s %s', trans('dashboard.notifications.whoops'), trans('dashboard.subscribers.add.failure')))
                ->withErrors($e->getMessageBag());
        }

        return cachet_redirect('dashboard.subscribers.mattermost')
            ->withSuccess(sprintf('%s %s', trans('dashboard.notifications.awesome'), trans('dashboard.subscribers.add.success')));
    }

    /**
     * Deletes a Mattermost subscriber.
     *
     * @param \CachetHQ\Cachet\Models\Subscriber $subscriber
     *
     * @throws \Exception
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteMattermostSubscriberAction(Subscriber $subscriber)
    {
        execute(new UnsubscribeSubscriberCommand($subscriber));

        return cachet_redirect('dashboard.subscribers.mattermost');
    }
}
