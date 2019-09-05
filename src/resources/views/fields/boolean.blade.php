@component('app_settings::input_group', compact('field'))
    @if( count(Arr::get($field, 'options', [])) )
        @include('app_settings::fields._select')
    @else
        <br>
        @include('app_settings::fields._boolean_radio')
    @endif
@endcomponent
