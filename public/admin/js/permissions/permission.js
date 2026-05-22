$(document).ready(function () {

    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let permissionModal = new bootstrap.Modal(
        document.getElementById('permissionModal')
    );

    function loadPermissionTable() {
        $('#permissionTable').load(location.href + ' #permissionTable > *');
    }

    $(document).on('click', '#addPermissionBtn', function () {

        $.ajax({

            url: '/admin/permissions/create',

            type: 'GET',

            beforeSend: function () {

                $('#permissionModalContent').html(`

                    <div class="p-5 text-center">

                        <div class="spinner-border text-primary"></div>

                        <div class="mt-2">
                            Loading...
                        </div>

                    </div>

                `);

                permissionModal.show();

            },

            success: function (response) {

                $('#permissionModalContent').html(response);

            },

            error: function () {

                toastr.error('Failed to load form');

            }

        });

    });

    $(document).on('keyup', '#permissionName', function () {

        let value = $(this).val();

        let slug = value
            .toLowerCase()
            .replace(/\s+/g, '.');

        $('#permissionSlug').val(slug);

    });

    $(document).on('click', '.editPermissionBtn', function () {

        let url = $(this).data('url');

        $.ajax({

            url: url,

            type: 'GET',

            beforeSend: function () {

                $('#permissionModalContent').html(`

                    <div class="p-5 text-center">

                        <div class="spinner-border text-primary"></div>

                        <div class="mt-2">
                            Loading...
                        </div>

                    </div>

                `);

                permissionModal.show();

            },

            success: function (response) {

                $('#permissionModalContent').html(response);

            },

            error: function () {

                toastr.error('Failed to load edit form');

            }

        });

    });

    $(document).on('submit', '#permissionForm', function (e) {

        e.preventDefault();

        let form = $(this);

        let url = form.attr('action');

        let method = form.find('input[name="_method"]').val() || 'POST';

        let submitBtn = form.find('button[type="submit"]');

        $.ajax({

            url: url,

            type: method,

            data: form.serialize(),

            beforeSend: function () {

                submitBtn.prop('disabled', true);

                submitBtn.html(`

                    <span class="spinner-border spinner-border-sm"></span>
                    Processing...

                `);

                form.find('.text-danger.validation-error').remove();

                form.find('.is-invalid').removeClass('is-invalid');

            },

            success: function (response) {

                permissionModal.hide();

                loadPermissionTable();

                toastr.success(response.message);

            },

            error: function (xhr) {

                submitBtn.prop('disabled', false);

                submitBtn.html('Save');

                if (xhr.status === 422) {

                    let errors = xhr.responseJSON.errors;

                    $.each(errors, function (key, value) {

                        let input = form.find('[name="' + key + '"]');

                        input.addClass('is-invalid');

                        input.after(`
                            <small class="text-danger validation-error">
                                ${value[0]}
                            </small>
                        `);

                    });

                } else {

                    toastr.error('Something went wrong');

                }

            }

        });

    });

    $(document).on('click', '.deletePermissionBtn', function () {

        let url = $(this).data('url');

        Swal.fire({

            title: 'Are you sure?',

            text: "This permission will be deleted.",

            icon: 'warning',

            showCancelButton: true,

            confirmButtonText: 'Yes, Delete',

            cancelButtonText: 'Cancel'

        }).then((result) => {

            if (result.isConfirmed) {

                $.ajax({

                    url: url,

                    type: 'DELETE',

                    beforeSend: function () {

                        toastr.info('Deleting permission...');

                    },

                    success: function (response) {

                        loadPermissionTable();

                        toastr.success(response.message);

                    },

                    error: function () {

                        toastr.error('Delete failed');

                    }

                });

            }

        });

    });

});