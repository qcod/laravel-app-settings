<div class="{{ Arr::get( $field, 'input_wrapper_class', config('app_settings.input_wrapper_class', 'form-group')) }} {{ $errors->has($field['name']) ? Arr::get( $field, 'input_error_class', config('app_settings.input_error_class', 'has-danger')) : '' }}">
    @include('app_settings::fields._label')

    {{ $slot }}

    @include('app_settings::fields._hint')
</div>
