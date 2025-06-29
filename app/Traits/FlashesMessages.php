<?php

namespace App\Traits;

use App\Enums\FlashType;
use Illuminate\Support\Facades\Session;

trait FlashesMessages
{
    private const DEFAULT_FLASH_DURATION = 3000;

    private function flashMessage(string $message, FlashType $type = FlashType::INFO, ?int $duration = null): void
    {
        Session::flash('flash', [
            'message' => $message,
            'type' => $type->value,
            'duration' => $duration ?? self::DEFAULT_FLASH_DURATION,
        ]);
    }

    protected function flashSuccess(string $message, ?int $duration = null): void
    {
        $this->flashMessage($message, FlashType::SUCCESS, $duration);
    }

    protected function flashError(string $message, ?int $duration = null): void
    {
        $this->flashMessage($message, FlashType::ERROR, $duration);
    }

    protected function flashWarning(string $message, ?int $duration = null): void
    {
        $this->flashMessage($message, FlashType::WARNING, $duration);
    }

    protected function flashInfo(string $message, ?int $duration = null): void
    {
        $this->flashMessage($message, FlashType::INFO, $duration);
    }
}
