@extends('seller.layouts.app')

@section('content')
    <div class="card">

        <div class="card-header d-flex justify-content-between">

            <h5>Services</h5>

            <button class="btn btn-primary openModalBtn" data-url="{{ route('seller.services.create') }}">

                Add Service

            </button>

        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>

                        <th>ID</th>

                        <th>Title</th>

                        <th>Category</th>

                        <th>Price</th>

                        <th>Status</th>

                        <th>Action</th>

                    </tr>

                </thead>

                <tbody>

                    @forelse($services as $service)
                        <tr>

                            <td>{{ $service->id }}</td>

                            <td>{{ $service->title }}</td>

                            <td>{{ $service->category?->name }}</td>

                            <td>{{ $service->price }}</td>

                            <td>{{ ucfirst($service->status) }}</td>

                            <td>

                                <button class="btn btn-sm btn-primary openModalBtn"
                                    data-url="{{ route('seller.services.edit', $service->id) }}">

                                    Edit

                                </button>

                                <button class="btn btn-sm btn-info openModalBtn"
                                    data-url="{{ route('seller.services.gallery', $service->id) }}">

                                    Gallery

                                </button>

                                <button class="btn btn-sm btn-danger deleteBtn"
                                    data-url="{{ route('seller.services.destroy', $service->id) }}">

                                    Delete

                                </button>


                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">

                                No Services Found

                            </td>

                        </tr>
                    @endforelse

                </tbody>

            </table>

        </div>

    </div>
@endsection
