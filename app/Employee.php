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
    * Get the employee's exam
    */
    public function exam()
    {
        return $this->hasOne('App\Exam');
    }

    /**
    * Get the employee's tests
    */
    public function tests()
    {
        return $this->hasManyThrough('App\Test', 'App\Exam');
    }

    /**
     * Get average overall score
     */
    public function averageOverallScore(){
        $tests = $this->tests;
        $total = 0;

        foreach($tests as $test){
            $total += $test->score;
        }

        return sizeof($tests) > 0 ? $total / sizeof($tests) : 0;
    }
    
    /**
     * Generate average score for specified level
     */  
    public function getClassAvgScore($exam_id, $level){
        //  Get test of school classification
        $test = $this->tests()->where('template_id', '=', 'hpeElec4')->where('exam_id', '=', $exam_id)->first();

        $temp = 0;
        $max_index = 3 * $level;
        $min_index = $max_index - 3;

        if(isset($test)){
            $answers = $test->answers;
            for($i = $min_index; $i < $max_index; $i++){
                $temp += $answers[$i]->correct;
            }
        }
        else
            return 0;
            
        return number_format($temp / 3 * 100,2);
    }
}
