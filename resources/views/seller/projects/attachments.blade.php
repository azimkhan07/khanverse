@extends('seller.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">

                    Project Files

                </h3>

                <small class="text-muted">

                    {{ $project->title }}

                </small>

            </div>

            <div>

                <a href="{{ route('seller.projects.show', $project->id) }}" class="btn btn-secondary">

                    <i class="ti-arrow-left"></i>

                    Back

                </a>

            </div>

        </div>

        <div class="card shadow-sm border-0 mb-4">

            <div class="card-header">

                <strong>

                    Upload Attachment

                </strong>

            </div>

            <div class="card-body">

                <form action="{{ route('seller.projects.attachments.upload', $project->id) }}" method="POST"
                    enctype="multipart/form-data">

                    @csrf

                    <div class="row">

                        <div class="col-md-9">

                            <input type="file" name="file" class="form-control" required>

                        </div>

                        <div class="col-md-3">

                            <button class="btn btn-primary w-100">

                                <i class="ti-upload"></i>

                                Upload

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>



        <div class="card shadow-sm border-0">

            <div class="card-header">

                <strong>

                    Uploaded Files

                </strong>

            </div>

            <div class="card-body p-0">

                <div class="table-responsive">

                    <table class="table table-hover mb-0">

                        <thead>

                            <tr>

                                <th>#</th>

                                <th>File</th>

                                <th>Size</th>

                                <th>Uploaded By</th>

                                <th>Date</th>

                                <th width="150">

                                    Action

                                </th>

                            </tr>

                        </thead>

                        <tbody>

                            @forelse($project->attachments as $attachment)
                                <tr>

                                    <td>

                                        {{ $loop->iteration }}

                                    </td>

                                    <td>

                                        <div class="d-flex align-items-center">

                                            <i class="ti-file text-primary me-2"></i>

                                            <div>

                                                <strong>

                                                    {{ $attachment->file_name }}

                                                </strong>

                                                <br>

                                                <small class="text-muted">

                                                    {{ $attachment->mime_type }}

                                                </small>

                                            </div>

                                        </div>

                                    </td>

                                    <td>

                                        {{ number_format($attachment->file_size / 1024, 2) }} KB

                                    </td>

                                    <td>

                                        <span class="badge bg-info">

                                            {{ ucfirst($attachment->uploaded_by) }}

                                        </span>

                                    </td>

                                    <td>

                                        {{ $attachment->created_at->format('d M Y') }}

                                        <br>

                                        <small class="text-muted">

                                            {{ $attachment->created_at->format('h:i A') }}

                                        </small>

                                    </td>

                                    <td>

                                        <div class="btn-group">

                                            <a href="{{ route('seller.projects.attachments.download', $attachment->id) }}"
                                                class="btn btn-sm btn-success">

                                                <i class="ti-download"></i>

                                            </a>

                                            <form
                                                action="{{ route('seller.projects.attachments.delete', $attachment->id) }}"
                                                method="POST" onsubmit="return confirm('Delete this file?')">

                                                @csrf

                                                @method('DELETE')

                                                <button class="btn btn-sm btn-danger">

                                                    <i class="ti-trash"></i>

                                                </button>

                                            </form>

                                        </div>

                                    </td>

                                </tr>

                            @empty

                                <tr>

                                    <td colspan="6" class="text-center py-5">

                                        <i class="ti-folder display-4 text-muted"></i>

                                        <h5 class="mt-3">

                                            No Attachments Found

                                        </h5>

                                        <p class="text-muted">

                                            Upload the first project file.

                                        </p>

                                    </td>

                                </tr>
                            @endforelse

                        </tbody>

                    </table>

                </div>

            </div>

        </div>

    </div>
@endsection
