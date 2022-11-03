<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Class RequestsStats.
 *
 * @author  Pablo Blanco <pa_blanco@hotmail.com>
 *
 * @OA\Schema(
 *     title="RequestsStats model",
 *     description="RequestsStats model",
 * )
 */
class RequestsStats extends Model
{

    /**
     * @OA\Property(
     *     title="fillable",
     *     description="The attributes that are mass assignable.",
     *     @OA\Items(
     *         type="string",
     *     )     
     * )
     *
     * @var array[]
     */
    protected $fillable = [
        'success',
        'city',
        'response',
        'ip',
        'temperature',
        'playlist',
        'created_at'
    ];

    /**
     * @OA\Property(
     *     default="2017-02-02 18:31:45",
     *     format="datetime",
     *     description="Request date",
     *     title="Request date",
     *     type="string"
     * )
     *
     * @var \DateTime
     */
    public $timestamps = true;
    
    /**
     * @OA\Property(
     *     description="The attributes that should be hidden for serialization.",
     *     title="hidden",
     *     @OA\Items(
     *         type="string",
     *     )
     * )
     *
     * @var array
     */
    protected $hidden = [

    ];

    /**
     * @OA\Property(
     *     description="The attributes that should be cast",
     *     title="casts",
     *     @OA\Items(
     *         type="string",
     *     )
     * )
     *
     * @var array
     */
    protected $casts = [

    ];
}
