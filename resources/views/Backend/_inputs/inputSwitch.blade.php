<div class="row mb-6">
    <div class="col-lg-4 col-form-label text-lg-end">
        <label class="fw-bold fs-6 @if($required??false) required @endif" for="{{$campo}}" id="label_{{$campo}}">{{$label??ucfirst(str_replace('_',' ',$campo))}}</label>
        @if($tooltip??false)
            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="{{$tooltip}}" aria-label="{{$tooltip}}"></i>
        @endif
    </div>
    <div class="col-lg-8 fv-row fv-plugins-icon-container">
        <div class="form-check form-switch form-check-custom form-check-solid mt-2">
            <input class="form-check-input" type="checkbox" value="1" id="{{$campo}}" name="{{$campo}}"  {{old($campo,$record->$campo)?'checked':''}}/>
        </div>
        @if($help??false)
            <div class="form-text">{{$help}}</div>
        @endif
    </div>
</div>
