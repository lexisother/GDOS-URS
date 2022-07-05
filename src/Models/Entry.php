<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Entry extends Model
{
    /**
     * Indicates if the model should be timestamped.
     * 
     * @var bool
     */
    public $timestamps = false;

    /**
     * The table associated with the model.
     * 
     * @var string
     */
    protected $table = 'urenregistratie';

    /**
     * The primary key associated with the table.
     * 
     * @var string
     */
    protected $primary_key = 'urenregistratie_id';

    /**
     * The attributes that are mass assignable.
     * 
     * @var array<int, string>
     */
    protected $fillable = [
        'medewerker_id',
        'datum',
        'activiteit_id',
        'minuten',
    ];
}
