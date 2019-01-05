<?php

namespace App\Traits;

use App\Area;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

trait HasAreas
{
    /**
     * A model may have multiple areas.
     */
    public function areas(): MorphToMany
    {
        return $this->morphToMany(
            Area::class,
            'model',
            'model_has_areas'
            // 'model_id',
            // 'area_id'
        );
    }

    /**
     * Assign the given area to the model.
     *
     * @param array|string|App\Area ...$areas
     *
     * @return $this
     */
    public function assignArea(...$areas)
    {
        $areas = collect($areas)
            ->flatten()
            ->map(function ($area) {
                if (empty($area)) {
                    return false;
                }

                return $this->getStoredArea($area);
            })
            ->filter(function ($area) {
                return $area instanceof Area;
            })
            // ->each(function ($role) {
            //     $this->ensureModelSharesGuard($role);
            // })
            ->map->id
            ->all();

        $model = $this->getModel();

        if ($model->exists) {
            $this->areas()->sync($areas, false);
            $model->load('areas');
        } else {
            $class = \get_class($model);

            $class::saved(
                function ($model) use ($areas) {
                    $model->areas()->sync($areas, false);
                });
        }

        // $this->forgetCachedPermissions();

        return $this;
    }

    /**
     * Revoke the given area from the model.
     *
     * @param string|App\Area $area
     */
    public function removeArea($area)
    {
        $this->areas()->detach($this->getStoredArea($area));

        $this->load('areas');
    }

    /**
     * Remove all current area and set the given ones.
     *
     * @param array|App\Area|string ...$areas
     *
     * @return $this
     */
    public function syncAreas(...$areas)
    {
        $this->areas()->detach();

        return $this->assignArea($areas);
    }

    /**
     * Determine if the model has (one of) the given role(s).
     *
     * @param string|int|array|App\Area $area
     *
     * @return bool
     */
    public function hasArea($areas): bool
    {
        if (is_string($areas) && false !== strpos($areas, '|')) {
            $areas = $this->convertPipeToArray($areas);
        }

        if (is_string($areas)) {
            return $this->areas()->get()->contains('name', $areas);
        }

        if (is_int($areas)) {
            return $this->areas()->get()->contains('id', $areas);
        }

        if ($areas instanceof Area) {
            return $this->areas()->get()->contains('id', $areas->id);
        }

        if (is_array($areas)) {
            foreach ($areas as $area) {
                if ($this->hasArea($area)) {
                    return true;
                }
            }

            return false;
        }

        return $areas->intersect($this->areas()->get())->isNotEmpty();
    }

    /**
     * Determine if the model has any of the given area(s).
     *
     * @param string|array|App\Area|\Illuminate\Support\Collection $areas
     *
     * @return bool
     */
    public function hasAnyArea($areas): bool
    {
        return $this->hasArea($areas);
    }

    /**
     * Determine if the model has all of the given role(s).
     *
     * @param string|App\Area|\Illuminate\Support\Collection $areas
     *
     * @return bool
     */
    public function hasAllAreas($areas): bool
    {
        if (is_string($areas) && false !== strpos($areas, '|')) {
            $areas = $this->convertPipeToArray($areas);
        }

        if (is_string($areas)) {
            return $this->areas()->get()->contains('name', $areas);
        }

        if ($areas instanceof Area) {
            return $this->areas()->get()->contains('id', $areas->id);
        }

        $areas = collect()->make($areas)->map(function ($area) {
            return $area instanceof Area ? $area->name : $area;
        });

        return $areas->intersect($this->areas->pluck('name')) == $areas;
    }

    public function getAreaNames(): Collection
    {
        return $this->areas->pluck('name');
    }

    protected function getStoredArea($area): Area
    {
    	$areaClass = Area::class;

        if (is_numeric($area)) {
            return app($areaClass)->find($area);
        }

        if (is_string($area)) {
            return app($areaClass)->whereName($area)->first();
        }

        return $area;
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