<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
    * Get the employee's exams
    */
    public function exams()
    {
        return $this->hasMany('App\Exam');
    }

    /**
    * Get the employee's tests
    */
    public function tests()
    {
        return $this->hasManyThrough('App\Test', 'App\Exam');
    }

    public function averageOverallScore(){
        $tests = $this->tests;
        $total = 0;

        foreach($tests as $test){
            $total += $test->score;
        }

        return sizeof($tests) > 0 ? $total / sizeof($tests) : 0;
    }
    
    public function getClassAvgScore($exam_id, $level){
        $answers = $this->tests;
        return sizeof($answers);
        $temp = 0;
        
        if(isset($answers)){
            for($a = 1; $a <= 3; $a++){
                $temp += $answers->where('question', '=', $a + (3 * $level))->first()->correct;
            }
        }
        return number_format($temp / 3 * 100,2);
    }
}
