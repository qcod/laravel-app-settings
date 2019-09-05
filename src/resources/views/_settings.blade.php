<div class="container">
    <div class="row">
        <div class="col-md-7 col-md-offset-2">

            @includeIf(config('app_settings.flash_partial'))

            <form method="post" action="{{ config('app_settings.url') }}" class="form-horizontal mb-3" enctype="multipart/form-data" role="form">
                {!! csrf_field() !!}

                @if( isset($settingsUI) && count($settingsUI) )

                    @foreach(Arr::get($settingsUI, 'sections', []) as $section => $fields)
                        @component('app_settings::section', compact('fields'))
                            <div class="{{ Arr::get($fields, 'section_body_class', config('app_settings.section_body_class', 'card-body')) }}">
                                @foreach(Arr::get($fields, 'inputs', []) as $field)
                                    @if(!view()->exists('app_settings::fields.' . $field['type']))
                                        <div style="background-color: #f7ecb5; box-shadow: inset 2px 2px 7px #e0c492; border-radius: 0.3rem; padding: 1rem; margin-bottom: 1rem">
                                            Defined setting <strong>{{ $field['name'] }}</strong> with
                                            type <code>{{ $field['type'] }}</code> field is not supported. <br>
                                            You can create a <code>fields/{{ $field['type'] }}.balde.php</code> to render this input however you want.
                                        </div>
                                    @endif
                                    @includeIf('app_settings::fields.' . $field['type'] )
                                @endforeach
                            </div>
                        @endcomponent
                    @endforeach
                @endif

                <div class="row m-b-md">
                    <div class="col-md-12">
                        <button class="btn-primary btn">
                            {{ Arr::get($settingsUI, 'submit_btn_text', 'Save Settings') }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
