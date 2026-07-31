@extends('buyer.layouts.app')

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

                <a href="{{ route('buyer.projects.show', $project->id) }}" class="btn btn-secondary">

                    <i class="ti-arrow-left"></i>

                    Back

                </a>

            </div>

        </div>


        <div class="card shadow-sm border-0">

            <div class="card-header">

                <strong>

                    Files

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
                                @php
                                    $icon = 'ti-file';
                                    if (Str::contains($attachment->mime_type, 'pdf')) {
                                        $icon = 'ti-file';
                                    } elseif (Str::contains($attachment->mime_type, 'image')) {
                                        $icon = 'ti-image';
                                    } elseif (Str::contains($attachment->mime_type, 'zip')) {
                                        $icon = 'ti-archive';
                                    } elseif (Str::contains($attachment->mime_type, 'word')) {
                                        $icon = 'ti-write';
                                    }
                                @endphp

                                <tr>
                                    <td> {{ $loop->iteration }} </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <i class="{{ $icon }} text-primary me-2"></i>
                                            <div>
                                                <strong> {{ $attachment->file_name }} </strong>
                                                <br>
                                                <small class="text-muted"> {{ $attachment->mime_type }} </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @php
                                            $size = $attachment->file_size;
                                            if ($size >= 1048576) {
                                                $size = number_format($size / 1048576, 2) . ' MB';
                                            } else {
                                                $size = number_format($size / 1024, 2) . ' KB';
                                            }
                                        @endphp
                                        {{ $size }}
                                    </td>
                                    <td>
                                        @php
                                            $uploadedBy = [
                                                'seller' => 'Seller',
                                                'buyer' => 'Buyer',
                                                'admin' => 'Admin',
                                            ];
                                        @endphp
                                        <span class="badge bg-info"> {{ $uploadedBy[$attachment->uploaded_by] ?? ucfirst($attachment->uploaded_by) }} </span>
                                    </td>
                                    <td>
                                        {{ $attachment->created_at->format('d M Y') }}
                                        <br>
                                        <small class="text-muted"> {{ $attachment->created_at->format('h:i A') }} </small>
                                    </td>
                                    <td>
                                        <div class="btn-group">
                                            <a href="{{ route('buyer.projects.attachments.download', $attachment->id) }}"
                                                class="btn btn-success btn-sm">
                                                <i class="ti-download"></i>
                                                Download
                                            </a>
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
