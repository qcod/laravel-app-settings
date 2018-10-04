@php
        $fieldName = isset($field['multiple']) ? $field['name'].'[]' : $field['name'];
@endphp

<select name="{{ $fieldName }}"
        class="{{ array_get( $field, 'class', config('app_settings.input_class', 'form-control')) }}"
        @if(isset($field['multi'])) multiple @endif
        @if( $styleAttr = array_get($field, 'style')) style="{{ $styleAttr }}" @endif
        id="{{ $field['name'] }}">
    @foreach(array_get($field, 'options', []) as $val => $label)
        <option value="{{ $val }}" @if( old($field['name'], \setting($field['name'])) == $val ) selected @endif>
                {{ $label }}
        </option>
    @endforeach
</select>
