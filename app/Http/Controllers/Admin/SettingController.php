<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateSettingRequest;
use App\Services\SettingService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function __construct(
        private readonly SettingService $settings,
    ) {}

    public function edit(): View
    {
        return view('admin.settings.edit', [
            'setting' => $this->settings->current(),
        ]);
    }

    public function update(UpdateSettingRequest $request): RedirectResponse
    {
        $data = $request->safe()->except(['logo', 'favicon']);
        // Unchecked checkboxes aren't submitted at all, so leaving this to
        // safe() would mean the DB value can only ever flip to true and
        // never back to false once the box is unticked.
        $data['show_site_name_with_logo'] = $request->boolean('show_site_name_with_logo');
        $current = $this->settings->current();

        if ($request->hasFile('logo')) {
            if ($current->logo_path) {
                Storage::disk('public')->delete($current->logo_path);
            }
            $data['logo_path'] = $request->file('logo')->store('settings', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($current->favicon_path) {
                Storage::disk('public')->delete($current->favicon_path);
            }
            $data['favicon_path'] = $request->file('favicon')->store('settings', 'public');
        }

        $this->settings->update($data);

        return back()->with('status', 'Settings updated.');
    }
}
