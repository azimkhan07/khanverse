<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AiServiceContract;
use App\Http\Controllers\Controller;
use App\Models\ServiceType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AiController extends Controller
{
    public function __construct(protected AiServiceContract $ai)
    {
    }

    public function suggestServices(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:5000'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
        ]);

        $names = $this->ai->suggestServices(
            $validated['title'],
            $validated['description'] ?? '',
            $validated['category_id'] ?? null
        );

        $types = collect($names)->flatMap(fn($name) => ServiceType::where('name', $name)->get());

        return response()->json([
            'data' => $types->map(fn($t) => [
                'id' => $t->id,
                'name' => $t->name,
                'category_id' => $t->category_id,
                'category' => $t->category?->name,
            ])->values(),
        ]);
    }
}