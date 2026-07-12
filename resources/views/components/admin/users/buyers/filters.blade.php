<div class="card mb-3">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-4">

                    <input type="text" name="search" class="form-control" placeholder="Search Buyer"
                        value="{{ request('search') }}">

                </div>

                <div class="col-md-3">

                    <select class="form-control" name="status">

                        <option value="">Status</option>

                        <option value="1" @selected(request('status') == '1')>Active</option>

                        <option value="0" @selected(request('status') == '0')>Inactive</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <select class="form-control" name="verified">

                        <option value="">Verification</option>

                        <option value="1">Verified</option>

                        <option value="0">Not Verified</option>

                    </select>

                </div>

                <div class="col-md-2">

                    <button class="btn btn-primary w-100">

                        Search

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
