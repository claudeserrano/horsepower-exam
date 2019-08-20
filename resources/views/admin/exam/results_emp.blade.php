@extends('layout')

@section('title')
  {{$emp->name}} - Exam Results
@endsection

@section('content')
  <div class="container content">
      <div class="panel panel-default">
        <div class="panel-heading">
          <center>
          <h1> {{$emp->name}} Results </h1>
          <h3> Exam of {{date_format($exam->created_at, 'Y/m/d h:m:s')}} </h3>
          </center>
        </div>
        <div class="panel-body">
          <center>
          @foreach($exam->tests as $test)
            <div class="col-lg-3">
                <div class="panel panel-default">
                    <div class="panel-heading">
                      <h4>{{$test->template_subtitle}}</h4>
                    </div>
                    <div class="panel-body">
                      <h4><b>Score :</b> <br/> {{number_format($test->score,2)}} %</h4>
                    </div>
                </div>
            </div>
          @endforeach
          @if($exam->type == 'hpe')
            <div class="col-lg-12">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4>School Classification</h4>
                  </div>
                  <div class="panel-body">
                  
                    <div class="col-lg-1">
                    </div>
                  @for($i = 1; $i <= 5; $i++)
                    <div class="col-lg-2">
                        <div class="panel panel-default">
                            <div class="panel-heading">
                              <h4>Year {{$i}}</h4>
                            </div>
                            <div class="panel-body">
                              <h4><b>Score :</b> <br/> {{$emp->getClassAvgScore($exam->id, $i)}}  %</h4>
                            </div>
                        </div>
                    </div>
                  @endfor
                  </div>
                </div>
            @endif
            </div>
          </center>
        </div>
      </div>
  </div>
@endsection