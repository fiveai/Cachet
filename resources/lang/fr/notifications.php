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
        'salutation'       => 'Cordialement,',
        'alternative_link' => "Si vous avez de la difficulté à cliquer sur le bouton \":actionText\" copiez et collez l’URL ci-dessous dans votre navigateur web : :actionURL",
        'copyright_notice' => 'Tout droits réservés.',
    ],
    'component' => [
        'status_update' => [
            'mail' => [
                'subject'  => 'État des composants mis à jour',
                'greeting' => 'L\'état d’un composant a été mis à jour !',
                'content'  => ':name statut changé de  :old_status à :new_status.',
                'action'   => 'Affichage',
                'thanks'   => 'Cordialement,',
            ],
            'slack' => [
                'title'   => 'État des composants mis à jour',
                'content' => ':name statut changé de  :old_status à :new_status.',
            ],
            'sms' => [
                'content' => ':name statut changé de  :old_status à :new_status.',
            ],
        ],
    ],
    'incident' => [
        'new' => [
            'mail' => [
                'subject'  => 'Nouvel incident signalé',
                'greeting' => 'Un nouvel incident a été signalé à l\'adresse :app_name.',
                'content'  => 'Incident :name a été signalé',
                'action'   => 'Affichage',
                'thanks'   => 'Cordialement,',
            ],
            'slack' => [
                'title'   => 'Incident :name a été signalé',
                'content' => 'Un nouvel incident a été signalé à l\'adresse :app_name.',
            ],
            'sms' => [
                'content' => 'Un nouvel incident a été signalé à l\'adresse :app_name.',
            ],
        ],
        'update' => [
            'mail' => [
                'subject' => 'Incident mis à jour',
                'content' => ':name a été mis à jour',
                'title'   => ':name a été mis à jour pour :new_status',
                'action'  => 'Affichage',
                'thanks'   => 'Cordialement,',
            ],
            'slack' => [
                'title'   => ':name a été mis à jour',
                'content' => ':name a été mis à jour pour :new_status',
            ],
            'sms' => [
                'content' => 'Incident :name a été mis à jour',
            ],
        ],
    ],
    'schedule' => [
        'new' => [
            'mail' => [
                'subject' => 'Nouveau calendrier créé',
                'content' => '<Strong>Planifié</Strong><br>:name est prévue pour :date,<br><p>:message</p> ',
                'title'   => 'Une nouvelle maintenance planifiée a été créée. ',
                'action'  => 'Affichage',
                'thanks'   => 'Cordialement,',
            ],
            'slack' => [
                'title'   => 'Nouveau calendrier créé',
                'content' => ':name est prévue pour :date',
            ],
            'sms' => [
                'content' => ':name est prévue pour :date',
            ],
        ],
    ],
    'subscriber' => [
        'verify' => [
            'mail' => [
                'content' => 'Cliquez pour vérifier votre abonnement à la page d\'état :app_name.',
                'title'   => 'Vérifiez votre abonnement à la page d’état :app_name.',
                'action'  => 'Vérifer',
            ],
        ],
        'manage' => [
            'mail' => [
                'subject' => 'Gérer vos abonnements',
                'content' => 'Cliquez pour gérer votre abonnement à la page d\'état :app_name.',
                'title'   => 'Cliquez pour gérer votre abonnement à la page d\'état :app_name.',
                'action'  => 'Gérer l\'abonnement',
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
                'subject' => 'Votre invitation est à l\'intérieur...',
                'content' => 'Vous avez été invité à rejoindre la page de statut :app_name.',
                'title'   => 'Vous êtes invité à rejoindre :app_name status page.',
                'action'  => 'Accepter',
            ],
        ],
    ],
];

