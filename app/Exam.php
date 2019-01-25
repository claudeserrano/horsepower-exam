<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Exam extends Model
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
    public function employee()
    {
        return $this->belongsTo('App\Employee');
    }

    /**
    * Get the tests related to the exam record
    */
    public function tests()
    {
        return $this->hasMany('App\Test');
    }
}
