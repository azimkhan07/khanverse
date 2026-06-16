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

    let btn = form.find('button[type="submit"]');

    let oldText = btn.html();

    btn.prop('disabled', true);
    btn.html('Please Wait...');

    $.ajax({

        url: form.attr('action'),

        type: form.attr('method'),

        data: form.serialize(),

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

                }, 800);
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


/*
|--------------------------------------------------------------------------
| DELETE RECORD
|--------------------------------------------------------------------------
*/

$(document).on('click', '.deleteBtn', function () {

    let url = $(this).data('url');

    if (!confirm('Are you sure?')) {

        return false;
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

            }, 800);

        }

    });

});