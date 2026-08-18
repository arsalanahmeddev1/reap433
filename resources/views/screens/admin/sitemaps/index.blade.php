@extends('layouts.admin.master')
@section('title', __('Sitemap'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card">
                    <div class="card-header card-no-border">
                        <h5 class="mb-1 f-w-600">{{ __('Sitemap') }}</h5>
                        <p class="mb-0 c-o-light">
                            {{ __('Paste the complete sitemap XML. It will be served at') }}
                            <a href="{{ url('/sitemap.xml') }}" target="_blank" rel="noopener noreferrer">{{ url('/sitemap.xml') }}</a>
                        </p>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('sitemaps.update') }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="content" class="form-label">{{ __('Sitemap XML') }}</label>
                                <textarea
                                    id="content"
                                    name="content"
                                    rows="22"
                                    class="form-control font-monospace @error('content') is-invalid @enderror"
                                    placeholder="&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;&#10;&lt;urlset xmlns=&quot;http://www.sitemaps.org/schemas/sitemap/0.9&quot;&gt;&#10;  &lt;url&gt;&#10;    &lt;loc&gt;https://reapthreads.com/&lt;/loc&gt;&#10;  &lt;/url&gt;&#10;&lt;/urlset&gt;"
                                >{{ old('content', $sitemap->content) }}</textarea>
                                @error('content')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-floppy-disk pe-1"></i>{{ __('Save sitemap') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
