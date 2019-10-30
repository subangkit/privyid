<?php
/**
 * Created by PhpStorm.
 * User: subangkit
 * Date: 2019-01-25
 * Time: 16:41
 */

namespace BlackIT\PrivyID;

use Illuminate\Database\Eloquent\Model;

class PrivyIDAccount extends Model
{
    protected $fillable = ['code','token','user_token','token_expired','code_expired', 'refresh_token', 'identity_response_json'];
    protected $table = 'privyids';

    public function privyable()
    {
        return $this->morphTo();
    }
}
