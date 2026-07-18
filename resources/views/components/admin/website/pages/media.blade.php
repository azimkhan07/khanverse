<div class="card shadow-sm mb-4">

    <div class="card-header">

        Media

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Banner Image</label>

            <input type="file" name="banner_image" class="form-control" accept="image/*"
                onchange="previewImage(this,'bannerPreview')">

        </div>

        <img id="bannerPreview" src="{{ !empty($page->banner_image) ? asset('storage/' . $page->banner_image) : '' }}"
            style="max-width:100%;{{ empty($page->banner_image) ? 'display:none;' : '' }}" class="img-thumbnail">

    </div>

</div>

<script>
    function previewImage(input, id) {

        if (input.files && input.files[0]) {

            let reader = new FileReader();

            reader.onload = function(e) {

                let img = document.getElementById(id);

                img.src = e.target.result;

                img.style.display = 'block';

            }

            reader.readAsDataURL(input.files[0]);

        }

    }
</script>
