<div class="row mb-6">
    <!--begin::Heading-->
    <div class="col-lg-4 col-form-label text-lg-end">
        <label class="fw-bold fs-6 @if($required??false) required @endif" for="{{$campo}}" id="label_{{$campo}}">{{$label??ucfirst(str_replace('_',' ',$campo))}}</label>
        @if($tooltip??false)
            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="{{$tooltip}}" aria-label="{{$tooltip}}"></i>
        @endif
    </div>
    <!--end::Heading-->
    <!--begin::Row-->
    @php($selected=old($campo,$record->$campo))
    <div class="col-lg-8 fv-row fv-plugins-icon-container">
        <!--begin::Radio group-->
        <div class="btn-group " data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
            @foreach($array as $key=>$value)
                <label class="btn btn-outline btn-color-muted btn-active-primary {{$selected==$key?'active':''}}"
                       data-kt-button="true" style="padding: .4rem 1rem">
                    <input class="btn-check buttons  {{$campo}}" type="radio" name="{{$campo}}" value="{{$key}}" required {{$selected==$key?'checked':''}}/>
                    {{$value}}</label>
            @endforeach
        </div>
        <div class="fv-plugins-message-container invalid-feedback">
            @error($campo)
            {{$message}}
            @enderror
        </div>
    </div>
</div>



