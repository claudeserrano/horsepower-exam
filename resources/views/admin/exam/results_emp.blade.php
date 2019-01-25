@extends('layout')

@section('title')
  {{$emp->name}} - Exam Results
@endsection

@section('content')
  <div class="container content">
    @foreach($emp->exams as $exam)
      <div class="panel panel-default">
        <div class="panel-heading">
          <center>
          <h1> Exam of {{date_format($exam->created_at, 'Y/m/d h:m:s')}} </h1>
          </center>
        </div>
        <div class="panel-body">
          {{$emp->getClassAvgScore($exam->id, 1)}}
        </div>
      </div>
    @endforeach
        
  </div>
@endsection