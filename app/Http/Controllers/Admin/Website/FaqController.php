<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqController extends Controller
{
    public function index(Request $request)
    {
        $faqs = Faq::query()

            ->when($request->search, function ($q) use ($request) {

                $q->where('question', 'like', '%' . $request->search . '%');
            })

            ->when($request->status !== null && $request->status !== '', function ($q) use ($request) {

                $q->where('status', $request->status);
            })

            ->orderBy('sort_order')

            ->paginate(20);

        return view('admin.website.faq.index', compact('faqs'));
    }

    public function create()
    {
        return view('admin.website.faq.create');
    }

    public function store(Request $request)
    {
        $request->validate([

            'question' => 'required|max:255',

            'answer' => 'nullable',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

        ]);

        Faq::create([

            'question' => $request->question,

            'answer' => $request->answer,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.faq.index')
            ->with('success', 'FAQ created successfully.');
    }

    public function show(Faq $faq)
    {
        return view('admin.website.faq.show', compact('faq'));
    }

    public function edit(Faq $faq)
    {
        return view('admin.website.faq.edit', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $request->validate([

            'question' => 'required|max:255',

            'answer' => 'nullable',

            'sort_order' => 'nullable|integer',

            'status' => 'required|boolean',

        ]);

        $faq->update([

            'question' => $request->question,

            'answer' => $request->answer,

            'sort_order' => $request->sort_order ?? 0,

            'status' => $request->status,

        ]);

        return redirect()
            ->route('admin.website.faq.index')
            ->with('success', 'FAQ updated successfully.');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();

        return redirect()
            ->route('admin.website.faq.index')
            ->with('success', 'FAQ deleted successfully.');
    }
}
