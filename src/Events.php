<?php

namespace App;

final class Events
{
    /**
     * For the event naming conventions, see:
     * https://symfony.com/doc/current/components/event_dispatcher.html#naming-conventions.
     *
     * @Event("Symfony\Component\EventDispatcher\GenericEvent")
     *
     * @var string
     */
    const CALL_INCOMING = 'call.incoming';
    const DBF_FORM_SUBMIT = 'dbf.form.incoming';
    const DBF_CONTACT_FORM_SUBMIT = 'dbf.contact.form.incoming';
    const LANDING_FORM_SUBMIT = 'landing.form.incoming';
    const RGPD_COMMAND = 'rgpd.command';
}
