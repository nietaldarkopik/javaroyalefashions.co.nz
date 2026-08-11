<?php

namespace App\Repositories\Contracts;

use App\Models\Setting;

interface SettingRepositoryInterface
{
    /**
     * Fetch the single settings row, creating it with defaults if it
     * doesn't exist yet (e.g. on a fresh install before seeding runs).
     */
    public function current(): Setting;

    public function update(array $data): Setting;
}
