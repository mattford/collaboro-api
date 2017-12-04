<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'username', 'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'id', 'password'
    ];
    
    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName()
    {
        return 'username';
    }
    
    /**
     * Hash password before storage
     *
     * @param $value Password value
     */
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }
    
    /**
     * Relationship with Privileges
     *
     */
    public function privileges()
    {
        return $this->belongsToMany('App\Privilege');
    }
    
    public function hasPrivilege($privilege)
    {
        $privs = $this->privileges()->where('slug', $privilege)->count();
        
        return ($privs > 0);
    }
}
