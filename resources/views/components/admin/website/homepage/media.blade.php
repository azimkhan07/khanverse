<div class="card shadow-sm mb-4">

    <div class="card-header">

        Media

    </div>

    <div class="card-body">

        {{-- Image --}}

        <div class="mb-3">

            <label>Image</label>

            <input type="file" name="image" id="image" class="form-control">

        </div>

        <div class="mt-2">

            <img id="image-preview" src="{{ !empty($homepage->image) ? asset('storage/' . $homepage->image) : '' }}"
                class="img-thumbnail" width="180" style="{{ !empty($homepage->image) ? '' : 'display:none;' }}">

        </div>


        {{-- Background Image --}}

        <div class="mt-4">

            <label>Background Image</label>

            <input type="file" name="background_image" id="background_image" class="form-control">

        </div>

        <div class="mt-2">

            <img id="background-preview"
                src="{{ !empty($homepage->background_image) ? asset('storage/' . $homepage->background_image) : '' }}"
                class="img-thumbnail" width="180"
                style="{{ !empty($homepage->background_image) ? '' : 'display:none;' }}">

        </div>


        {{-- Icon --}}

        <div class="mt-4">

            <label>Icon</label>

            <input type="text" name="icon" class="form-control" value="{{ old('icon', $homepage->icon ?? '') }}"
                placeholder="fas fa-star">

        </div>

    </div>


    @push('scripts')
        <script>
            function previewImage(inputId, previewId) {
                const input = document.getElementById(inputId);

                if (!input) return;

                input.addEventListener('change', function() {

                    if (!this.files.length) return;

                    const reader = new FileReader();

                    reader.onload = function(e) {

                        const preview = document.getElementById(previewId);

                        preview.src = e.target.result;

                        preview.style.display = 'block';

                    };

                    reader.readAsDataURL(this.files[0]);

                });
            }

            previewImage('image', 'image-preview');

            previewImage('background_image', 'background-preview');
        </script>
    @endpush

</div>
