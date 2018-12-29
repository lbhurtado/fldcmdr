<?php

namespace App\Observers;

use App\Contact;
use App\Events\{ContactEvent, ContactEvents};

class ContactObserver
{
    public function created(Contact $contact)
    {
        event(ContactEvents::CREATED, new ContactEvent($contact));
    }
}
