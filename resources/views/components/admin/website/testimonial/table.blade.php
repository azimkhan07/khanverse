<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h5>Testimonials</h5>

        <a
            href="{{ route('admin.website.testimonials.create') }}"
            class="btn btn-primary">

            Add Testimonial

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Image</th>

                    <th>Name</th>

                    <th>Rating</th>

                    <th>Status</th>

                    <th>Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($testimonials as $testimonial)

                    <tr>

                        <td>{{ $loop->iteration }}</td>

                        <td>

                            @if($testimonial->image)

                                <img
                                    src="{{ asset('storage/'.$testimonial->image) }}"
                                    width="60">

                            @endif

                        </td>

                        <td>

                            {{ $testimonial->name }}

                        </td>

                        <td>

                            {{ $testimonial->rating }}/5 ⭐

                        </td>

                        <td>

                            @if($testimonial->status)

                                <span class="badge badge-success">Active</span>

                            @else

                                <span class="badge badge-danger">Inactive</span>

                            @endif

                        </td>

                        <td>

                            <a href="{{ route('admin.website.testimonial.show',$testimonial) }}" class="btn btn-info btn-sm">
                                <i class="fa fa-eye"></i>
                            </a>

                            <a href="{{ route('admin.website.testimonial.edit',$testimonial) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-edit"></i>
                            </a>

                            <form action="{{ route('admin.website.testimonial.destroy',$testimonial) }}"
                                method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this testimonial?')">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">

                            No Testimonials Found

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        {{ $testimonials->links() }}

    </div>

</div>
