<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitCluster extends Model
{
    protected $fillable = [
        'name',
        'address',
        'image',
        'description',
        // 'staff_id', // Remove this line
    ];

    // Remove this relationship method
    // public function staff()
    // {
    //     return $this->belongsTo(User::class, 'staff_id');
    // }

    public function allowedOccupantTypes()
    {
        return $this->belongsToMany(OccupantType::class, 'occupant_type_unit_cluster');
    }

    /**
     * Define a many-to-many relationship with User for staff assignments.
     */
    public function staffUsers()
    {
        return $this->belongsToMany(User::class, 'user_unit_cluster');
    }
}