@if( $label = array_get($field, 'label') )
    <label for="{{ array_get($field, 'name') }}">{{ $label }}</label>
@endif
