<?php

namespace App\Traits;

use App\{Group, Contact};
use Illuminate\Support\Collection;
use App\Events\{ContactEvent, ContactEvents};
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasGroups
{
    /**
     * A model may have multiple groups.
     */
    public function groups(): MorphToMany
    {
        return $this->morphToMany(
            Group::class,
            'model',
            'model_has_groups'
            // 'model_id',
            // 'group_id'
        );
    }

    /**
     * Assign the given group to the model.
     *
     * @param array|string|App\Group ...$groups
     *
     * @return $this
     */
    public function assignGroup(...$groups)
    {
        $groups = collect($groups)
            ->flatten()
            ->map(function ($group) {
                if (empty($group)) {
                    return false;
                }

                return $this->getStoredGroup($group);
            })
            ->filter(function ($group) {
                return $group instanceof Group;
            })
            // ->each(function ($role) {
            //     $this->ensureModelSharesGuard($role);
            // })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->groups()->sync($groups, false);
            $model->load('groups');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($model) use ($groups) {
                    $model->groups()->sync($groups, false);
                });
        }

        // $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * Revoke the given group from the model.
     *
     * @param string|App\Group $group
     */
    public function removeGroup($group)
    {
        $this->groups()->detach($this->getStoredGroup($group));

        $this->load('groups');
    }

    /**
     * Remove all current group and set the given ones.
     *
     * @param array|App\Group|string ...$groups
     *
     * @return $this
     */
    public function syncGroups(...$groups)
    {
        $this->groups()->detach();

        $retval = $this->assignGroup($groups); 

        if ($this instanceof Contact){
            $event = new ContactEvent($this);
            $event->setGroup($groups[0]);
            event(ContactEvents::GROUP_SYNCED, $event);
        }

        return $retval;
    }

    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param string|int|array|App\Group $group
     *
     * @return bool
     */
    public function hasGroup($groups): bool
    {
        if (is_string($groups) && false !== strpos($groups, '|')) {
            $groups = $this->convertPipeToArray($groups);
        }

        if (is_string($groups)) {
            return $this->groups()->get()->contains('name', $groups);
        }

        if (is_int($groups)) {
            return $this->groups()->get()->contains('id', $groups);
        }

        if ($groups instanceof Group) {
            return $this->groups()->get()->contains('id', $groups->id);
        }

        if (is_array($groups)) {
            foreach ($groups as $group) {
                if ($this->hasGroup($group)) {
                    return true;
                }
            }

            return false;
        }

        return $groups->intersect($this->groups()->get())->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given group(s).
     *
     * @param string|array|App\Group|\Illuminate\Support\Collection $groups
     *
     * @return bool
     */
    public function hasAnyGroup($groups): bool
    {
        return $this->hasGroup($groups);
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param string|App\Group|\Illuminate\Support\Collection $groups
     *
     * @return bool
     */
    public function hasAllGroups($groups): bool
    {
        if (is_string($groups) && false !== strpos($groups, '|')) {
            $groups = $this->convertPipeToArray($groups);
        }

        if (is_string($groups)) {
            return $this->groups()->get()->contains('name', $groups);
        }

        if ($groups instanceof Group) {
            return $this->groups()->get()->contains('id', $groups->id);
        }

        $groups = collect()->make($groups)->map(function ($group) {
            return $group instanceof Group ? $group->name : $group;
        });

        return $groups->intersect($this->groups->pluck('name')) == $groups;
    }

    public function getGroupNames(): Collection
    {
        return $this->groups->pluck('name');
    }

    protected function getStoredGroup($group): Group
    {
    	$groupClass = Group::class;

        if (is_numeric($group)) {
            return app($groupClass)->find($group);
        }

        if (is_string($group)) {
            return app($groupClass)->whereName($group)->first();
        }

        return $group;
    }
    // protected function convertPipeToArray(string $pipeString)
    // {
    //     $pipeString = trim($pipeString);

    //     if (strlen($pipeString) <= 2) {
    //         return $pipeString;
    //     }

    //     $quoteCharacter = substr($pipeString, 0, 1);
    //     $endCharacter = substr($quoteCharacter, -1, 1);

    //     if ($quoteCharacter !== $endCharacter) {
    //         return explode('|', $pipeString);
    //     }

    //     if (! in_array($quoteCharacter, ["'", '"'])) {
    //         return explode('|', $pipeString);
    //     }

    //     return explode('|', trim($pipeString, $quoteCharacter));
    // }
}