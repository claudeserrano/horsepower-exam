@extends('layout')

@section('title')
Electical Exam - {{$data->description->title}}
@endsection

@section('content')

    <div class="container content">
        <div class="panel panel-default">
            <div class="panel-heading">
                <center>


                <h1> {{$data->description->title}} </h1>
                <h4> {{$data->description->subtitle}} </h4>
                <a href="{{ route('changeLang') }}" type="button" class="btn btn-primary">To {{ session('en_alt') ? session('en_alt') : 'Spanish' }}</a>
                </center>
            </div>
            <div class="panel-body">
                <form class="form-horizontal" id="examForm" role="form" method="POST" action="{{ route('submitExam', ['progress' => session('progress')]) }}">
                    
                {{ csrf_field() }}
                
                <input type="hidden" name="template_id" value="{{$data->description->id}}" />
                <input type="hidden" name="template_title" value="{{$data->description->title}}" />
                <input type="hidden" name="template_subtitle" value="{{$data->description->subtitle}}" />

                <div class="col-lg-12">
                    <center>
                        @foreach($data->questions as $q)
                            <div class="col-lg-12"><h3 name="{{$q->name}}">{{$q->label}}</h3></div>
                            @foreach($q->choices as $choice)
                                <div class="col-lg-6">
                                    <div class="form-check">
                                    <label class="form-check-label">
                                        <input class="form-check-input" type="radio" name="{{$q->name}}" value="{{$choice->value}}">
                                        {{$choice->label}}
                                    </label>
                                    </div>
                                </div>
                            @endforeach

                            <div class="col-lg-12"><hr></div>
                        @endforeach

                        <div class="col-lg-12">
                            <button id="btn-disable-onclick" type="submit">Submit</button>
                        </div>

                        <div class="col-lg-12"><br></div>
                    </center>
                </div>

                </form>
            </div>
        </div>
               
    </div>
@endsection

@section('scripts')
    
    <script>

        $("#examForm").submit(function(){
            var c = confirm("Submit and finalize answers?");
            $("#btn-disable-onclick").attr('disabled', true);
            if(!c)
                $("#btn-disable-onclick").attr('disabled', false);
            return c;
        });
        
    </script>

@endsection