<?php

namespace BlackBits\KeySecretApiAuthentication;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;


class KeySecretAuthenticatableModel extends Model implements AuthenticatableContract
{
    use Authenticatable;
}