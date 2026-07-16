<div class="card shadow-sm">

    <div class="card-header">

        <h5 class="mb-0">

            Homepage Sections

        </h5>

    </div>

    <div class="table-responsive">

        <table class="table table-hover align-middle">

            <thead>

                <tr>
                    <th width="60">#</th>
                    <th width="80">Image</th>
                    <th>Section</th>
                    <th>Title</th>
                    <th width="80">Sort</th>
                    <th width="100">Status</th>
                    <th width="150">Updated</th>
                    <th width="180">Action</th>
                </tr>

            </thead>

            <tbody>

                @forelse($sections as $section)
                    <tr>

                        <td>

                            {{ $loop->iteration }}

                        </td>

                        <td>

                            @if ($section->image)
                                <img src="{{ asset('storage/' . $section->image) }}" class="img-thumbnail"
                                    style="width:60px;height:60px;object-fit:cover;">
                            @else
                                <span class="text-muted">N/A</span>
                            @endif

                        </td>

                        <td>

                            <span class="badge bg-info">

                                {{ ucwords(str_replace('_', ' ', $section->section_key)) }}

                            </span>

                        </td>

                        <td>

                            {{ $section->title }}

                        </td>

                        <td>

                            {{ $section->sort_order }}

                        </td>

                        <td>

                            @if ($section->status)
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

                            {{ $section->updated_at->format('d M Y') }}

                        </td>

                        <td>

                            <a href="{{ route('admin.website.homepage.show', $section->id) }}"
                                class="btn btn-sm btn-info">

                                <i class="fa fa-eye"></i>

                            </a>

                            <a href="{{ route('admin.website.homepage.edit', $section->id) }}"
                                class="btn btn-sm btn-warning">

                                <i class="fa fa-edit"></i>

                            </a>

                            <form action="{{ route('admin.website.homepage.destroy', $section->id) }}" method="POST"
                                style="display:inline-block"
                                onsubmit="return confirm('Are you sure you want to delete this section?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-danger">

                                    <i class="fa fa-trash"></i>

                                </button>

                            </form>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="7" class="text-center">

                            No Homepage Section Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <div class="card-footer">

        {{ $sections->links() }}

    </div>

</div>
