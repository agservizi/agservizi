<div class="row mb-6">
    <div class="col-lg-4 col-form-label text-lg-end">
        <label class="fw-bold fs-6 @if($required??false) required @endif" for="{{$campo}}" id="label_{{$campo}}">{{$label??ucfirst(str_replace('_',' ',$campo))}}</label>
        @if($tooltip??false)
            <i class="fas fa-exclamation-circle ms-1 fs-7" data-bs-toggle="tooltip" title="{{$tooltip}}" aria-label="{{$tooltip}}"></i>
        @endif
    </div>
    <div class="col-lg-8 fv-row fv-plugins-icon-container">
        @php($selected=old($campo,$record->$campo))
        <select id="{{$campo}}" name="{{$campo}}" class="form-select form-select-solid" @if($required??false) required @endif data-required="{{$required??''}}">
            <option value="">Seleziona</option>
            @foreach($array as $key=>$value)
                <option value="{{$key}}" @selected($selected==$key)>{{$value}}</option>
            @endforeach
        </select>
        @if($help??false)
            <div class="form-text">{{$help}}</div>
        @endif
        <div class="fv-plugins-message-container invalid-feedback">
            @error($campo)
            {{$message}}
            @enderror
        </div>
    </div>
</div>

