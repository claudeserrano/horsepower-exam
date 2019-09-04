<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Test extends Model
{
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
    * Get the exam's employee record
    */
    public function exam()
    {
        return $this->belongsTo('App\Exam');
    }

    /**
    * Get the answers of the test
    */
    public function answers()
    {
        return $this->hasMany('App\Answer')->orderBy('id');
    }
}
