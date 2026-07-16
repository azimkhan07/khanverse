<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Basic Information

        </h5>

    </div>

    <div class="card-body">

        <div class="row">

            <div class="col-md-6">

                <div class="mb-3">

                    <label class="form-label">

                        Section

                    </label>

                    <select class="form-control" name="section_key" required>

                        <option value="">Select Section</option>

                        @foreach ($sections as $key => $title)
                            <option value="{{ $key }}"
                                {{ old('section_key', $homepage->section_key ?? '') == $key ? 'selected' : '' }}>

                                {{ $title }}

                            </option>
                        @endforeach

                    </select>

                </div>

            </div>

            <div class="col-md-6">

                <div class="mb-3">

                    <label class="form-label">

                        Title

                    </label>

                    <input type="text" name="title" class="form-control"
                        value="{{ old('title', $homepage->title ?? '') }}">

                </div>

            </div>

        </div>

        <div class="mb-3">

            <label class="form-label">

                Subtitle

            </label>

            <input type="text" name="subtitle" class="form-control"
                value="{{ old('subtitle', $homepage->subtitle ?? '') }}">

        </div>

        <div class="mb-3">

            <x-admin.editor id="description" name="description" label="Description" :value="$homepage->description ?? ''" />

        </div>

    </div>

</div>
