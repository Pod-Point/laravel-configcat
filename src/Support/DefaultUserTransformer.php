<?php

namespace PodPoint\ConfigCat\Support;

class DefaultUserTransformer
{
    public function __invoke(\App\Models\User $user)
    {
        return new \ConfigCat\User($user->id, $user->email);
    }
}
