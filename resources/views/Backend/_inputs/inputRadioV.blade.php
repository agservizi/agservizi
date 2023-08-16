<div class=" mb-6" id="div_{{$campo}}">
        <label class="fw-bold form-check-label fw-bold fs-6 @if($required??false) required @endif">
            {{$label??ucfirst(str_replace('_',' ',$campo))}}
        </label>
    <div class="">
        <div class="d-flex flex-wrap">
            @php($selected=old($campo,$record->$campo))
            @foreach($array as $key=>$value)
                <div class="form-check form-check-custom form-check-solid me-10 mb-2">
                    <input class="form-check-input {{$campo}}" type="radio" value="{{$key}}" name="{{$campo}}"
                           id="{{$campo.$key}}" {{($required??false)?'required':''}} {{$selected==$key?'checked':''}}>
                    <label class="form-check-label" for="{{$campo.$key}}">{{$value}}</label>
                </div>
            @endforeach
        </div>
        @if($help??false)
            <div class="form-text">{{$help}}</div>
        @endif
    </div>
</div>


