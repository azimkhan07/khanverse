<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <h5 class="mb-0">

            FAQ List

        </h5>

        <a href="{{ route('admin.website.faq.create') }}" class="btn btn-primary">

            <i class="fa fa-plus"></i> Add FAQ

        </a>

    </div>

    <div class="card-body table-responsive">

        <table class="table table-bordered table-hover">

            <thead>

                <tr>

                    <th width="60">#</th>

                    <th>Question</th>

                    <th width="120">Sort Order</th>

                    <th width="120">Status</th>

                    <th width="150">Updated</th>

                    <th width="180">Action</th>

                </tr>

            </thead>

            <tbody>

                @forelse($faqs as $faq)
                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            {{ Str::limit($faq->question, 80) }}

                        </td>

                        <td>

                            {{ $faq->sort_order }}

                        </td>

                        <td>

                            @if ($faq->status)
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

                            {{ $faq->updated_at->format('d M Y') }}

                        </td>

                        <td>

                            <a href="{{ route('admin.website.faq.show', $faq->id) }}" class="btn btn-info btn-sm">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ route('admin.website.faq.edit', $faq->id) }}" class="btn btn-warning btn-sm">

                                <i class="fa fa-edit"></i>

                            </a>

                            <form action="{{ route('admin.website.faq.destroy', $faq->id) }}" method="POST"
                                class="d-inline">

                                @csrf

                                @method('DELETE')

                                <button class="btn btn-danger btn-sm" onclick="return confirm('Delete this FAQ?')">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="6" class="text-center">

                            No FAQ Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

        <div class="mt-3">

            {{ $faqs->links() }}

        </div>

    </div>

</div>
