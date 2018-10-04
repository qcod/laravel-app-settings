@component('app_settings::input_group', compact('field'))

    @include('app_settings::fields._description', ['field' => $field])

    <div class="checkbox">
        @foreach($field['options'] as $option)
            <div>
                @php
                    $checkbox_value = array_get($option, 'value', $option, []);
                    $checkbox_label = array_get($option, 'label', $option, []);
                    $current_value = old($field['name'], \setting($field['name'], []));
                @endphp
                <label>
                    <input
                        @if( in_array($checkbox_value, $current_value)) checked @endif
                        name="{{ $field['name'] }}[]"
                        value="{{ $checkbox_value }}"
                        class="{{ array_get( $field, 'class') }}"
                        type="checkbox">
                    {{ $checkbox_label }}
                </label>
            </div>
        @endforeach
    </div>
@endcomponent
