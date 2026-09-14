<?php

namespace App\Contracts;

interface AiServiceContract
{
    /**
     * Suggest matching service catalog names for a gig description.
     *
     * @return array<int, string> list of suggested service type names
     */
    public function suggestServices(string $title, string $description, ?int $categoryId = null): array;
}