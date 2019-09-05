<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               id="{{ $field['name'] }}"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ Arr::get($field, 'true_value', '1') }}"
               @if(setting($field['name']) == Arr::get($field, 'true_value', '1')) checked @endif>
        {{ Arr::get($field, 'true_label', 'Yes') }}
    </label>
</div>
<div class="form-check form-check-inline">
    <label class="form-check-label">
        <input class="form-check-input"
               type="radio"
               name="{{ $field['name'] }}"
               value="{{ Arr::get($field, 'false_value', '0') }}"
               @if(setting($field['name']) == Arr::get($field, 'false_value', '0')) checked @endif>
        {{ Arr::get($field, 'false_label', 'No') }}
    </label>
</div>
