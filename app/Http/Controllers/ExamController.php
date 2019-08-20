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
    
    private const PAGE_NUM = 4;
 
    private $validate = true;

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
    public function index(Request $request, $exam_type)
    {
        $examList = ExamHelper::getExamList($exam_type);
        if($examList){
            if($request->session()->has('started'))
                return redirect('exam/generate');
            return view('exams.home')->with(['exam_type' => $exam_type]);
        }
    }

    /**
     * Process name and return exam page/s.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function start(Request $request)
    {
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
            $exam->type = $request->type;
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
        session(['type' => $exam->type]);
        session(['progress' => $emp->progress]);
        session(['exam_list' => ExamHelper::getExamList($request->type)]);
        session(['lang' => 'en']);
        session(['en_alt' => 'Spanish']);
        
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
        //  Exam submit revamp
        $progress = $request->query('progress');
        
        //  Get answer key
        $answers = ExamHelper::getKey(session('exam_list')[$progress - 1]);

        /*  Checking for test data
        $test = Test::where('exam_id', session('exam_id'))->where('template_id', $request->template_id)->first();

        if(!is_null($test))
            return redirect('exam/generate');
        */

        //  Inserting exam information
        $test = new Test;
        try{
            $test->exam_id = session('exam_id');
            $test->template_id = $request->template_id;
            $test->template_title = $request->template_title;
            $test->template_subtitle = $request->template_subtitle;
            $test->score = 0;
            $test->save();
        }
        catch(Exception $e){
            return $e;
            //abort(404, $e); //  Throws a 404 error
        }

        $score = 0;
        //  Score tally for calculating percentage
        $score_perfect = 0;

        $post = $request->post();
        $correct = false;

        //  Filter all post values to only get answers
        $ans = array_values(array_filter(array_keys($post), function($var){
            return substr($var, 0, 3) == 'ans';
        }));
       
        //  Start score processing
        for($a = 0; $a < sizeof($ans); $a++){
            $index = $ans[$a];
            $list_index = substr($index, 3);
            //  Check if checkbox
            $cb_pos = strpos($list_index, "_");
            $cb_index = null;

            $user_ans = $post[$index];
            $key_ans = null;
            
            //  If checkbox, find cb_index in the array in answer key
            if($cb_pos){
                $cb_index = substr($list_index, $cb_pos + 1, strlen($list_index) - $cb_pos);
                $list_index = substr($list_index, 0, $cb_pos);
                $key_ans = $answers[$list_index - 1];
                $varkey = array_search($cb_index, $key_ans);
                
                //  If answer not found, add penalty
                if($varkey === false){
                    $score -= 1 / sizeof($key_ans);
                }
                //  If answer found in possible answers
                else{
                    $correct = true;
                    $score += 1 / sizeof($key_ans);
                }

            }
            else{
                $key_ans = $answers[$list_index - 1];

                if($key_ans == $user_ans){
                    $correct = true;
                    $score++;
                } 
            }

            $ans_record = new Answer;
            $ans_record->test_id = $test->id;
            $ans_record->value = $user_ans;
            $ans_record->question_id = $index;
            $ans_record->correct = $correct;
            
            $ans_record->save();

        }

        $score = $score / sizeof($answers) * 100;
        $test->score = $score;
        $test->save();

        $emp = Employee::find(session('id'));

        //  Progress checking
        $emp->progress += 1;

        //  Special rules per exam
        switch(session('type')){
            //  If score is less than 70% & still not last exam, skip to last exam
            case 'hpe':
                if($score < 70 && $emp->progress < 4)
                    $emp->progress = 4;
                break;
            case 'mfs':
                break;
            default:
                break;
        }

        $emp->save();

        //  Put progress in session data
        session(['progress' => $emp->progress]);

        //  If progress is less than or equal to total pages, return exam page, else redirect to completion page
        if(session('progress') <= sizeof(session('exam_list'))){
            return redirect('exam/generate');
        }
        else{
            return redirect('exam/complete');
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
        if($request->session()->get('complete'))
            return redirect('exam/complete');
        try{
            $file = 'exams/'. session('lang') .'/' . session('exam_list')[session('progress') - 1] . '.json';   //  For revamp
            $data = json_decode(file_get_contents($file));
        }
        catch(\ErrorException $e){
            //abort(404, $e);
            return redirect('exam');
        }

        return view('exams.exam')->with(['data' => $data]);
    }

    /**
     * Complete the exam, return view.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function complete(Request $request)
    {
        if(!$request->session()->has('progress')){
            return redirect('exam');
            
        }
        else{
            //  If progress is less than total pages, return exam page, else redirect to completion page
            if(session('progress') <= sizeof(session('exam_list'))){
                return redirect('exam/generate');
            }
            else{
                $request->session()->flush();
                //  session(['progress' => 1]);
                return view('exams.complete');
            }

        }
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
        return redirect('exam/home/hpe');
    }

    /**
     * Language change, only from English to Spanish.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function changeLanguage(Request $request)
    {
        $lang = $request->session()->get('lang');
        $en_alt = '';

        if(is_null($lang)){
            $lang = 'sp';
            $en_alt = 'English';
        }
        else{
            switch($lang){
                case 'en':
                    $lang = 'sp';
                    $en_alt = 'English';
                    break;
                case 'sp':
                    $lang = 'en';
                    $en_alt = 'Spanish';
                    break;
                default:
                    break;
            }
        }
        $request->session()->put('lang', $lang);
        $request->session()->put('en_alt', $en_alt);

        return redirect('exam/generate');
    }

}
