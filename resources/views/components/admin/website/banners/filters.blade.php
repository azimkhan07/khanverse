<div class="card mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-5">

                    <input type="text" name="search" class="form-control" placeholder="Search Banner..."
                        value="{{ request('search') }}">

                </div>

                <div class="col-md-3">

                    <select name="status" class="form-control">

                        <option value="">All Status</option>

                        <option value="1" {{ request('status') == '1' ? 'selected' : '' }}>
                            Active
                        </option>

                        <option value="0" {{ request('status') == '0' ? 'selected' : '' }}>
                            Inactive
                        </option>

                    </select>

                </div>

                <div class="col-md-2">

                    <button class="btn btn-primary w-100">

                        Search

                    </button>

                </div>

                <div class="col-md-2">

                    <a href="{{ route('admin.website.banners.index') }}" class="btn btn-secondary w-100">

                        Reset

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>
