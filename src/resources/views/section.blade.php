<div class="{{ array_get($fields, 'section_class', config('app_settings.section_class', 'card')) }} section-{{ str_slug($fields['title']) }}">
    <div class="{{ array_get($fields, 'section_heading_class', config('app_settings.section_heading_class', 'card-header')) }}">
        <i class="{{ array_get($fields, 'icon', 'glyphicon glyphicon-flash') }}"></i>
        {{ $fields['title'] }}
    </div>

    @if( $desc = array_get($fields, 'descriptions') )
        <div class="pb-0 {{ config('app_settings.section_body_class', array_get($fields, 'section_body_class', 'card-body')) }}">
            <p class="text-muted mb-0 ">{{ $desc }}</p>
        </div>
    @endif

    {{ $slot }}
</div>
<!-- end card for {{ $fields['title'] }} -->
