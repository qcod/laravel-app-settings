<?php

namespace QCod\AppSettings\Tests\Feature;

use QCod\AppSettings\Tests\TestCase;
use QCod\AppSettings\Setting\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * it shows default settings url
     *
     * @test
     */
    public function it_shows_default_settings_url()
    {
        // this should be default url
        $this->get('settings')
            ->assertStatus(200)
            ->assertViewIs('app_settings::settings_page');
    }

    /**
     * it validates setting from defined rules on inputs
     *
     * @test
     */
    public function it_validates_setting_from_defined_rules_on_inputs()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'rules' => 'required'
            ]
        ]);

        $this->post('settings', [])
            ->assertRedirect()
            ->assertSessionHasErrors([
                'app_name'
            ]);
    }

    /**
     * it saves setting into db on submit form
     *
     * @test
     */
    public function it_saves_setting_into_db_on_submit_form()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'rules' => 'required'
            ]
        ]);

        $appNameSetting = ['name' => 'app_name', 'val' => 'QCode App'];

        $this->assertDatabaseMissing('settings', $appNameSetting);

        $this->post('settings', ['app_name' => 'QCode App'])
            ->assertRedirect()
            ->assertSessionHas([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);

        $this->assertDatabaseHas('settings', $appNameSetting);
    }

    /**
     * it dont removes abandoned settings if its set in config
     *
     * @test
     */
    public function it_dont_removes_abandoned_settings_if_its_set_in_config()
    {
        config(['app_settings.remove_abandoned_settings' => false]);

        $oldSetting = ['name' => 'old_setting', 'val' => 'old data'];
        Setting::create($oldSetting);

        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'rules' => 'required'
            ]
        ]);

        $this->assertCount(1, Setting::all());

        $appNameSetting = ['name' => 'app_name', 'val' => 'QCode App'];
        $this->assertDatabaseMissing('settings', $appNameSetting);

        $payload = [
            'app_name' => 'QCode App'
        ];

        $this->post('settings', $payload)
            ->assertRedirect()
            ->assertSessionHas([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);

        $this->assertDatabaseHas('settings', $appNameSetting);
        $this->assertCount(2, Setting::all());
        $this->assertDatabaseHas('settings', $oldSetting);
    }

    /**
     * it removes abandoned settings on save
     *
     * @test
     */
    public function it_removes_abandoned_settings_on_save()
    {
        config(['app_settings.remove_abandoned_settings' => true]);

        $oldSetting = ['name' => 'old_setting', 'val' => 'old data'];
        Setting::create($oldSetting);

        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'rules' => 'required'
            ],
            [
                'name' => 'maintenance_mode',
                'type' => 'boolean'
            ]
        ]);


        $this->assertCount(1, Setting::all());
        $appNameSetting = ['name' => 'app_name', 'val' => 'QCode App'];
        $this->assertDatabaseMissing('settings', $appNameSetting);

        $payload = [
            'app_name' => 'QCode App',
            'maintenance_mode' => 1
        ];

        $this->post('settings', $payload)
            ->assertRedirect()
            ->assertSessionHas([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);

        $this->assertDatabaseHas('settings', $appNameSetting);
        $this->assertCount(2, Setting::all());
        $this->assertDatabaseMissing('settings', $oldSetting);
    }
}
