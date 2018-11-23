<?php

namespace App\Contracts;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

interface Answer
{
    /**
     * The user who gave this answer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo;

    /**
     * The question for which this is an answer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function question(): BelongsTo;

    /**
     * The model who gave this answer.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function askable(): MorphTo;
}