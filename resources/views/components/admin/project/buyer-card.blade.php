<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5>

            Buyer

        </h5>

    </div>

    <div class="card-body">

        <p><strong>Name :</strong> {{ $project->buyer?->name ?? '-' }}</p>

        <p><strong>Email :</strong> {{ $project->buyer?->email ?? '-' }}</p>

        <p><strong>ID :</strong> {{ $project->buyer?->id ?? '-' }}</p>

    </div>

</div>
