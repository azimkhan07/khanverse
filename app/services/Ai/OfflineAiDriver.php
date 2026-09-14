<?php

namespace App\Services\Ai;

use App\Models\ServiceType;
use Illuminate\Support\Str;

class OfflineAiDriver implements \App\Contracts\AiServiceContract
{
    /**
     * Keyword/name-overlap matcher. Scores every catalog entry against the
     * gig title + description, so suggestions work without any external API.
     */
    public function suggestServices(string $title, string $description, ?int $categoryId = null): array
    {
        $query = ServiceType::where('is_active', true);

        if ($categoryId) {
            $query->where('category_id', $categoryId);
        }

        $types = $query->get(['id', 'name', 'description', 'category_id']);

        $corpus = mb_strtolower($title . ' ' . $description . ' ' . $title);
        $tokens = $this->tokenize($corpus);

        $scored = [];
        foreach ($types as $type) {
            $words = $this->tokenize($type->name);
            $score = 0;

            foreach ($words as $word) {
                if (in_array($word, $tokens, true)) {
                    $score += 3;
                } elseif (Str::contains($corpus, $word)) {
                    $score += 2;
                }
            }

            // Weak two-word phrases on the name side (e.g. "web development").
            if (count($words) > 1) {
                $phrase = implode(' ', $words);
                if (Str::contains($corpus, $phrase)) {
                    $score += 4;
                }
            }

            // Primary keyword (first word) strongly matched.
            if (in_array($words[0] ?? '', $tokens, true)) {
                $score += 2;
            }

            if ($score > 0) {
                $scored[] = ['name' => $type->name, 'score' => $score];
            }
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);

        return array_slice(array_column($scored, 'name'), 0, 8);
    }

    protected function tokenize(string $text): array
    {
        $text = mb_strtolower(strip_tags($text));

        // Hinglish-friendly: keep chunks of letters/digits AND standalone devanagari.
        preg_match_all('/([a-z0-9]+|\p{Devanagari}+)/u', $text, $matches);

        return array_values(array_map('strtolower', array_unique($matches[1])));
    }
}