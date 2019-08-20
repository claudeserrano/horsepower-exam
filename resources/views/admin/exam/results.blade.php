@extends('layout')

@section('title')
  Horsepower - Exam Results
@endsection

@section('content')
  <div class="container content">
    <center>
      <h2 style="padding-bottom:20px">Examination Results</h2>
    </center>

    <table id='tb' class="table table-bordered display" cellspacing='0' width='100%'>
      <thead>
        <tr>
          <th scope="col">Date Taken</th>
          <th scope="col">Exam Type</th>
          <th scope="col">Name</th>
          <th scope="col">Average</th>
        </tr>
      </thead>
      <tbody>
        @foreach($emps as $emp)
          <tr>
            <td>{{ $emp->exam ? $emp->exam->created_at : ""}}</td>
            <td>{{ $emp->exam ? $emp->exam->type : ""}}</td>
            <td><a href="results/{{$emp->id}}">{{$emp->name}}</a></td>
            <td>{{number_format($emp->averageOverallScore(), 2)}} %</td>
          </tr>
        @endforeach
      </tbody>
    </table>
  </div>
@endsection

@section('scripts')

    <script>
      $(document).ready(function(){
            tb = $("#tb").DataTable({
            });
      })
    </script>

@endsection