<?php
namespace Tests\Traits;

use App\Models\User;
trait ActingJWTUser
{
    public function JWTACtingAs(User $user)
    {
        $token = \AUth::guard('api')->fromUser($user);
        $this->withHeaders(['Authorization' => 'Bearer '.$token]);
        return $this;
    }
}
