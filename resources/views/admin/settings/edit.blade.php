@extends('layouts.admin')

@section('content')
    <div class="page-body">

        <div class="custom-card">

            <div class="card-header">

                <h5>
                    Edit Setting
                </h5>

            </div>

            <div class="card-body">

                <form action="{{ route('admin.settings.update', $setting->id) }}" method="POST">

                    @csrf
                    {{-- @method('PUT') --}}

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Key</label>

                                <input type="text" name="key" class="form-control" value="{{ $setting->key }}">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Type</label>

                                <select name="type" class="form-control">

                                    <option value="text" {{ $setting->type == 'text' ? 'selected' : '' }}>
                                        Text
                                    </option>

                                    <option value="textarea" {{ $setting->type == 'textarea' ? 'selected' : '' }}>
                                        Textarea
                                    </option>

                                    <option value="number" {{ $setting->type == 'number' ? 'selected' : '' }}>
                                        Number
                                    </option>

                                    <option value="image" {{ $setting->type == 'image' ? 'selected' : '' }}>
                                        Image
                                    </option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-12">

                            <div class="form-group">

                                <label>Value</label>

                                <textarea name="value" class="form-control">{{ $setting->value }}</textarea>

                            </div>

                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">

                        Update Setting

                    </button>

                </form>

            </div>

        </div>

    </div>
@endsection