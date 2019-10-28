<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-28
 * Time: 20:29
 */

namespace BlackIT\PrivyID;


trait PrivyIDAble
{
    public function privyids()
    {
        return $this->morphMany(PrivyIDAccount::class, 'privyable');
    }

    public function privyid(string $code)
    {
        return $this->privyids()->where('code', $code)->first();
    }

    public function bind(string $code)
    {
        $check = $this->privyid($code);
        if ($check != null)
            return $check;

        $account = new PrivyIDAccount();
        $account->code = $code;

        return $this->privyids()->save($account);
    }
}
