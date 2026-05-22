@extends('layouts.admin')

@section('content')
    <div class="page-body">

        <div class="custom-card">
            @if (session('success'))
                <div class="alert alert-success">

                    {{ session('success') }}

                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger">

                    {{ session('error') }}

                </div>
            @endif

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5>
                    {{ ucfirst($group) }} Settings
                </h5>



                <a href="{{ route('admin.settings.create', $group) }}" class="btn btn-primary">

                    Add Setting

                </a>

            </div>
            

            <div class="card-body">

                <div class="table-responsive">

                    <table class="table table-bordered table-hover">

                        <thead>

                            <tr>
                                <th width="70">ID</th>
                                <th>Key</th>
                                <th>Value</th>
                                <th width="120">Type</th>
                                <th width="170">Action</th>
                            </tr>

                        </thead>

                        <tbody>

                            @forelse($settings as $setting)
                                <tr>

                                    <td>{{ $setting->id }}</td>

                                    <td>{{ $setting->key }}</td>

                                    <td>{{ $setting->value }}</td>

                                    <td>{{ ucfirst($setting->type) }}</td>

                                    <td>

                                        <div class="action-btns">

                                            <a href="{{ route('admin.settings.edit', ['id' => $setting->id]) }}"
                                                class="btn btn-info btn-sm">

                                                Edit

                                            </a>

                                            <form action="{{ route('admin.settings.delete', $setting->id) }}"
                                                method="POST">

                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-danger btn-sm">

                                                    Delete

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="5" class="text-center">
                                        No Settings Found
                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
    <script>
        setTimeout(function() {

            $('.alert').fadeOut();

        }, 3000);
    </script>
@endsection
