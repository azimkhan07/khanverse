<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ServiceApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Service::with(['seller', 'category', 'images']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $services = $query->latest()->paginate(min(100, $request->get('per_page', 10)));

        return response()->json($services);
    }

    public function show($id): JsonResponse
    {
        $service = Service::with(['seller', 'category', 'images'])
            ->findOrFail($id);

        return response()->json($service);
    }
}

