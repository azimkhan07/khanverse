<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Twitter Card

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Twitter Title</label>

            <input type="text" name="twitter_title" class="form-control"
                value="{{ old('twitter_title', $seo->twitter_title ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Twitter Description</label>

            <textarea name="twitter_description" rows="4" class="form-control">{{ old('twitter_description', $seo->twitter_description ?? '') }}</textarea>

        </div>

        <div class="mb-3">

            <label>Twitter Image</label>

            <input type="file" name="twitter_image" class="form-control">

        </div>

        @if (!empty($seo->twitter_image))
            <img src="{{ asset('storage/' . $seo->twitter_image) }}" class="img-thumbnail" width="120">
        @endif

    </div>

</div>
