@component('app_settings::input_group', compact('field'))

    <textarea type="{{ $field['type'] }}"
              name="{{ $field['name'] }}"
              @if( $placeholder = Arr::get($field, 'placeholder') )
              placeholder="{{ $placeholder }}"
              @endif
              @if( $rows = Arr::get($field, 'rows') )
              rows="{{ $rows }}"
              @endif
              @if( $cols = Arr::get($field, 'cols') )
              cols="{{ $cols }}"
              @endif
              class="{{ Arr::get( $field, 'class', config('app_settings.input_class', 'form-control')) }}"
              @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
              id="{{ Arr::get($field, 'name') }}"
    >{{ old($field['name'], \setting($field['name'])) }}</textarea>

@endcomponent
