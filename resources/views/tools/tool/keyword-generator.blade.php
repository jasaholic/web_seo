@section('site_title', formatTitle([__('Keyword generator'), __('Tool'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('dashboard'), 'title' => __('Home')],
    ['url' => route('tools'), 'title' => __('Tools')],
    ['title' => __('Tool')],
]])

<div class="d-flex">
    <h1 class="h2 mb-3 text-break">{{ __('Keyword generator') }}</h1>
</div>

<div class="card border-0 shadow-sm">
    <div class="card-header align-items-center">
        <div class="row">
            <div class="col">
                <div class="font-weight-medium py-1">{{ __('Keyword generator') }}</div>
            </div>
        </div>
    </div>
    <div class="card-body">
        @include('shared.message')

        <form action="{{ route('tools.keyword_generator')  }}" method="post" enctype="multipart/form-data" id="keywords-generator-form">
            @csrf

            <div class="form-group">
                <label for="i-keyword">{{ __('Keyword') }}</label>
                <input type="text" name="keyword" id="i-keyword" class="form-control{{ $errors->has('keyword') ? ' is-invalid' : '' }}" value="{{ $keyword ?? (old('keyword') ?? '') }}">

                @if ($errors->has('keyword'))
                    <span class="invalid-feedback d-block" role="alert">
                        <strong>{{ $errors->first('keyword') }}</strong>
                    </span>
                @endif
            </div>

            <div class="row">
                <div class="col">
                    @if(config('settings.captcha_keyword_generator'))
                        {!! NoCaptcha::displaySubmit('keywords-generator-form', __('Send'), ['data-theme' => (Cookie::get('dark_mode') == 1 ? 'dark' : 'light'), 'data-size' => 'invisible', 'class' => 'btn ' . ($errors->has('g-recaptcha-response') ? 'btn-danger' : 'btn-primary')]) !!}

                        {!! NoCaptcha::renderJs(__('lang_code')) !!}
                    @else
                        <button type="submit" name="submit" class="btn btn-primary">{{ __('Search') }}</button>
                    @endif
                </div>
                <div class="col-auto">
                    <a href="{{ route('tools.keyword_generator') }}" class="btn btn-outline-secondary ml-auto">{{ __('Reset') }}</a>
                </div>
            </div>

            @if ($errors->has('g-recaptcha-response'))
                <span class="invalid-feedback d-block" role="alert">
                    <strong>{{ $errors->first('g-recaptcha-response') }}</strong>
                </span>
            @endif
        </form>
    </div>
</div>

@if(isset($keywords))
    <div class="card border-0 shadow-sm mt-3">
        <div class="card-header align-items-center">
            <div class="row">
                <div class="col">
                    <div class="font-weight-medium py-1">{{ __('Results') }}</div>
                </div>
            </div>
        </div>

        <div class="card-body">
            @if(empty($keywords))
                {{ __('No results found.') }}
            @else
                <div class="list-group list-group-flush my-n3">
                    <div class="list-group-item px-0 text-muted">
                        <div class="row align-items-center">
                            <div class="col">
                                <div class="row">
                                    <div class="col-12">{{ __('Keyword') }}</div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="form-row">
                                    <div class="col">
                                        <div class="invisible btn btn-sm btn-outline-primary">{{ __('Copy') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @foreach($keywords as $keyword)
                        <div class="list-group-item px-0">
                            <div class="row align-items-center">
                                <div class="col text-truncate">
                                    <div class="row text-truncate">
                                        <div class="col-12 col-lg-5 d-flex align-items-center text-truncate">
                                            {{ $keyword }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-auto">
                                    <div class="form-row">
                                        <div class="col">
                                            <div class="btn btn-sm btn-outline-primary keyword-copy" data-enable="tooltip-keyword" data-value="{{ $keyword }}" title="{{ __('Copy') }}" data-copy="{{ __('Copy') }}" data-copied="{{ __('Copied') }}" data-clipboard-target="#i-keyword-{{ Str::slug($keyword) }}">{{ __('Copy') }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
@endif

<script>
    'use strict';

    window.addEventListener('DOMContentLoaded', function () {
        jQuery('[data-enable="tooltip-keyword"]').tooltip({animation: true});

        document.querySelectorAll('[data-enable="tooltip-keyword"]').forEach(function (element) {
            element.addEventListener('click', function (e) {
                // Update the tooltip
                jQuery(this).tooltip('hide').attr('data-original-title', this.dataset.copied).tooltip('show');
            });

            element.addEventListener('mouseleave', function () {
                this.setAttribute('data-original-title', this.dataset.copy);
            });
        });

        document.querySelectorAll('.keyword-copy').forEach(function (element) {
            element.addEventListener('click', function (e) {
                e.preventDefault();

                try {
                    let value = this.dataset.value;
                    let tempInput = document.createElement('input');

                    document.body.append(tempInput);

                    // Set the input's value to the url to be copied
                    tempInput.value = value;

                    // Select the input's value to be copied
                    tempInput.select();

                    // Copy the url
                    document.execCommand("copy");

                    // Remove the temporary input
                    tempInput.remove();
                } catch (e) {}
            });
        });
    });
</script>