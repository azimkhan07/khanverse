<div class="card mb-3">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-3">

                    <input type="text" class="form-control" name="search" placeholder="Search Seller"
                        value="{{ request('search') }}">

                </div>

                <div class="col-md-2">

                    <select name="status" class="form-control">

                        <option value="">Status</option>

                        <option value="1">Active</option>

                        <option value="0">Inactive</option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select name="available" class="form-control">

                        <option value="">Available</option>

                        <option value="1">Yes</option>

                        <option value="0">No</option>

                    </select>

                </div>

                <div class="col-md-2">

                    <select name="verified" class="form-control">

                        <option value="">Verified</option>

                        <option value="1">Verified</option>

                        <option value="0">Pending</option>

                    </select>

                </div>

                <div class="col-md-3">

                    <button class="btn btn-primary">

                        Search

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
