<div class="card shadow-sm border-0">

    <div class="card-header">

        <h5>

            Seller

        </h5>

    </div>

    <div class="card-body">

        <p><strong>Name :</strong> {{ $project->seller?->name ?? '-' }}</p>

        <p><strong>Email :</strong> {{ $project->seller?->email ?? '-' }}</p>

        <p><strong>ID :</strong> {{ $project->seller?->id ?? '-' }}</p>

    </div>

</div>
