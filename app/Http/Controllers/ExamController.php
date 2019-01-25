<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Employee;
use App\Exam;
use App\Test;
use App\Answer;
use Facades\App\Services\ExamHelper;

class ExamController extends Controller
{
 
     private $validate = true;
     private const PAGE_NUM = 4;

	/**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('exam');
    }

    /**
     * Display a listing of the resource.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request->session()->has('started'))
            return redirect('exam/generate');
    	return view('exams.home');
    }

    /**
     * Process name and return exam page/s.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request)
    {
        /*
        $request->validate(['name' => 'required']);

        session(['started' => 1]);

        //  Get list of exams to take
        $examList = ExamHelper::getExamList($request->type);

        $guest = new Guest;
        $guest->name = $request->name;
        $guest->progress = 0;
        $guest->type = $request->type;
        $guest->save();

        session(['id' => $guest->id]);
        session(['list' => $examList]);
        session(['progress' => 0]);
        session(['page' => session('list')[session('progress')]]);
        session(['pages' => sizeof($examList)]);

        return redirect('exam/generate');
        */
        //  REVAMPING THE WHOLE EXAM CONTROLLER -------------------------------------------HERE //

        //  Validating input
        $request->validate([
            'name' => 'required'
            //'email' => 'required'
        ]);

        //  Inserting employee information
        $emp = new Employee;
        try{
            $emp->name = $request->name;
            $emp->email = $request->email;
            $emp->progress = 1; //  using 1 and increment for the json file naming convention. exam1.json, exam2.json, ... 'exam' . $progress . '.json'
            $emp->save();
        }
        catch(Exception $e){
            return $e;
            //abort(404, $e); //  Throws a 404 error
        }

        //  Inserting exam information
        $exam = new Exam;
        try{
            $exam->employee_id = $emp->id;
            $exam->score = 0;
            $exam->save();
        }
        catch(Exception $e){
            return $e;
            //abort(404, $e); //  Throws a 404 error
        }


        //  Add session data
        session(['started' => 1]);
        session(['id' => $emp->id]);
        session(['exam_id' => $exam->id]);
        session(['progress' => $emp->progress]);
        
        return redirect('exam/generate');
    }

    /**
     * Submit exam data.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function submit(Request $request)
    {
        /*
        $level = $request->query('level');

        $scr = new Score;
        $scr->level = $level;
        $scr->guest_id = session('id');
        $scr->save();

        $answers = ExamHelper::getKey($level);
        $score = 0;

        for($i = 1; $i <= sizeof($answers); $i++)
        {
            $index = 'ans' . $i;
            $correct = $answers[$i - 1];
            $answer = $request->$index;

            $ans = new Answer;
            $ans->score_id = $scr->id;
            $ans->answer = $request->$index;
            $ans->question = $i;
            $ans->correct = 0;

            if($correct == $answer){
                $score++;
                $ans->correct = 1;
            }

            $ans->save();

        }

        $score = $score / sizeof($answers) * 100;

        $scr->score = $score;
        $scr->save();

        $guest = Guest::find(session('id'));

        //  If score is more than 7 or it's the last exam
        if($score >= 70 || $guest->progress >= 3)
            $guest->progress += 1;
        //  Skip to school exam
        else{
            $guest->progress = 3;
        }

        $guest->save();

        //  $progress = session('progress');
        //  session(['progress' => $progress + 1]);

        //  Put progress in session data
        session(['progress' => $guest->progress]);

        //  If progress is less than total pages, return exam page, else redirect to completion page
        if(session('progress') < session('pages')){
            session(['page' => session('list')[session('progress')]]);
        }
        else{
            return redirect('exam/complete');
        }

        return redirect('exam/generate');
        */

        //  REVAMPING THE WHOLE EXAM CONTROLLER -------------------------------------------HERE //

        //  Exam submit revamp
        $progress = $request->query('progress');

        //  Inserting exam information
        $test = new Test;
        try{
            $test->exam_id = session('exam_id');
            $test->template_id = $request->template_id;
            $test->score = 0;
            $test->save();
        }
        catch(Exception $e){
            return $e;
            //abort(404, $e); //  Throws a 404 error
        }

        $answers = ExamHelper::getKey($progress);

        $score = 0;

        for($i = 1; $i <= sizeof($answers); $i++)
        {
            $index = 'ans' . $i;
            $correct = $answers[$i - 1];
            $answer = $request->$index;

            $ans = new Answer;
            $ans->test_id = $test->id;
            $ans->value = $answer;
            $ans->question_id = $index;
            $ans->correct = false;

            //  If answer is correct
            if($correct == $answer){
                $score++;
                $ans->correct = true;
            }

            $ans->save();
        }


        $score = $score / sizeof($answers) * 100;
        $test->score = $score;
        $test->save();

        $emp = Employee::find(session('id'));

        //  If score is more than 70% or it's the last exam
        if($score >= 70 || $emp->progress >= 3)
            $emp->progress += 1;
        //  Skip to school exam
        else{
            $emp->progress = 4;
        }

        $emp->save();

        //  Put progress in session data
        session(['progress' => $emp->progress]);

        //  If progress is less than or equal to total pages, return exam page, else redirect to completion page
        if(session('progress') <= self::PAGE_NUM){
            return redirect('exam/generate');
        }
        else{
            return redirect('exam/complete');
        }
    }

    /**
     * Complete the exam, return view.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request)
    {
        //  If progress is less than total pages, return exam page, else redirect to completion page
        if(session('progress') <= self::PAGE_NUM){
            return redirect('exam/generate');
        }
        else{
            $request->session()->flush();
            session(['progress' => 0]);
            return view('exams.complete');
        }
    }

    /**
     * Resumes exam if session data is found.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function resume(Request $request)
    {
        if(session('progress') <= self::PAGE_NUM){
            return view('exams.exam');
        }
        else{
            return redirect('exam/complete');
        }
    }

    /**
     * Generates the exam view.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function generate(Request $request)
    {
        // $file = 'exams/exam' . (session('progress') + 1) . '.json';
        $file = 'exams/en/' . (session('progress')) . '.json';   //  For revamp
        $data = json_decode(file_get_contents($file));

        return view('exams.exam')->with(['data' => $data]);
    }

    /**
     * Flush sessions.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function flush(Request $request)
    {
        $request->session()->flush();
        return redirect('exam');
    }

}
