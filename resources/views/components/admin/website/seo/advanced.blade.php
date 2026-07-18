<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5>

            Advanced SEO

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Canonical URL</label>

            <input type="url" name="canonical_url" class="form-control"
                value="{{ old('canonical_url', $seo->canonical_url ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Robots</label>

            <select name="robots" class="form-control">

                <option value="index,follow"
                    {{ old('robots', $seo->robots ?? 'index,follow') == 'index,follow' ? 'selected' : '' }}>

                    index,follow

                </option>

                <option value="noindex,nofollow"
                    {{ old('robots', $seo->robots ?? '') == 'noindex,nofollow' ? 'selected' : '' }}>

                    noindex,nofollow

                </option>

                <option value="index,nofollow" {{ old('robots', $seo->robots ?? '') == 'index,nofollow' ? 'selected' : '' }}>

                    index,nofollow

                </option>

                <option value="noindex,follow" {{ old('robots', $seo->robots ?? '') == 'noindex,follow' ? 'selected' : '' }}>

                    noindex,follow

                </option>

            </select>

        </div>

    </div>

</div>
