<?php

namespace App\Traits;

use App\Enums\FlashType;

trait FlashesMessages
{
    protected function flashMessage(string $message, FlashType $type = FlashType::INFO): array
    {
        return [
            'flash_message' => $message,
            'flash_type' => $type->value,
        ];
    }

    protected function flashSuccess(string $message): array
    {
        return $this->flashMessage($message, FlashType::SUCCESS);
    }

    protected function flashError(string $message): array
    {
        return $this->flashMessage($message, FlashType::ERROR);
    }

    protected function flashWarning(string $message): array
    {
        return $this->flashMessage($message, FlashType::WARNING);
    }

    protected function flashInfo(string $message): array
    {
        return $this->flashMessage($message, FlashType::INFO);
    }
}
