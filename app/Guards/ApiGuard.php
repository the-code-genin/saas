<?php

namespace App\Guards;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Contracts\Auth\Authenticatable;

class ApiGuard implements Guard
{
    protected $request;
    protected $provider;
    protected $user;

    /**
     * Create a new authentication guard.
     *
     * @param  \Illuminate\Contracts\Auth\UserProvider  $provider
     * @param  \Illuminate\Http\Request  $request
     * @return void
     */
    public function __construct(EloquentUserProvider $provider, Request $request)
    {
        $this->request = $request;
        $this->provider = $provider;
        $this->user = NULL;
    }

    /**
     * Get the authorization token from the current request
     *
     * @return string|null
     */
    protected function getAuthToken()
    {
        $token = $this->request->bearerToken();

        if (empty($token) || !is_string($token)) { // If authorization token is not set.
            return null;
        }

        if (!preg_match('/^(\d+\.(.+))$/i', $token, $matches)) {
            return null;
        }

        return $matches[1];
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check()
    {
        $user = $this->user;

        if (is_null($user)) {
            if (!$apiToken = $this->getAuthToken()) {
                return false;
            }

            $userId = @explode('.', $apiToken, 2)[0];

            /** @var User */
            if (is_null($user = $this->provider->retrieveById($userId))) { // If no user exists.
                return false;
            } else if ($user->tokens()->where('token', $apiToken)->count() != 1) { // If the token doesn't exist.
                return false;
            } else if ($user->status == 'banned'){
                return false;
            }

            // Remove an expired token
            $date = $user->tokens()->where('token', $apiToken)->first()->created_at;
            if (Carbon::now()->diffInDays(Carbon::createFromFormat('Y-m-d H:i:s', $date)) > 30) { // Expired token
                $user->tokens()->where('token', $apiToken)->forceDelete();
                return false;
            }

            $this->setUser($user);
        }

        return !is_null($user);
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest()
    {
        return !$this->check();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable|null
     */
    public function user()
    {
        if (!$this->check()) {
            return null;
        }

        return $this->user;
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id()
    {
        if ($user = $this->user()) {
            return $user->getAuthIdentifier();
        }
    }

    /**
     * Validate a user's credentials.
     *
     * @param  array  $credentials
     * @return bool
     */
    public function validate(array $credentials = [])
    {
        if (empty($credentials['username']) || empty($credentials['password'])) {
            return false;
        }

        $user = $this->provider->retrieveByCredentials($credentials);

        if (!is_null($user) && $this->provider->validateCredentials($user, $credentials)) {
            $this->setUser($user);
            return true;
        } else {
            return false;
        }
    }

    /**
     * Set the current user.
     *
     * @param  \Illuminate\Contracts\Auth\Authenticatable  $user
     * @return void
     */
    public function setUser(Authenticatable $user)
    {
        $this->user = $user;
        return $this;
    }
}
