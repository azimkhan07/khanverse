<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Basic SEO

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label>Page</label>

            <select name="page_key" class="form-control" required>

                <option value="">Select Page</option>

                <option value="homepage" {{ old('page_key', $seo->page_key ?? '') == 'homepage' ? 'selected' : '' }}>Homepage
                </option>

                <option value="about" {{ old('page_key', $seo->page_key ?? '') == 'about' ? 'selected' : '' }}>About Us
                </option>

                <option value="contact" {{ old('page_key', $seo->page_key ?? '') == 'contact' ? 'selected' : '' }}>Contact Us
                </option>

                <option value="faq" {{ old('page_key', $seo->page_key ?? '') == 'faq' ? 'selected' : '' }}>FAQ</option>

                <option value="services" {{ old('page_key', $seo->page_key ?? '') == 'services' ? 'selected' : '' }}>Services
                </option>

                <option value="categories" {{ old('page_key', $seo->page_key ?? '') == 'categories' ? 'selected' : '' }}>
                    Categories</option>

            </select>

        </div>

        <div class="mb-3">

            <label>Meta Title</label>

            <input type="text" name="meta_title" class="form-control"
                value="{{ old('meta_title', $seo->meta_title ?? '') }}">

        </div>

        <div class="mb-3">

            <label>Meta Description</label>

            <textarea name="meta_description" rows="4" class="form-control">{{ old('meta_description', $seo->meta_description ?? '') }}</textarea>

        </div>

        <div class="mb-3">

            <label>Meta Keywords</label>

            <textarea name="meta_keywords" rows="3" class="form-control">{{ old('meta_keywords', $seo->meta_keywords ?? '') }}</textarea>

        </div>

    </div>

</div>
