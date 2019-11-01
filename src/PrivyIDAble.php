<?php
/**
 * Created by IntelliJ IDEA.
 * User: subangkit
 * Date: 2019-10-28
 * Time: 20:29
 */

namespace BlackIT\PrivyID;


use BlackIT\PrivyID\Exceptions\PrivyIDAlreadyBindedException;
use mysql_xdevapi\Exception;

trait PrivyIDAble
{
    use PrivyIDUploadable;

    public function privyids()
    {
        return $this->morphMany(PrivyIDAccount::class, 'privyable');
    }

    public function privyid()
    {
        return $this->privyids()->orderBy('created_at','DESC')->first();
    }

    public function refreshToken() {
        $check = $this->privyid();
        if ($check) {
            $privyID = new PrivyID();
            $refreshTokenResponse = $privyID->refreshToken($check->refresh_token);

            $check->token = $refreshTokenResponse['access_token'];
            $check->refresh_token = $refreshTokenResponse['refresh_token'];
            $check->token_expired = date("Y-m-d H:i:s", $refreshTokenResponse['created_at']+$refreshTokenResponse['expires_in']);
            $check->save();

            return true;
        }

        return false;
    }

    public function updateIdentity() {
        $check = $this->privyid();
        if ($check) {
            $privyID = new PrivyID();
            $expired = new \DateTime($check->token_expired);
            $now = new \DateTime();

            if ($expired < $now) {
                $this->refreshToken();
                $check = $this->privyid();

                $identityResponse = $privyID->getUserIdentity($check->token);
                if (isset($identityResponse['data'])) {
                    $check->user_token = $identityResponse['data']['userToken'];
                }

                $check->identity_response_json = json_encode($identityResponse);
                $check->save();
            }

            return true;
        }

        return false;
    }

    /**
     * @param string $code
     * @return mixed
     * @throws \Exception
     */
    public function bind(string $code)
    {

        $check = $this->privyid();
        if ($check != null)
            return $check;

        $account = new PrivyIDAccount();
        $account->code = $code;

        $privyID = new PrivyID();
        // Get Token
        $tokenResponse = $privyID->getOAuthToken($code);
        $account->token = $tokenResponse['access_token'];
        $account->refresh_token = $tokenResponse['refresh_token'];
        $account->token_expired = date("Y-m-d H:i:s", $tokenResponse['created_at']+$tokenResponse['expires_in']);
        // Get userToken
        $identityResponse = $privyID->getUserIdentity($account->token);
        if (isset($identityResponse['data'])) {
            $account->user_token = $identityResponse['data']['userToken'];
            $account->privyId = $identityResponse['data']['privyId'];
            $account->identity_response_json = json_encode($identityResponse);

            $exist = PrivyIDAccount::where('privyId',$account->privyId)->first();
            if ($exist) {
                throw new \Exception($account->privyId.' already binded', 'PrivyIDAlreadyBinded');
            } else {
                return $this->privyids()->save($account);
            }
        }
    }
}
