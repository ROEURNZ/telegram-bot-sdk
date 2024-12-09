<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\TelegramCommandService;

class SetTelegramMenu extends Command
{
    protected $signature = 'telegram:set-menu';
    protected $description = 'Set the Telegram bot command menu';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle(TelegramCommandService $telegramService)
    {
        $result = $telegramService->setCommandMenu();
        $this->info($result);
    }
}
