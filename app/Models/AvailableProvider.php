<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class AvailableProvider extends Model
{

    protected $table = 'available_providers';

    protected $fillable = [
        'name',
        'uuid',
        'template',
        'describe',        
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    /**
     * Obtener el registro para un nombre de provider 
     * @param  [type] $query    [description]
     * @param  [type] $provider [description]
     * @return [type]           [description]
     */
    public static function FindProvider(string $provider)
    {
        return Self::where('name', $provider)->get()->first();
    }

}
