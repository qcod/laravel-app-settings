@component('app_settings::input_group', compact('field'))

    <textarea type="{{ $field['type'] }}"
              name="{{ $field['name'] }}"
              @if( $placeholder = array_get($field, 'placeholder') )
              placeholder="{{ $placeholder }}"
              @endif
              @if( $rows = array_get($field, 'rows') )
              rows="{{ $rows }}"
              @endif
              @if( $cols = array_get($field, 'cols') )
              cols="{{ $cols }}"
              @endif
              class="{{ array_get( $field, 'class', config('app_settings.input_class', 'form-control')) }}"
              @if( $styleAttr = array_get($field, 'style')) style="{{ $styleAttr }}" @endif
              id="{{ array_get($field, 'name') }}"
    >{{ old($field['name'], \setting($field['name'])) }}</textarea>

@endcomponent
