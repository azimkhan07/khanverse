console.log('Master JS Loaded');

/*
|--------------------------------------------------------------------------
| OPEN MODAL
|--------------------------------------------------------------------------
*/

$(document).on('click', '.openModalBtn', function (e) {

    e.preventDefault();

    let url = $(this).data('url');

    $.ajax({

        url: url,

        type: 'GET',

        success: function (response) {

            $('#globalModalContent').html(response);

            let modal = new bootstrap.Modal(
                document.getElementById('globalModal')
            );

            modal.show();
        },

        error: function () {

            toastr.error('Unable to load form');
        }

    });

});


/*
|--------------------------------------------------------------------------
| AJAX FORM SUBMIT
|--------------------------------------------------------------------------
*/

$(document).on('submit', '.ajaxForm', function (e) {

    e.preventDefault();

    let form = $(this);

    let formData = new FormData(this);

    let btn = form.find('button[type="submit"]');

    let oldText = btn.html();

    btn.prop('disabled', true);

    btn.html('Please Wait...');

    $.ajax({

        url: form.attr('action'),

        type: form.attr('method'),

        data: formData,
        processData: false,
        contentType: false,

        success: function (response) {

            if (response.message) {

                toastr.success(response.message);
            }

            if (response.closeModal) {

                bootstrap.Modal
                    .getInstance(document.getElementById('globalModal'))
                    ?.hide();
            }

            if (response.reload) {

                setTimeout(function () {

                    location.reload();

                }, 500);
            }

        },

        error: function (xhr) {

            if (xhr.status === 422) {

                $.each(xhr.responseJSON.errors, function (key, value) {

                    toastr.error(value[0]);

                });

            } else {

                toastr.error('Something went wrong');
            }

        },

        complete: function () {

            btn.prop('disabled', false);

            btn.html(oldText);

        }

    });

});

// thumnails add remove code 

$(document).on('change', '#thumbnailInput', function () {

    let file = this.files[0];

    if (file) {

        let reader = new FileReader();

        reader.onload = function (e) {

            $('#thumbnailPreview').attr('src', e.target.result);

        }

        reader.readAsDataURL(file);
    }

});

$(document).on('click', '#removeThumbnail', function () {

    $('#thumbnailInput').val('');

    $('#thumbnailPreview').attr('src', '');

});

/*
|--------------------------------------------------------------------------
| IMAGE PREVIEW
|--------------------------------------------------------------------------
*/

$(document).on('change', '.imageInput', function () {

    let input = this;

    let wrapper = $(input).closest('.mb-3');

    let previewBox = wrapper.find('.imagePreviewWrapper');

    let preview = wrapper.find('.imagePreview');

    if (input.files && input.files[0]) {

        let reader = new FileReader();

        reader.onload = function (e) {

            preview.attr('src', e.target.result);

            previewBox.removeClass('d-none');

        }

        reader.readAsDataURL(input.files[0]);
    }

});


/*
|--------------------------------------------------------------------------
| REMOVE IMAGE PREVIEW
|--------------------------------------------------------------------------
*/

$(document).on('click', '.removeImageBtn', function () {

    let wrapper = $(this).closest('.mb-3');

    wrapper.find('.imageInput').val('');

    wrapper.find('.imagePreview').attr('src', '');

    wrapper.find('.imagePreviewWrapper').addClass('d-none');

});

/*
|--------------------------------------------------------------------------
| DELETE RECORD
|--------------------------------------------------------------------------
*/

$(document).on('click', '.deleteBtn', function () {

    let url = $(this).data('url');

    if (!confirm('Are you sure you want to delete this record?')) {

        return;
    }

    $.ajax({

        url: url,

        type: 'DELETE',

        data: {

            _token: $('meta[name="csrf-token"]').attr('content')

        },

        success: function (response) {

            toastr.success(response.message);

            setTimeout(function () {

                location.reload();

            }, 500);

        },

        error: function () {

            toastr.error('Something went wrong');

        }

    });

});


/*
|--------------------------------------------------------------------------
| STATUS TOGGLE
|--------------------------------------------------------------------------
*/

$(document).on('change', '.statusToggle', function () {

    let checkbox = $(this);

    let url = checkbox.data('url');

    let checked = checkbox.prop('checked');

    if (!confirm('Are you sure you want to change status?')) {

        checkbox.prop('checked', !checked);

        return;
    }

    $.ajax({

        url: url,

        type: 'POST',

        data: {

            _token: $('meta[name="csrf-token"]').attr('content')

        },

        success: function (response) {

            toastr.success(response.message);

        },

        error: function () {

            checkbox.prop('checked', !checked);

            toastr.error('Something went wrong');

        }

    });

});