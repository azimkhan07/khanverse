<div class="card">

    <div class="card-header d-flex justify-content-between">

        <h5>{{ $title }}</h5>

        @if (isset($createUrl))
            <button class="btn btn-primary openModalBtn" data-url="{{ $createUrl }}">
                Add {{ $title }}
            </button>
        @endif

    </div>

    <div class="card-body">

        {{ $slot }}

    </div>

</div>