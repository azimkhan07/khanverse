<div class="card shadow-sm mb-4">

    <div class="card-body">

        <form method="GET">

            <div class="row">

                <div class="col-md-5">

                    <input
                        type="text"
                        class="form-control"
                        name="search"
                        value="{{ request('search') }}"
                        placeholder="Search Section">

                </div>

                <div class="col-md-3">

                    <select
                        class="form-control"
                        name="status">

                        <option value="">All Status</option>

                        <option value="1"
                            @selected(request('status')=='1')>

                            Active

                        </option>

                        <option value="0"
                            @selected(request('status')=='0')>

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

                    <a
                        href="{{ route('admin.website.homepage.create') }}"
                        class="btn btn-success w-100">

                        <i class="fa fa-plus"></i>

                        Add

                    </a>

                </div>

            </div>

        </form>

    </div>

</div>
