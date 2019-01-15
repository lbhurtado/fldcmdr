<?php

namespace App\Listeners;

use App\{Contact, Tag};
use Spatie\Permission\Models\Role;
use App\Events\{ContactEvent, ContactEvents};
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;


class ContactEventSubscriber
{
    public function onContactAreaSynced(ContactEvent $event)
    {
        $contact = $event->getContact();
        $area = $event->getArea();
        Tag::all()->each(function ($tag) use ($contact, $area) {
            if ($tag->tagger instanceof Contact)
                if ($tag->tagger->id == $contact->id) {
                    $tag->setArea($area, true);
                }
        });
    }

    public function onContactGroupSynced(ContactEvent $event)
    {
        $contact = $event->getContact();
        $group = $event->getGroup();
        Tag::all()->each(function ($tag) use ($contact, $group) {
            if ($tag->tagger instanceof Contact)
                if ($tag->tagger->id == $contact->id) {
                    $tag->setGroup($group, true);
                }
        });
    }

    public function subscribe($events)
    {
        $events->listen(
            ContactEvents::AREA_SYNCED, 
            ContactEventSubscriber::class.'@onContactAreaSynced'
        );

        $events->listen(
            ContactEvents::GROUP_SYNCED, 
            ContactEventSubscriber::class.'@onContactGroupSynced'
        );
    }  
}
