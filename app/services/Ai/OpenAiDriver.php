<?php

namespace App\Services\Ai;

use App\Models\ServiceType;
use Illuminate\Support\Facades\Http;

class OpenAiDriver implements \App\Contracts\AiServiceContract
{
    /**
     * Uses the OpenAI chat completions API to map a gig description to the
     * closest entries in the service catalog, then returns real catalog names.
     */
    public function suggestServices(string $title, string $description, ?int $categoryId = null): array
    {
        $catalog = ServiceType::where('is_active', true)
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId))
            ->limit(200)
            ->pluck('name')
            ->all();

        if (!$catalog) {
            return [];
        }

        $apiKey = config('ai.openai.api_key');
        if (!$apiKey) {
            return [];
        }

        try {
            $response = Http::withToken($apiKey)
                ->timeout(config('ai.openai.timeout', 30))
                ->post(rtrim(config('ai.openai.base_url'), '/') . '/chat/completions', [
                    'model' => config('ai.openai.model', 'gpt-4o-mini'),
                    'temperature' => 0.2,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => 'You map freelance gig descriptions to the closest service labels from a given catalog. Return ONLY a JSON array of the exact catalog labels, at most 8, no markdown, no explanation.',
                        ],
                        [
                            'role' => 'user',
                            'content' => 'Catalog: ' . json_encode($catalog) . "\n\nTitle: {$title}\nDescription: {$description}\n\nReturn the JSON array of matching catalog labels.",
                        ],
                    ],
                ]);

            if (!$response->successful()) {
                return [];
            }

            $content = $response->json('choices.0.message.content');
            $names = is_array($content) ? $content : $this->extractJsonArray((string) $content);

            $names = array_values(array_filter(
                array_map('trim', $names),
                fn($n) => in_array($n, $catalog, true)
            ));

            return array_slice($names, 0, 8);
        } catch (\Throwable $e) {
            return [];
        }
    }

    protected function extractJsonArray(string $content): array
    {
        if (preg_match('/\[.*\]/s', $content, $m)) {
            $decoded = json_decode($m[0], true);
            return is_array($decoded) ? $decoded : [];
        }
        return [];
    }
}