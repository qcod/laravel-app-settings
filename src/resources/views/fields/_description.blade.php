@if( $sub_title = Arr::get($field, 'sub_title'))
    <div class="row">
        <div class="col-md-12">
            <h4>{{ $sub_title }}</h4>
            @if($desc = Arr::get($field, 'desc'))
                <p>{{ $desc }}</p>
            @endif
        </div>
    </div>
@endif
