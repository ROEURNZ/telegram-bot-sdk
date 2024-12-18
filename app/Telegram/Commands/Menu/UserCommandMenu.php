<?php

namespace App\Telegram\Commands\Menu;

class UserCommandMenu
{

    public $commands = array(
        array(
            'command' => 'start',
            'description' => 'Start the bot'
        ),
        array(
            'command' => 'sharecontact',
            'description' => 'Share your contact information'
        ),
        array(
            'command' => 'changelanguage',
            'description' => 'Change the system language.'
        ),
        array(
            'command' => 'manage',
            'description' => 'Manage the visits'
        ),
        array(
            'command' => 'help',
            'description' => 'Get help'
        ),
    );

}
