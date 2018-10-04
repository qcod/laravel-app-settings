@if( $sub_title = array_get($field, 'sub_title'))
    <div class="row">
        <div class="col-md-12">
            <h4>{{ $sub_title }}</h4>
            @if($desc = array_get($field, 'desc'))
                <p>{{ $desc }}</p>
            @endif
        </div>
    </div>
@endif