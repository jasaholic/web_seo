@section('site_title', formatTitle([__('Reports'), __('Settings'), config('settings.title')]))

@include('shared.breadcrumbs', ['breadcrumbs' => [
    ['url' => route('admin.dashboard'), 'title' => __('Admin')],
    ['title' => __('Settings')],
]])

<h1 class="h2 mb-3 d-inline-block">{{ __('Advanced') }}</h1>

<div class="card border-0 shadow-sm">
    <div class="card-header"><div class="font-weight-medium py-1">{{ __('Advanced') }}</div></div>
    <div class="card-body">

        <ul class="nav nav-pills d-flex flex-fill flex-column flex-md-row mb-3" id="pills-tab" role="tablist">
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link active" id="pills-general-tab" data-toggle="pill" href="#pills-general" role="tab" aria-controls="pills-general" aria-selected="true">{{ __('General') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-gsb-tab" data-toggle="pill" href="#pills-gsb" role="tab" aria-controls="pills-gsb" aria-selected="false">{{ __('Google Safe Browsing') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-gcs-tab" data-toggle="pill" href="#pills-gcs" role="tab" aria-controls="pills-gcs" aria-selected="false">{{ __('Google Custom Search') }}</a>
            </li>
            <li class="nav-item flex-grow-1 text-center">
                <a class="nav-link" id="pills-screenshot-tab" data-toggle="pill" href="#pills-screenshot" role="tab" aria-controls="pills-screenshot" aria-selected="false">{{ __('Screenshot') }}</a>
            </li>
        </ul>

        @include('shared.message')

        <form action="{{ route('admin.settings', 'report') }}" method="post" enctype="multipart/form-data">

            @csrf

            <div class="tab-content" id="pills-tabContent">
                <div class="tab-pane fade show active" id="pills-general" role="tabpanel" aria-labelledby="pills-general-tab">
                    <div class="form-group">
                        <label for="i-demo-url">{{ __(':name URL', ['name' => __('Demo')]) }}</label>
                        <input type="text" dir="ltr" name="demo_url" id="i-demo-url" class="form-control{{ $errors->has('demo_url') ? ' is-invalid' : '' }}" value="{{ old('settings.demo_url') ?? config('settings.demo_url') }}">
                        @if ($errors->has('demo_url'))
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $errors->first('demo_url') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-sitemap-links">{{ __('Sitemap links') }}</label>
                        <input type="number" name="sitemap_links" id="i-sitemap-links" class="form-control{{ $errors->has('sitemap_links') ? ' is-invalid' : '' }}" value="{{ old('sitemap_links') ?? config('settings.sitemap_links') }}">
                        @if ($errors->has('sitemap_links'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('sitemap_links') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-request-proxy">{{ __('Proxies') }}</label>
                        <textarea name="request_proxy" id="i-request-proxy" class="form-control{{ $errors->has('request_proxy') ? ' is-invalid' : '' }}" rows="3" placeholder="{{ __('One per line') }}
http://username:password@ip:port
">{{ config('settings.request_proxy') }}</textarea>
                        @if ($errors->has('request_proxy'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('request_proxy') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-bad-words">{{ __('Bad words') }}</label>
                        <textarea name="bad_words" id="i-bad-words" class="form-control{{ $errors->has('bad_words') ? ' is-invalid' : '' }}" rows="3" placeholder="{{ __('One per line') }}">{{ config('settings.bad_words') }}</textarea>
                        @if ($errors->has('bad_words'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('bad_words') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-gsb" role="tabpanel" aria-labelledby="pills-gsb-tab">
                    <div class="form-group">
                        <label for="i-gsb">{{ __('Enabled') }}</label>
                        <select name="gsb" id="i-gsb" class="custom-select{{ $errors->has('gsb') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('No'), 1 => __('Yes')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('gsb') !== null && old('gsb') == $key) || (config('settings.gsb') == $key && old('gsb') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('gsb'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gsb') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-gsb-key">{{ __('API key') }}</label>
                        <input type="password" name="gsb_key" id="i-gsb-key" class="form-control{{ $errors->has('gsb_key') ? ' is-invalid' : '' }}" value="{{ old('gsb_key') ?? config('settings.gsb_key') }}">
                        @if ($errors->has('gsb_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gsb_key') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-gcs" role="tabpanel" aria-labelledby="pills-gcs-tab">
                    <div class="form-group">
                        <label for="i-gcs">{{ __('Enabled') }}</label>
                        <select name="gcs" id="i-gcs" class="custom-select{{ $errors->has('gcs') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('No'), 1 => __('Yes')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('gcs') !== null && old('gcs') == $key) || (config('settings.gcs') == $key && old('gcs') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('gcs'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gcs') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-gcs-key">{{ __('API key') }}</label>
                        <input type="password" name="gcs_key" id="i-gcs-key" class="form-control{{ $errors->has('gcs_key') ? ' is-invalid' : '' }}" value="{{ old('gcs_key') ?? config('settings.gcs_key') }}">
                        @if ($errors->has('gcs_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gcs_key') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-gcs-id">{{ __('Search engine ID') }}</label>
                        <input type="password" name="gcs_id" id="i-gcs-id" class="form-control{{ $errors->has('gcs_id') ? ' is-invalid' : '' }}" value="{{ old('gcs_id') ?? config('settings.gcs_id') }}">
                        @if ($errors->has('gcs_id'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gcs_id') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-gcs-country" class="d-inline-flex align-items-center"><span class="{{ (__('lang_dir') == 'rtl' ? 'ml-2' : 'mr-2') }}">{{ __('Country') }}</span><span class="badge badge-secondary">{{ __('Default') }}</span></label>
                        <select name="gcs_country" id="i-gcs-country" class="custom-select{{ $errors->has('gcs_country') ? ' is-invalid' : '' }}">
                            <option value="" hidden disabled selected>{{ __('Country') }}</option>
                            @foreach(array_diff_key(config('countries'), array_flip(['AX', 'BQ', 'CT', 'NQ', 'FQ', 'GG', 'IM', 'JE', 'JT', 'FX', 'MI', 'ME', 'NT', 'VD', 'PC', 'PZ', 'YD', 'BL', 'MF', 'RS', 'PU', 'SU', 'ZZ', 'WK'])) as $key => $value)
                                <option value="{{ $key }}" @if ((old('gcs_country') !== null && old('gcs_country') == $key) || (config('settings.gcs_country') == $key && old('gcs_country') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('gcs_country'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('gcs_country') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>

                <div class="tab-pane fade" id="pills-screenshot" role="tabpanel" aria-labelledby="pills-screenshot-tab">
                    <div class="form-group">
                        <label for="i-screenshot">{{ __('Enabled') }}</label>
                        <select name="screenshot" id="i-screenshot" class="custom-select{{ $errors->has('screenshot') ? ' is-invalid' : '' }}">
                            @foreach([0 => __('No'), 1 => __('Yes')] as $key => $value)
                                <option value="{{ $key }}" @if ((old('screenshot') !== null && old('screenshot') == $key) || (config('settings.screenshot') == $key && old('screenshot') == null)) selected @endif>{{ $value }}</option>
                            @endforeach
                        </select>
                        @if ($errors->has('screenshot'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('screenshot') }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label for="i-screenshot-key">{{ __('API key') }}</label>
                        <input type="password" name="screenshot_key" id="i-screenshot-key" class="form-control{{ $errors->has('screenshot_key') ? ' is-invalid' : '' }}" value="{{ old('screenshot_key') ?? config('settings.screenshot_key') }}">
                        @if ($errors->has('screenshot_key'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('screenshot_key') }}</strong>
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <button type="submit" name="submit" class="btn btn-primary">{{ __('Save') }}</button>
        </form>

    </div>
</div>