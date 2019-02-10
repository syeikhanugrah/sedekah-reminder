<?php

namespace App;

final class Events
{
    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    public const REGISTRATION_SUCCESS = 'registration.success';

    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    public const RESET_PASSWORD_INIT = 'reset.password.init';

    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    public const RESET_PASSWORD_SUCCESS = 'reset.password.success';
}
