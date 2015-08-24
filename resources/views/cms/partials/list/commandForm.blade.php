<div class="modal fade">
    <div class="modal-dialog">

        {!! Form::open(["url" => $url]) !!}

        {!! Form::hidden("sharp_form_valued", true) !!}

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">{{ trans("sharp::ui.command_params_modal_title") }}</h4>
            </div>
            <div class="modal-body">
                @foreach($fields as $key => $field)
                    @include("sharp::cms.partials.formField", ["field" => $field, "instance" => null])
                @endforeach
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">{{ trans("sharp::ui.command_params_modal_btn") }}</button>
            </div>
        </div>

        {!! Form::close() !!}
    </div>
</div>


