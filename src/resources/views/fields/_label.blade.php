@if( $label = Arr::get($field, 'label') )
    <label for="{{ Arr::get($field, 'name') }}">{{ $label }}</label>
@endif
