<div class="modal-header">

    <h5 class="modal-title">

        Service Gallery -
        {{ $service->title }}

    </h5>

    <button type="button" class="btn-close" data-bs-dismiss="modal">
    </button>

</div>

<form action="{{ route('admin.services.gallery.store', $service->id) }}" method="POST" class="ajaxForm"
    enctype="multipart/form-data">

    @csrf

    <div class="modal-body">

        <div class="mb-3">

            <label>Upload Gallery Images</label>

            <input type="file" name="images[]" class="form-control" multiple accept="image/*">

        </div>

        <hr>

        <div class="row">

            @forelse($images as $image)
                <div class="col-md-3 mb-3">

                    <div class="card">

                        <img src="{{ asset('storage/' . $image->image) }}" class="card-img-top"
                            style="height:150px;object-fit:cover;">

                        <div class="card-body text-center">

                            <button type="button" class="btn btn-danger btn-sm deleteBtn"
                                data-url="{{ route('admin.services.gallery.delete', $image->id) }}">

                                Delete

                            </button>

                        </div>

                    </div>

                </div>

            @empty

                <div class="col-12">

                    <div class="alert alert-info">

                        No Gallery Images Found

                    </div>

                </div>
            @endforelse

        </div>

    </div>

    <div class="modal-footer">

        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">

            Close

        </button>

        <button type="submit" class="btn btn-primary">

            Upload Images

        </button>

    </div>

</form>