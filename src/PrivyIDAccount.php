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
    protected $fillable = ['code'];

    public function privyable()
    {
        return $this->morphTo();
    }
}
