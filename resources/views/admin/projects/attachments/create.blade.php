<div class="modal-header">

    <h5>Upload Attachment</h5>

    <button class="btn-close" data-bs-dismiss="modal"></button>

</div>

<form action="{{ route('admin.projects.attachments.store', $project) }}" method="POST" class="ajaxForm"
    enctype="multipart/form-data">

    @csrf

    <div class="modal-body">

        <div class="mb-3">

            <label>Attachment</label>

            <input type="file" name="file" class="form-control">

        </div>

    </div>

    <div class="modal-footer">

        <button class="btn btn-primary">

            Upload

        </button>

    </div>

</form>
