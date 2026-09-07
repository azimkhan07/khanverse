<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\BrandPartner;
use Illuminate\Http\Request;

class BrandPartnerApiController extends Controller
{
    public function index(Request $request)
    {
        $partners = BrandPartner::query()
            ->when($request->search, fn($q) => $q->where('name', 'like', "%$request->search%"))
            ->orderBy('sort_order')
            ->orderBy('id')
            ->paginate($request->per_page ?? 10);

        return response()->json($partners);
    }

    public function show(BrandPartner $brandPartner)
    {
        return response()->json(['partner' => $brandPartner]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['status'] = $request->boolean('status');

        $partner = BrandPartner::create($data);

        return response()->json(['status' => true, 'message' => 'Brand Partner Created Successfully', 'partner' => $partner], 201);
    }

    public function update(Request $request, BrandPartner $brandPartner)
    {
        $data = $this->validateData($request);
        $data['status'] = $request->boolean('status');

        $brandPartner->update($data);

        return response()->json(['status' => true, 'message' => 'Brand Partner Updated Successfully', 'partner' => $brandPartner]);
    }

    public function destroy(BrandPartner $brandPartner)
    {
        $brandPartner->delete();

        return response()->json(['status' => true, 'message' => 'Brand Partner Deleted Successfully']);
    }

    public function toggleStatus(BrandPartner $brandPartner)
    {
        $brandPartner->update(['status' => !$brandPartner->status]);

        return response()->json(['status' => true, 'message' => 'Status Updated Successfully', 'partner' => $brandPartner]);
    }

    private function validateData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'logo' => ['nullable', 'string', 'max:500'],
            'url' => ['nullable', 'string', 'max:500'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'status' => ['nullable', 'boolean'],
        ]);
    }
}
