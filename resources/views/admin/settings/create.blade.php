@extends('layouts.admin')

@section('content')
    <div class="page-body">

        <div class="custom-card">

            <div class="card-header">

                <h5>
                    Add Setting
                </h5>

            </div>

            <div class="card-body">

                <form action="{{ route('admin.settings.store') }}" method="POST">

                    @csrf

                    <input type="hidden" name="group" value="{{ $group }}">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Key</label>

                                <input type="text" name="key" class="form-control">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Type</label>

                                <select name="type" class="form-control">

                                    <option value="text">Text</option>
                                    <option value="textarea">Textarea</option>
                                    <option value="number">Number</option>
                                    <option value="image">Image</option>

                                </select>

                            </div>

                        </div>

                        <div class="col-md-12">

                            <div class="form-group">

                                <label>Value</label>

                                <textarea name="value" class="form-control"></textarea>

                            </div>

                        </div>

                    </div>

                    <button type="submit" class="btn btn-primary">

                        Save Setting

                    </button>

                </form>

            </div>

        </div>

    </div>
@endsection