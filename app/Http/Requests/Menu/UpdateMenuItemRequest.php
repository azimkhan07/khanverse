<?php

namespace App\Http\Requests\Menu;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateMenuItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $menuId = $this->route('id');
        $panel = $this->input('panel');

        if (!$panel && $menuId) {
            $panel = \App\Models\MenuItem::whereKey($menuId)->value('panel');
        }

        return [
            'title' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('menu_items', 'title')
                    ->where(function ($query) use ($panel) {
                        $query->where('panel', $panel)
                            ->whereNull('parent_id');
                    })
                    ->ignore($menuId),
            ],
            'panel' => ['sometimes', 'required', 'in:admin,seller,buyer'],
            'section' => ['nullable', 'string', 'max:255'],
            'path' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'route_name' => ['nullable', 'string', 'max:255'],
            'parent_id' => ['nullable', 'exists:menu_items,id'],
            'roles' => ['nullable', 'array'],
            'permission' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Menu title is required.',
            'title.unique' => 'A menu item with the same title already exists in this panel.',
            'panel.required' => 'Please select a panel (admin, seller or buyer).',
            'panel.in' => 'Invalid panel selected.',
        ];
    }
}