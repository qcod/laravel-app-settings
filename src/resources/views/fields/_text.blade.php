@component('app_settings::input_group', compact('field'))

    <input type="{{ $field['type'] }}"
           name="{{ $field['name'] }}"
           @if( $placeholder = Arr::get($field, 'placeholder') )
           placeholder="{{ $placeholder }}"
           @endif
           value="{{ old($field['name'], \setting($field['name'])) }}"
           class="{{ Arr::get( $field, 'class', config('app_settings.input_class', 'form-control')) }} {{ $errors->has($field['name']) ? config('app_settings.input_invalid_class', 'is-invalid') : '' }}"
           @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
           @if( $maxAttr = Arr::get($field, 'max')) max="{{ $maxAttr }}" @endif
           @if( $minAttr = Arr::get($field, 'min')) min="{{ $minAttr }}" @endif
           id="{{ Arr::get($field, 'name') }}"
    >

    @if( $append = Arr::get($field, 'append'))
        <span>{{ $append }}</span>
    @endif

@endcomponent
