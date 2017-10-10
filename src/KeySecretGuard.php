<?php

namespace BlackBits\KeySecretApiAuthentication;

use Illuminate\Auth\GuardHelpers;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Http\Request;


class KeySecretGuard implements Guard
{
    use GuardHelpers;

    protected $request;
    protected $storageKey, $storageSecret;


    public function __construct(UserProvider $provider)
    {
        $this->provider = $provider;
        $this->request  = Request::capture();

        $this->storageKey    = 'key';
        $this->storageSecret = 'secret';
    }


    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        // If we've already retrieved the user for the current request we can just
        // return it back immediately. We do not want to fetch the user data on
        // every call to this method because that would be tremendously slow.
        if (! is_null($this->user)) {
            return $this->user;
        }

        $user = null;

        $credentials = $this->getCredentialsForRequest();

        if (! empty($credentials)) {
            $user = $this->provider->retrieveByCredentials(
                $credentials
            );
        }

        return $this->user = $user;
    }


    /**
     * Validate a user's credentials.
     *
     * @param  array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials[$this->storageKey] || empty($credentials[$this->storageSecret]) )) {
            return false;
        }

        if ($this->provider->retrieveByCredentials($credentials)) {
            return true;
        }

        return false;
    }

    /**
     * Get Credentials from bearer token
     *
     * @return array
     */
    private function getCredentialsForRequest()
    {
        $token = base64_decode($this->request->bearerToken());

        list($credentials['key'], $credentials['secret']) = explode(':', $token);

        return $credentials ?: [];
    }

}