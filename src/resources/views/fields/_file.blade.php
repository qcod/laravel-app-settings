@component('app_settings::input_group', compact('field'))

    <br>
    <input type="file"
           name="{{ $field['name'] }}"
           @if( $placeholder = Arr::get($field, 'placeholder') )
           placeholder="{{ $placeholder }}"
           @endif
           class="{{ Arr::get( $field, 'class') }} {{ $errors->has($field['name']) ? config('app_settings.input_invalid_class', 'is-invalid') : '' }}"
           @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
           id="{{ Arr::get($field, 'name') }}"
    >

    @if( $filePath = \setting($field['name']))
        <label class="text-danger" style="float:right; font-size: 0.8rem">
            <input type="checkbox" value="1" name="remove_file_{{$field['name']}}">
            {{ Arr::get($field, 'remove_label', 'Remove') }}
        </label>
        @php $fileUrl = \Storage::disk(Arr::get($field, 'disk', 'public'))->url($filePath) @endphp
        @if(in_array(pathinfo($filePath, PATHINFO_EXTENSION), ["gif", "jpg", "jpeg", "png", "tiff", "tif"]))
            <a href="{{ $fileUrl }}" target="_blank">
                <img src="{{ $fileUrl }}" alt="{{ $field['name'] }}" class="{{ Arr::get( $field, 'preview_class') }}" style="{{ Arr::get($field, 'preview_style') }}"/>
            </a>
        @else
            <a target="_blank" class="btn btn-light btn-sm" href="{{ $fileUrl }}">View {{ $field['label'] }}</a>
        @endif
    @endif

@endcomponent
