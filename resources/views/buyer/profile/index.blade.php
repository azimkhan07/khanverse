@extends('buyer.layouts.app')

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">

            <div>

                <h3 class="mb-1">
                    My Profile
                </h3>

                <p class="text-muted mb-0">
                    Manage your account information.
                </p>

            </div>

        </div>

        @if (session('success'))
            <div class="alert alert-success">

                {{ session('success') }}

            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-danger">

                {{ session('error') }}

            </div>
        @endif

        <form action="{{ route('buyer.profile.update') }}" method="POST" enctype="multipart/form-data">

            @csrf

            <div class="row">

                <!-- LEFT -->

                <div class="col-lg-4">

                    <div class="card shadow-sm border-0 mb-4">

                        <div class="card-body text-center">

                            @php

                                $image = $buyer->profile_image
                                    ? asset('storage/' . $buyer->profile_image)
                                    : asset('assets/images/default-user.png');

                            @endphp

                            <img src="{{ $image }}" class="rounded-circle border mb-3"
                                style="width:140px;height:140px;object-fit:cover;">

                            <h5>

                                {{ $buyer->full_name }}

                            </h5>

                            <small class="text-muted">

                                Buyer

                            </small>

                            <hr>

                            <div class="mb-3 text-start">

                                <label class="form-label">

                                    Profile Image

                                </label>

                                <input type="file" class="form-control" name="profile_image">

                            </div>

                        </div>

                    </div>

                </div>

                <!-- RIGHT -->

                <div class="col-lg-8">

                    <div class="card shadow-sm border-0">

                        <div class="card-header">

                            <strong>

                                Basic Information

                            </strong>

                        </div>

                        <div class="card-body">

                            <div class="row">

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Full Name

                                    </label>

                                    <input type="text" name="full_name" class="form-control"
                                        value="{{ old('full_name', $buyer->full_name) }}">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Company Name

                                    </label>

                                    <input type="text" name="company_name" class="form-control"
                                        value="{{ old('company_name', $buyer->company_name) }}">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Email

                                    </label>

                                    <input type="email" class="form-control" value="{{ Auth::user()->email }}" readonly>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Phone

                                    </label>

                                    <input type="text" name="phone" class="form-control"
                                        value="{{ old('phone', optional($buyer->profile)->phone) }}">

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Gender

                                    </label>

                                    <select class="form-select" name="gender">

                                        <option value="">

                                            Select Gender

                                        </option>

                                        <option value="male"
                                            {{ optional($buyer->profile)->gender == 'male' ? 'selected' : '' }}>

                                            Male

                                        </option>

                                        <option value="female"
                                            {{ optional($buyer->profile)->gender == 'female' ? 'selected' : '' }}>

                                            Female

                                        </option>

                                        <option value="other"
                                            {{ optional($buyer->profile)->gender == 'other' ? 'selected' : '' }}>

                                            Other

                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-6 mb-3">

                                    <label class="form-label">

                                        Date Of Birth

                                    </label>

                                    <input type="date" class="form-control" name="dob"
                                        value="{{ optional($buyer->profile)->dob ? optional($buyer->profile)->dob->format('Y-m-d') : '' }}">

                                </div>

                                <hr>

                                <div class="col-12 mb-3">

                                    <h5>

                                        Address Information

                                    </h5>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        Country

                                    </label>

                                    <select name="country_id" id="country_id" class="form-select">

                                        <option value="">

                                            Select Country

                                        </option>

                                        @foreach ($countries as $country)
                                            <option value="{{ $country->id }}"
                                                {{ optional($buyer->profile)->country_id == $country->id ? 'selected' : '' }}>

                                                {{ $country->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        State

                                    </label>

                                    <select name="state_id" id="state_id" class="form-select">

                                        <option value="">

                                            Select State

                                        </option>

                                        @foreach ($states as $state)
                                            <option value="{{ $state->id }}"
                                                {{ optional($buyer->profile)->state_id == $state->id ? 'selected' : '' }}>

                                                {{ $state->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        City

                                    </label>

                                    <select name="city_id" id="city_id" class="form-select">

                                        <option value="">

                                            Select City

                                        </option>

                                        @foreach ($cities as $city)
                                            <option value="{{ $city->id }}"
                                                {{ optional($buyer->profile)->city_id == $city->id ? 'selected' : '' }}>

                                                {{ $city->name }}

                                            </option>
                                        @endforeach

                                    </select>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        Postal Code

                                    </label>

                                    <input type="text" class="form-control" name="postal_code"
                                        value="{{ old('postal_code', optional($buyer->profile)->postal_code) }}">

                                </div>

                                <div class="col-md-8 mb-3">

                                    <label class="form-label">

                                        Address

                                    </label>

                                    <textarea name="address" rows="2" class="form-control">{{ old('address', optional($buyer->profile)->address) }}</textarea>

                                </div>

                                <div class="col-12 mb-3">

                                    <label class="form-label">

                                        About Me

                                    </label>

                                    <textarea name="bio" rows="5" class="form-control" placeholder="Write something about yourself...">{{ old('bio', optional($buyer->profile)->bio) }}</textarea>

                                </div>

                                <hr>

                                <div class="col-12 mb-3">

                                    <h5>

                                        Notification Settings

                                    </h5>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <div class="form-check">

                                        <input class="form-check-input" type="checkbox" name="email_notifications"
                                            value="1" id="email_notifications"
                                            {{ optional($buyer->profile)->email_notifications ? 'checked' : '' }}>

                                        <label class="form-check-label" for="email_notifications">

                                            Email Notifications

                                        </label>

                                    </div>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <div class="form-check">

                                        <input class="form-check-input" type="checkbox" name="push_notifications"
                                            value="1" id="push_notifications"
                                            {{ optional($buyer->profile)->push_notifications ? 'checked' : '' }}>

                                        <label class="form-check-label" for="push_notifications">

                                            Push Notifications

                                        </label>

                                    </div>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <div class="form-check">

                                        <input class="form-check-input" type="checkbox" name="sms_notifications"
                                            value="1" id="sms_notifications"
                                            {{ optional($buyer->profile)->sms_notifications ? 'checked' : '' }}>

                                        <label class="form-check-label" for="sms_notifications">

                                            SMS Notifications

                                        </label>

                                    </div>

                                </div>

                                <hr>

                                <div class="col-12 mb-3">

                                    <h5>

                                        Privacy Settings

                                    </h5>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <label class="form-label">

                                        Profile Visibility

                                    </label>

                                    <select name="profile_visibility" class="form-select">

                                        <option value="public"
                                            {{ optional($buyer->profile)->profile_visibility == 'public' ? 'selected' : '' }}>

                                            Public

                                        </option>

                                        <option value="private"
                                            {{ optional($buyer->profile)->profile_visibility == 'private' ? 'selected' : '' }}>

                                            Private

                                        </option>

                                    </select>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <div class="form-check mt-4">

                                        <input class="form-check-input" type="checkbox" name="show_email" value="1"
                                            id="show_email" {{ optional($buyer->profile)->show_email ? 'checked' : '' }}>

                                        <label class="form-check-label" for="show_email">

                                            Show Email

                                        </label>

                                    </div>

                                </div>

                                <div class="col-md-4 mb-3">

                                    <div class="form-check mt-4">

                                        <input class="form-check-input" type="checkbox" name="show_phone" value="1"
                                            id="show_phone" {{ optional($buyer->profile)->show_phone ? 'checked' : '' }}>

                                        <label class="form-check-label" for="show_phone">

                                            Show Phone

                                        </label>

                                    </div>

                                </div>

                                <div class="col-12 mt-3">

                                    <button class="btn btn-primary">

                                        <i class="ti-save"></i>

                                        Update Profile

                                    </button>

                                </div>

                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </form>

        <div class="card shadow-sm border-0 mt-4">

            <div class="card-header">

                <strong>

                    Change Password

                </strong>

            </div>

            <div class="card-body">

                <form action="{{ route('buyer.profile.password') }}" method="POST">

                    @csrf

                    <div class="row">

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Current Password

                            </label>

                            <input type="password" name="current_password" class="form-control">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                New Password

                            </label>

                            <input type="password" name="password" class="form-control">

                        </div>

                        <div class="col-md-4 mb-3">

                            <label class="form-label">

                                Confirm Password

                            </label>

                            <input type="password" name="password_confirmation" class="form-control">

                        </div>

                        <div class="col-12">

                            <button class="btn btn-success">

                                <i class="ti-lock"></i>

                                Change Password

                            </button>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {

            $('#country_id').change(function() {
                let countryId = $(this).val();
                $('#state_id').html('<option value="">Loading...</option>');
                $('#city_id').html('<option value="">Select City</option>');
                if (countryId != '') {
                    $.get("{{ url('buyer/profile/states') }}/" + countryId, function(states) {
                        let options = '<option value="">Select State</option>';
                        $.each(states, function(index, state) {
                            options += '<option value="' + state.id + '">' + state.name + '</option>';
                        });
                        $('#state_id').html(options);
                    });
                } else {
                    $('#state_id').html('<option value="">Select State</option>');
                }
            });

            $('#state_id').change(function() {
                let stateId = $(this).val();
                $('#city_id').html('<option value="">Loading...</option>');
                if (stateId != '') {
                    $.get("{{ url('buyer/profile/cities') }}/" + stateId, function(cities) {
                        let options = '<option value="">Select City</option>';
                        $.each(cities, function(index, city) {
                            options += '<option value="' + city.id + '">' + city.name + '</option>';
                        });
                        $('#city_id').html(options);
                    });
                } else {
                    $('#city_id').html('<option value="">Select City</option>');
                }
            });
        });
    </script>
@endpush
