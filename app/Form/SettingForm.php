<?php

namespace App\Form;

use App\Models\Setting;
use Illuminate\Support\Facades\Gate;
use Livewire\Form;

class SettingForm extends Form
{
    public ?Setting $setting = null;

    public string $site_title = '';

    public string $site_short_title = '';

    public bool $enable_user_reg = true;

    public string $site_email = '';

    protected function rules(): array
    {
        return [
            'site_title' => ['required', 'string', 'max:255'],
            'site_short_title' => ['nullable', 'string', 'max:100'],
            'enable_user_reg' => ['boolean'],
            'site_email' => ['required', 'email:rfc', 'max:255'],
        ];
    }

    public function load(Setting $setting): void
    {
        $this->setting = $setting;

        $this->site_title = $setting->site_title ?? '';
        $this->site_short_title = $setting->site_short_title ?? '';
        $this->enable_user_reg = $setting->enable_user_reg ?? true;
        $this->site_email = $setting->site_email ?? '';
    }

    public function save(): Setting
    {
        $this->validate();

        Gate::authorize('update', Setting::class);

        $data = [
            'site_title' => $this->site_title,
            'site_short_title' => $this->site_short_title,
            'enable_user_reg' => $this->enable_user_reg,
            'site_email' => $this->site_email,
        ];

        if ($this->setting?->exists) {
            $this->setting->update($data);
        } else {
            $this->setting = Setting::create($data);
        }

        return $this->setting;
    }
}
