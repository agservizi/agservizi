@extends('Backend._components.modal')
@section('content')

    <form id="form-cambia-stato"
          action="{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'updateStato'],$record->id)}}"
          method="POST"
    >
        @method('PATCH')
        @csrf

        <div class="row mt-6">
            <div class="col-lg-12 col-form-label">
                <label class="fw-bold fs-6">LDV</label>
            </div>
            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                <div class="fv-row">
                    <div class="dropzone" id="kt_dropzonejs_example_1">
                        <div class="dz-message needsclick">
                            <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>

                            <div class="ms-4">
                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Trascina il file qui o clicca per
                                    selezionare i files</h3>
                                <span class="fs-7 fw-bold text-gray-400">
                                            <span>Qui puoi allegare la lettera di vettura</span>
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-6">
            <div class="col-lg-12 col-form-label">
                <label class="fw-bold fs-6">POD</label>
            </div>
            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                <div class="fv-row">
                    <div class="dropzone" id="kt_dropzonejs_example_pod">
                        <div class="dz-message needsclick">
                            <i class="bi bi-file-earmark-arrow-up text-primary fs-3x"></i>

                            <div class="ms-4">
                                <h3 class="fs-5 fw-bolder text-gray-900 mb-1">Trascina il file qui o clicca per
                                    selezionare i files</h3>
                                <span class="fs-7 fw-bold text-gray-400">
                                            <span>Qui puoi allegare il pod</span>
                                        </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row pt-12">
            <!--begin::Row-->
            @php($selected=old('stato_spedizione',$record->stato_spedizione))
            <div class="col-lg-12 fv-row fv-plugins-icon-container">
                <!--begin::Radio group-->
                <div class="btn-group w-100" data-kt-buttons="true" data-kt-buttons-target="[data-kt-button]">
                    @foreach(\App\Models\StatoSpedizione::get() as $stato)
                        <label
                            class="btn btn-outline-dark  btn-active-primary {{$selected==$stato->id?'active':''}}"
                            style="background-color: {{$stato->colore_hex}};"
                            data-kt-button="true" style="padding: .4rem 1rem">
                            <input class="btn-check buttons" type="radio" name="stato_spedizione" value="{{$stato->id}}"
                                   {{$selected==$stato->id?'checked':''}} id="stato{{$stato->id}}"
                            />
                            {{$stato->nome}}
                        </label>
                    @endforeach
                </div>
                <div class="fv-plugins-message-container invalid-feedback">

                </div>
            </div>
        </div>
    </form>
    <script>
        $(function () {
            $('.btn-check').click(function () {

                formSubmit($('#form-cambia-stato'));
            });
            var myDropzone = new Dropzone("#kt_dropzonejs_example_1", {
                url: "{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'uploadAllegato'],['cosa'=>'ldv','id'=>$record->id])}}", // Set the url for your upload script location
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 1,
                maxFilesize: 20, // MB
                addRemoveLinks: true,
                //acceptedFiles: "image/*",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                init: function () {
                    thisDropzone = this;
                    this.on("sending", function (file, xhr, formData) {

                    });
                    const esistenti =@json(\App\Models\AllegatoSpedizione::perBlade($record->id,'ldv'));
                    if (esistenti) {
                        $.each(esistenti, function (key, value) {

                            var mockFile = {
                                name: value.path_filename,
                                size: value.dimensione_file,
                                filename: value.path_filename,
                                id: value.id
                            };

                            thisDropzone.emit('addedfile', mockFile);
                            if (value.thumbnail) {
                                thisDropzone.emit('thumbnail', mockFile, "/storage/" + value.thumbnail);

                            }
                            thisDropzone.emit('complete', mockFile);


                        });
                    }

                },
                accept: function (file, done) {
                    if (file.name == "q") {
                        done("Naha, you don't.");
                    } else {
                        done();
                    }
                },
                success: function (file, response) {
                    file.filename = response.filename;
                    file.id = response.id;
                    if (response.thumbnail) {
                        file.previewElement.querySelector("img").src = response.thumbnail;
                    }
                },
                removedfile: function (file) {
                    console.dir(file);
                    var name = file.filename;
                    console.log(name);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '{{ action([$controller,'deleteAllegato']) }}',
                        data: {
                            id: file.id
                        },
                        success: function (data) {
                            console.log("File has been successfully removed!!");
                        },
                        error: function (e) {
                            console.log(e);
                        }
                    });
                    var fileRef;
                    return (fileRef = file.previewElement) != null ?
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                },
            });
          var myDropzonepodf = new Dropzone("#kt_dropzonejs_example_pod", {
                url: "{{action([\App\Http\Controllers\Backend\SpedizioneController::class,'uploadAllegato'],['cosa'=>'pod','id'=>$record->id])}}", // Set the url for your upload script location
                paramName: "file", // The name that will be used to transfer the file
                maxFiles: 1,
                maxFilesize: 20, // MB
                addRemoveLinks: true,
                //acceptedFiles: "image/*",
                headers: {
                    'X-CSRF-TOKEN': "{{ csrf_token() }}"
                },
                init: function () {
                    thisDropzone = this;
                    this.on("sending", function (file, xhr, formData) {

                    });
                    const esistenti =@json(\App\Models\AllegatoSpedizione::perBlade($record->id,'pod'));
                    if (esistenti) {
                        $.each(esistenti, function (key, value) {

                            var mockFile = {
                                name: value.path_filename,
                                size: value.dimensione_file,
                                filename: value.path_filename,
                                id: value.id
                            };

                            thisDropzone.emit('addedfile', mockFile);
                            if (value.thumbnail) {
                                thisDropzone.emit('thumbnail', mockFile, "/storage/" + value.thumbnail);

                            }
                            thisDropzone.emit('complete', mockFile);


                        });
                    }

                },
                accept: function (file, done) {
                    if (file.name == "q") {
                        done("Naha, you don't.");
                    } else {
                        done();
                    }
                },
                success: function (file, response) {
                    file.filename = response.filename;
                    file.id = response.id;
                    if (response.thumbnail) {
                        file.previewElement.querySelector("img").src = response.thumbnail;
                    }
                },
                removedfile: function (file) {
                    console.dir(file);
                    var name = file.filename;
                    console.log(name);
                    $.ajax({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                        },
                        type: 'DELETE',
                        url: '{{ action([$controller,'deleteAllegato']) }}',
                        data: {
                            id: file.id
                        },
                        success: function (data) {
                            console.log("File has been successfully removed!!");
                        },
                        error: function (e) {
                            console.log(e);
                        }
                    });
                    var fileRef;
                    return (fileRef = file.previewElement) != null ?
                        fileRef.parentNode.removeChild(file.previewElement) : void 0;
                },
            });

        });
    </script>
@endsection
