@props(['id', 'name', 'label' => '', 'value' => ''])

<div class="form-group mb-3">

    @if ($label)
        <label for="{{ $id }}" class="form-label">
            {{ $label }}
        </label>
    @endif

    <textarea id="{{ $id }}" name="{{ $name }}" class="form-control" rows="10">{{ old($name, $value) }}</textarea>

</div>

@push('scripts')
    <script>
        (function() {

            function initEditor() {

                if (typeof tinymce === 'undefined') {
                    console.error('TinyMCE not loaded');
                    return;
                }

                if (tinymce.get('{{ $id }}')) {
                    tinymce.get('{{ $id }}').remove();
                }

                tinymce.init({

                    selector: '#{{ $id }}',

                    license_key: 'gpl',

                    height: 500,

                    menubar: true,

                    branding: false,

                    promotion: false,

                    resize: true,

                    plugins: [
                        'advlist',
                        'autolink',
                        'lists',
                        'link',
                        'image',
                        'charmap',
                        'preview',
                        'anchor',
                        'searchreplace',
                        'visualblocks',
                        'code',
                        'fullscreen',
                        'insertdatetime',
                        'media',
                        'table',
                        'wordcount'
                    ],

                    toolbar: 'undo redo | styles | ' +
                        'bold italic underline strikethrough | ' +
                        'forecolor backcolor | ' +
                        'alignleft aligncenter alignright alignjustify | ' +
                        'bullist numlist outdent indent | ' +
                        'link image media table | ' +
                        'code preview fullscreen',

                    content_style: `
                body{
                    font-family:Arial,sans-serif;
                    font-size:15px;
                    padding:15px;
                }
            `

                });

            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initEditor);
            } else {
                initEditor();
            }

        })();
    </script>
@endpush
