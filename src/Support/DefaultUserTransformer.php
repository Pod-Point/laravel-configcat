<?php

namespace PodPoint\ConfigCat\Support;

class DefaultUserTransformer
{
    public function __invoke(\Illuminate\Foundation\Auth\User $user): \ConfigCat\User
    {
        return new \ConfigCat\User($user->getKey(), $user->email);
    }
}
