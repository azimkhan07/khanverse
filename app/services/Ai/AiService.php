<?php

namespace App\Services\Ai;

use Illuminate\Contracts\Container\Container;

class AiService implements \App\Contracts\AiServiceContract
{
    protected \App\Contracts\AiServiceContract $driver;

    public function __construct(Container $app)
    {
        $this->driver = match (config('ai.driver', 'offline')) {
            'openai' => $app->make(OpenAiDriver::class),
            default => $app->make(OfflineAiDriver::class),
        };
    }

    public function suggestServices(string $title, string $description, ?int $categoryId = null): array
    {
        return $this->driver->suggestServices($title, $description, $categoryId);
    }
}