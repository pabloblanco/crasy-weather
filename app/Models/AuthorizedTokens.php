<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuthorizedTokens extends Model
{
    use HasFactory;

   protected $table = 'authorized_tokens';

  protected $fillable = [
    'token',
    'environment',
    'status',
    'api'
  ];

  public $timestamps = true;

  /**
   * [isAuthorizedToken Retorna si es valido el token de conexion]
   * @return boolean [description]
   */
  public static function isAuthorizedToken( $request ) {

    $environment = env('APP_ENV', 'local');

    $authorizedIpId = self::select('authorized_ips.id')
      ->join('authorized_ips',
        'authorized_ips.token',
        'authorized_tokens.token')
      ->where([
        ['authorized_ips.token', $request->bearerToken()],
        ['authorized_tokens.environment', $environment],
        ['authorized_tokens.status', 'enabled'],
        ['authorized_ips.status', 'enabled'],
        ['authorized_ips.api', env('APP_NAME')],
        ['authorized_tokens.api', env('APP_NAME')],
        ['authorized_ips.ip', $request->ip()]])
      ->first();

    if (!empty($authorizedIpId)) {
      return true;
    }
    return false;
  }

}