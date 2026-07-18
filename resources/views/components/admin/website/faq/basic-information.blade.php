<div class="card shadow-sm mb-4">

    <div class="card-header">

        <h5 class="mb-0">

            Basic Information

        </h5>

    </div>

    <div class="card-body">

        <div class="mb-3">

            <label class="form-label">

                Question

            </label>

            <input type="text" name="question" class="form-control" value="{{ old('question', $faq->question ?? '') }}"
                required>

        </div>

        <x-admin.editor id="answer" name="answer" label="Answer" :value="$faq->answer ?? ''" />

    </div>

</div>
