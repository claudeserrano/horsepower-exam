<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Answer extends Model
{
	/**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;


    /**
    * Get the test it belongs to.
    */
    public function test()
    {
        return $this->belongsTo('App\Test');
    }
}
