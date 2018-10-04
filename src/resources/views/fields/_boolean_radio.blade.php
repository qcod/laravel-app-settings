<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               id="{{ $field['name'] }}"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ array_get($field, 'true_value', '1') }}"
               @if(setting($field['name']) == array_get($field, 'true_value', '1')) checked @endif>
        {{ array_get($field, 'true_label', 'Yes') }}
    </label>
</div>
<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ array_get($field, 'false_value', '0') }}"
               @if(setting($field['name']) == array_get($field, 'false_value', '0')) checked @endif>
        {{ array_get($field, 'false_label', 'No') }}
    </label>
</div>
