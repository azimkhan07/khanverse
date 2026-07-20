<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Banner Information

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Title</label>

            <input type="text" name="title" class="form-control" value="{{ old('title', $banner->title ?? '') }}"
                required>

        </div>

        <div class="mb-3">

            <label>Link</label>

            <input type="url" name="link" class="form-control" value="{{ old('link', $banner->link ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Position</label>

            <select name="position" class="form-control">

                <option value="homepage" {{ old('position', $banner->position ?? '') == 'homepage' ? 'selected' : '' }}>
                    Homepage Hero
                </option>

                <option value="homepage_middle"
                    {{ old('position', $banner->position ?? '') == 'homepage_middle' ? 'selected' : '' }}>
                    Homepage Middle
                </option>

                <option value="homepage_bottom"
                    {{ old('position', $banner->position ?? '') == 'homepage_bottom' ? 'selected' : '' }}>
                    Homepage Bottom
                </option>

                <option value="category" {{ old('position', $banner->position ?? '') == 'category' ? 'selected' : '' }}>
                    Category
                </option>

                <option value="service" {{ old('position', $banner->position ?? '') == 'service' ? 'selected' : '' }}>
                    Service
                </option>

                <option value="promotion" {{ old('position', $banner->position ?? '') == 'promotion' ? 'selected' : '' }}>
                    Promotion
                </option>

            </select>

        </div>

        <div class="mb-3">

            <label>Banner Image</label>

            <input type="file" name="image" class="form-control">

        </div>

        @if (!empty($banner->image))
            <img src="{{ asset('storage/' . $banner->image) }}" class="img-thumbnail" width="140">
        @endif

    </div>

</div>
