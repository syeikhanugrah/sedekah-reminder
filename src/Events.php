<?php

namespace App;

final class Events
{
    /**
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     */
    public const REGISTRATION_SUCCESS = 'registration.success';
}
