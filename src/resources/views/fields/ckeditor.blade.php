@component('app_settings::input_group', compact('field'))

    <textarea name="{{ $field['name'] }}"
              class="{{ Arr::get( $field, 'class', config('app_settings.input_class', 'form-control')) }} ckeditor-field"
              @if( $styleAttr = Arr::get($field, 'style')) style="{{ $styleAttr }}" @endif
              id="{{ Arr::get($field, 'name') }}"
    >{{ old($field['name'], \setting($field['name'])) }}</textarea>

@endcomponent

@once
@push('body.scripts')
    <script src="https://cdn.ckeditor.com/ckeditor5/22.0.0/classic/ckeditor.js"></script>
    <script>
      ClassicEditor
        .create( document.querySelector( '.ckeditor-field' ) )
        .catch( error => {
          console.error( error );
        } );
    </script>
@endpush
@endonce
