<?php

namespace App\Http\Traits;

use Illuminate\Support\Facades\Auth;

trait AuthorizePerson
{
    public function getCurrentAuthUser(string $guard = 'customers')
    {
        return Auth::guard($guard)->user();
    }

    public function getCurrentAuthId(string $guard = 'customers')
    {
        return $this->getCurrentAuthUser($guard)->id;
    }

    public function getTerminal()
    {
        return $this->getCurrentAuthUser('terminal')->terminal;
    }

    public function authorizedPerson(int $model_id = 0, string $guard = 'customers')
    {
        if ($model_id === $this->getCurrentAuthId($guard)) {
            return true;
        }
        abort(404);
    }

    public function authorizedTerminal(int $model_terminal_id = 0)
    {
        if ($model_terminal_id === $this->getTerminal()->id) {
            return true;
        }
        abort(404);
    }
}
