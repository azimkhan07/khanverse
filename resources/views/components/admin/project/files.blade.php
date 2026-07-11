<div class="card shadow-sm border-0">

    <div class="card-header d-flex justify-content-between">

        <h5>

            Project Attachments

        </h5>

        {{-- <button class="btn btn-primary btn-sm openModalBtn"
            data-url="{{ route('admin.projects.attachments.create', $project->id) }}">

            Upload

        </button> --}}

    </div>

    <div class="card-body">

        <table class="table">

            <thead>

                <tr>

                    <th>File</th>

                    <th>Uploaded By</th>

                    <th>Size</th>

                    <th></th>

                </tr>

            </thead>

            <tbody>

                @forelse($project->attachments as $attachment)
                    <tr>

                        <td>

                            {{ $attachment->file_name }}

                        </td>

                        <td>

                            {{ ucfirst($attachment->uploaded_by) }}

                        </td>

                        <td>

                            {{ number_format($attachment->file_size / 1024, 2) }} KB

                        </td>

                        <td>

                            <a href="{{ route('admin.projects.attachments.download', [$project, $attachment]) }}"
                                class="btn btn-success btn-sm">

                                Download

                            </a>

                        </td>

                    </tr>

                @empty

                    <tr>

                        <td colspan="4">

                            No Attachment Found

                        </td>

                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

</div>
