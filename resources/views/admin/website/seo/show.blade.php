@extends('layouts.admin')

@section('title','SEO Details')

@section('content')

<div class="container-fluid">

    <div class="card">

        <div class="card-body">

            <table class="table table-bordered">

                <tr>
                    <th>Page</th>
                    <td>{{ $seo->page_key }}</td>
                </tr>

                <tr>
                    <th>Meta Title</th>
                    <td>{{ $seo->meta_title }}</td>
                </tr>

                <tr>
                    <th>Meta Description</th>
                    <td>{{ $seo->meta_description }}</td>
                </tr>

                <tr>
                    <th>Keywords</th>
                    <td>{{ $seo->meta_keywords }}</td>
                </tr>

                <tr>
                    <th>Canonical URL</th>
                    <td>{{ $seo->canonical_url }}</td>
                </tr>

                <tr>
                    <th>Robots</th>
                    <td>{{ $seo->robots }}</td>
                </tr>

                <tr>
                    <th>Status</th>
                    <td>

                        @if($seo->status)

                            <span class="badge badge-success">

                                Active

                            </span>

                        @else

                            <span class="badge badge-danger">

                                Inactive

                            </span>

                        @endif

                    </td>

                </tr>

            </table>

            @if($seo->og_image)

                <h5>Open Graph Image</h5>

                <img
                    src="{{ asset('storage/'.$seo->og_image) }}"
                    width="180"
                    class="img-thumbnail">

            @endif

            @if($seo->twitter_image)

                <h5 class="mt-4">

                    Twitter Image

                </h5>

                <img
                    src="{{ asset('storage/'.$seo->twitter_image) }}"
                    width="180"
                    class="img-thumbnail">

            @endif

        </div>

    </div>

</div>

@endsection
