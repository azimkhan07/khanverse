<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            SEO Settings

        </h5>

        <a
            href="{{ route('admin.website.seo.create') }}"
            class="btn btn-primary">

            <i class="fa fa-plus"></i>

            Add SEO

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th width="60">#</th>

                    <th>Page</th>

                    <th>Meta Title</th>

                    <th width="100">Status</th>

                    <th width="150">Updated</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($seoSettings as $seo)

                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ ucfirst(str_replace('_',' ',$seo->page_key)) }}

                        </td>

                        <td>

                            {{ Str::limit($seo->meta_title,60) }}

                        </td>

                        <td>

                            @if($seo->status)

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

                            {{ $seo->updated_at->format('d M Y') }}

                        </td>

                        <td>

                            <a
                                href="{{ route('admin.website.seo.show',$seo->id) }}"
                                class="btn btn-info btn-sm">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a
                                href="{{ route('admin.website.seo.edit',$seo->id) }}"
                                class="btn btn-warning btn-sm">

                                <i class="fa fa-edit"></i>

                            </a>

                            <form
                                action="{{ route('admin.website.seo.destroy',$seo->id) }}"
                                method="POST"
                                class="d-inline">

                                @csrf

                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete this SEO record?')">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">

                            No SEO Records Found

                        </td>

                    </tr>

                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $seoSettings->links() }}

        </div>

    </div>

</div>
