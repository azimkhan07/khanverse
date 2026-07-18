<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h5>

            Pages

        </h5>

        <a href="{{ route('admin.website.pages.create') }}" class="btn btn-primary">

            Add Page

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th>#</th>

                    <th>Banner</th>

                    <th>Title</th>

                    <th>Slug</th>

                    <th>Status</th>

                    <th>Updated</th>

                    <th width="170">

                        Action

                    </th>

                </tr>

            </thead>

            <tbody>

                @forelse($pages as $page)
                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            @if ($page->banner_image)
                                <img src="{{ asset('storage/' . $page->banner_image) }}" width="70"
                                    class="img-thumbnail">
                            @else
                                N/A
                            @endif

                        </td>

                        <td>

                            {{ $page->title }}

                        </td>

                        <td>

                            {{ $page->slug }}

                        </td>

                        <td>

                            @if ($page->status)
                                <span class="badge badge-success">

                                    Active

                                </span>
                            @else
                                <span class="badge badge-danger">

                                    Inactive

                                </span>
                            @endif

                        </td>

                        <td>

                            {{ $page->updated_at->format('d M Y') }}

                        </td>

                        <td>

                            <a href="{{ route('admin.website.pages.show', $page->id) }}" class="btn btn-info btn-sm">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ route('admin.website.pages.edit', $page->id) }}" class="btn btn-warning btn-sm">

                                <i class="fa fa-edit"></i>

                            </a>

                            <form action="{{ route('admin.website.pages.destroy', $page->id) }}" method="POST"
                                class="d-inline">

                                @csrf
                                @method('DELETE')

                                <button onclick="return confirm('Delete this page?')" class="btn btn-danger btn-sm">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">

                            No Pages Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

        {{ $pages->links() }}

    </div>

</div>
