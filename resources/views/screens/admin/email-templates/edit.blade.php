@extends('layouts.admin.master')
@section('title', __('Edit email template'))

@section('content')
    <div class="container-fluid user-list-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header card-no-border d-flex flex-wrap justify-content-between align-items-center gap-2">
                        <div>
                            <h5 class="mb-1 f-w-600">{{ $template->name }}</h5>
                            <p class="mb-0 c-o-light"><code>{{ $template->slug }}</code></p>
                        </div>
                        <a href="{{ route('email-templates.index') }}" class="btn btn-light btn-sm">
                            <i class="fa-solid fa-arrow-left pe-1"></i>{{ __('Back to templates') }}
                        </a>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('email-templates.update', $template) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="subject" class="form-label">{{ __('Subject') }}</label>
                                <input
                                    type="text"
                                    id="subject"
                                    name="subject"
                                    class="form-control @error('subject') is-invalid @enderror"
                                    value="{{ old('subject', $template->subject) }}"
                                    required
                                >
                                @error('subject')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="body" class="form-label">{{ __('Body (HTML allowed)') }}</label>
                                <textarea
                                    id="body"
                                    name="body"
                                    rows="14"
                                    class="form-control @error('body') is-invalid @enderror"
                                    required
                                >{{ old('body', $template->body) }}</textarea>
                                @error('body')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4 form-check">
                                <input
                                    type="checkbox"
                                    class="form-check-input"
                                    id="is_active"
                                    name="is_active"
                                    value="1"
                                    @checked(old('is_active', $template->is_active))
                                >
                                <label class="form-check-label" for="is_active">{{ __('Active (send this email)') }}</label>
                            </div>

                            <div class="mb-4">
                                <h6 class="f-w-600">{{ __('Available placeholders') }}</h6>
                                <p class="c-o-light mb-2">{{ __('Use these tags in the subject or body. They will be replaced when the email is sent.') }}</p>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach ($placeholders as $placeholder)
                                        <code class="badge badge-light-primary">{{ $placeholder }}</code>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Save template') }}</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
