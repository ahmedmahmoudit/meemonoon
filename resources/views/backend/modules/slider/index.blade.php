@extends('backend.layouts.master')

@section('content')
    <div class="row">
        @if(!empty($slides))
            @foreach($slides as $slide)
                <div class="col-lg-4">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            {{ Form::open(['route' => ['backend.slider.destroy', $slide->id], 'method' => 'DELETE'])  }}
                            <button type="submit" class="btn btn-outline btn-circle pink btn-xs"><i
                                        class="fa fa-remove"></i>
                                Delete Slide
                            </button>
                            <span style="float: right;">{{$slide->order}}</span>
                            {{ Form::close() }}
                        </div>
                        <div class="panel-body">
                            <img class="img-thumbnail center" src={{ url('img/uploads/thumbnail/'.$slide->image_path) }} />
                            <div class="caption">{{ $slide->caption_en }}</div>
                        </div>
                    </div>
                </div>
            @endforeach
        @endif
    </div>

    {{ Form::open(['route'=>['backend.slider.store'],'method'=>'POST','files' => 'true','class' => 'form-horizontal','enctype' =>"multipart/form-data"]) }}

    {{ Form::hidden('MAX_FILE_SIZE','20971520') }}
    <div class="form-group">
        <div class="col-lg-12">
            <div class="alert alert-danger">
                <strong>Very Important!</strong> Image Size Must be 1920X759 .
            </div>
        </div>
        <div class="col-lg-4">
            <label class="mt-checkbox"> Add Slide</label>
            <div class="form-control">
                <input name="image" type="file" required/>
            </div>
        </div>
        <div class="col-lg-4">
            <label class="mt-checkbox"> caption </label>
            {!! Form::text('caption_en',null,['class' =>'form-control','required']) !!}
        </div>
        <div class="col-lg-2">
            <label class="mt-checkbox"> order </label>
            {!! Form::text('order',null,['class' =>'form-control','required']) !!}
        </div>
    </div>

    {{ Form::submit('submit',['class' => 'btn btn-outline btn-circle btn-success']) }}
    <a class="btn btn-outline btn-danger btn-circle" href="{{ route('backend.slider.index') }}">cancel</a>
    {{ Form::close() }}
@endsection