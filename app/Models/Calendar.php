<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'year',
        'active'
    ];

    public function timesheets()
    {
        return $this->hasMany(Timesheet::class);
    }
    
    public function holidays()
    {
        return $this->hasMany(Holiday::class);
    }

    public static function active()
    {
        return static::where('active', true)->first();
    }
}
