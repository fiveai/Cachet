<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [
    'common' => [
        'salutation'       => 'Thanks,',
        'alternative_link' => 'If you\'re having trouble clicking the ":actionText" button, copy and paste the URL below into your web browser: :actionURL',
        'copyright_notice' => 'All rights reserved.',
    ],
    'component' => [
        'status_update' => [
            'mail' => [
                'subject'  => 'Component Status Updated',
                'greeting' => 'A component\'s status was updated!',
                'content'  => ':name status changed from :old_status to :new_status.',
                'action'   => 'View',
            ],
            'mattermost' => [
                'title'         => 'Component Status Updated',
                'action'        => 'View',
                'content'       => ':name status changed from :old_status to :new_status.',
                'content_short' => 'Status changed from **:old_status** to **:new_status**.',
            ],
            'slack' => [
                'title'   => 'Component Status Updated',
                'content' => ':name status changed from :old_status to :new_status.',
            ],
            'sms' => [
                'content' => ':name status changed from :old_status to :new_status.',
            ],
        ],
    ],
    'incident' => [
        'new' => [
            'mail' => [
                'subject'  => 'New Incident Reported',
                'greeting' => 'A new incident was reported at :app_name.',
                'content'  => 'Incident :name was reported',
                'action'   => 'View',
            ],
            'mattermost' => [
                'title'     => 'New Incident Reported',
                'action'    => 'View',
                'content'   => 'A new incident, :name, was reported at :app_name',
                'component' => 'Component',
                'status'    => 'Current Status',
            ],
            'slack' => [
                'title'   => 'Incident :name Reported',
                'content' => 'A new incident was reported at :app_name',
            ],
            'sms' => [
                'content' => 'A new incident was reported at :app_name.',
            ],
        ],
        'update' => [
            'mail' => [
                'subject' => 'Incident Updated',
                'content' => ':name was updated',
                'title'   => ':name was updated to :new_status',
                'action'  => 'View',
            ],
            'mattermost' => [
                'title'   => 'Incident Updated',
                'action'  => 'View',
                'content' => ':name was updated to :new_status',
                'component' => 'Component',
                'status'    => 'Current Status',
            ],
            'slack' => [
                'title'   => ':name Updated',
                'content' => ':name was updated to :new_status',
            ],
            'sms' => [
                'content' => 'Incident :name was updated',
            ],
        ],
    ],
    'schedule' => [
        'new' => [
            'mail' => [
                'subject' => 'New Schedule Created',
                'content' => ':name was scheduled for :date',
                'title'   => 'A new scheduled maintenance was created.',
                'action'  => 'View',
            ],
            'mattermost' => [
                'title'   => 'New Scheduled Maintenance Created',
                'action'  => 'View',
                'content' => ':name was scheduled for :date',
                'status'  => 'Status',
                'start'   => 'Scheduled at',
                'end'     => 'Planned completion at',
            ],
            'slack' => [
                'title'   => 'New Schedule Created!',
                'content' => ':name was scheduled for :date',
            ],
            'sms' => [
                'content' => ':name was scheduled for :date',
            ],
        ],
    ],
    'subscriber' => [
        'verify' => [
            'mail' => [
                'subject' => 'Verify Your Subscription',
                'content' => 'Click to verify your subscription to :app_name status page.',
                'title'   => 'Verify your subscription to :app_name status page.',
                'action'  => 'Verify',
            ],
        ],
        'manage' => [
            'mail' => [
                'subject' => 'Manage Your Subscription',
                'content' => 'Click to manage your subscription to :app_name status page.',
                'title'   => 'Click to manage your subscription to :app_name status page.',
                'action'  => 'Manage subscription',
            ],
        ],
    ],
    'system' => [
        'test' => [
            'mail' => [
                'subject' => 'Ping from Cachet!',
                'content' => 'This is a test notification from Cachet!',
                'title'   => '🔔',
            ],
        ],
    ],
    'user' => [
        'invite' => [
            'mail' => [
                'subject' => 'Your invitation is inside...',
                'content' => 'You have been invited to join :app_name status page.',
                'title'   => 'You\'re invited to join :app_name status page.',
                'action'  => 'Accept',
            ],
        ],
    ],
];
