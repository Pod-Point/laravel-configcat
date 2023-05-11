<?php

namespace PodPoint\ConfigCat\Support;

class DefaultUserTransformer
{
    public function __invoke(\Illuminate\Foundation\Auth\User $user)
    {
        return new \ConfigCat\User($user->getKey(), $user->email);
    }
}
