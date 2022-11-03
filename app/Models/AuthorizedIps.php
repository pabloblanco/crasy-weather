<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedIps extends Model
{

  use HasFactory;

  protected $table = 'authorized_ips';

  protected $fillable = [
    'id',           
    'token',        
    'ip',          
    'status',       
    'propietario', 
    'api'
  ];         

  /**
   * [isIpValid Consulta si la IP de donde hace la peticion a la api esta registrada y activa]
   * @param  [type]  $ipRequest [description]
   * @return boolean            [description]
   */
  public static function isIpAuthorized($ipRequested)
  {

    $ip = self::where([['ip', $ipRequested], ['api', env('APP_NAME')], ['status', 'enabled']])->first();

    if (!empty($ip)) {
      return true;
    }
    return false;
  }

}