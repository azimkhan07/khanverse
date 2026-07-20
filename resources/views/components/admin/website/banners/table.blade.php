<div class="card shadow-sm">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            Banner List

        </h5>

        <a href="{{ route('admin.website.banners.create') }}" class="btn btn-primary">

            <i class="fa fa-plus"></i>

            Add Banner

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover align-middle">

            <thead>

                <tr>

                    <th width="60">#</th>

                    <th width="120">Image</th>

                    <th>Title</th>

                    <th>Position</th>

                    <th>Link</th>

                    <th width="90">Status</th>

                    <th width="130">Updated</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($banners as $banner)
                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            @if ($banner->image)
                                <img src="{{ asset('storage/' . $banner->image) }}" class="img-thumbnail" width="80">
                            @else
                                <span class="text-muted">

                                    No Image

                                </span>
                            @endif

                        </td>

                        <td>

                            {{ $banner->title }}

                        </td>

                        <td>

                            {{ ucwords(str_replace('_', ' ', $banner->position)) }}

                        </td>

                        <td>

                            @if ($banner->link)
                                <a href="{{ $banner->link }}" target="_blank">

                                    {{ Str::limit($banner->link, 30) }}

                                </a>
                            @else
                                -
                            @endif

                        </td>

                        <td>

                            @if ($banner->status)
                                <span class="badge bg-success">

                                    Active

                                </span>
                            @else
                                <span class="badge bg-danger">

                                    Inactive

                                </span>
                            @endif

                        </td>

                        <td>

                            {{ $banner->updated_at->format('d M Y') }}

                        </td>

                        <td>

                            <a href="{{ route('admin.website.banners.show', $banner->id) }}" class="btn btn-info btn-sm">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ route('admin.website.banners.edit', $banner->id) }}"
                                class="btn btn-warning btn-sm">

                                <i class="fa fa-edit"></i>

                            </a>

                            <form action="{{ route('admin.website.banners.destroy', $banner->id) }}" method="POST"
                                class="d-inline">

                                @csrf

                                @method('DELETE')

                                <button type="submit" class="btn btn-danger btn-sm"
                                    onclick="return confirm('Are you sure you want to delete this banner?')">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="8" class="text-center">

                            No banners found.

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $banners->links() }}

        </div>

    </div>

</div>
