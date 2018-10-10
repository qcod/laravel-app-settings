<?php

namespace QCod\AppSettings\Tests\Feature;

use Illuminate\Http\UploadedFile;
use QCod\Settings\Setting\Setting;
use QCod\AppSettings\Tests\TestCase;
use Illuminate\Support\Facades\Storage;
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

    /**
     * it uploads file and stores path on image type or file type inputs
     *
     * @test
     */
    public function it_uploads_file_and_stores_path_on_image_type_or_file_type_inputs()
    {
        $this->configureInputs([
            [
                'name' => 'logo',
                'type' => 'image',
                'label' => 'Upload logo',
                'hint' => 'Must be an image and cropped in desired size',
                'rules' => 'image|max:500',
                'disk' => 'public',
                'path' => 'app'
            ]
        ]);

        Storage::fake();

        $logo = UploadedFile::fake()->image('logo.jpg');

        $appNameSetting = ['name' => 'logo', 'val' => 'app/' . $logo->hashName()];

        $this->assertDatabaseMissing('settings', $appNameSetting);

        $this->post('settings', ['logo' => $logo])
            ->assertRedirect()
            ->assertSessionHas([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);

        $this->assertDatabaseHas('settings', $appNameSetting);
    }

    /**
     * it does not auto upload file if a mutator is defined
     *
     * @test
     */
    public function it_does_not_auto_upload_file_if_a_mutator_is_defined()
    {
        $this->configureInputs([
            [
                'name' => 'logo',
                'type' => 'image',
                'label' => 'Upload logo',
                'hint' => 'Must be an image and cropped in desired size',
                'rules' => 'image|max:500',
                'disk' => 'public',
                'path' => 'app',
                'mutator' => function ($value, $key) {
                    return null;
                }
            ]
        ]);

        Storage::fake();

        $logo = UploadedFile::fake()->image('logo.jpg');

        $appNameSetting = ['name' => 'logo', 'val' => null];

        $this->assertDatabaseMissing('settings', $appNameSetting);

        $this->post('settings', ['logo' => $logo])
            ->assertRedirect()
            ->assertSessionHas([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);

        $this->assertDatabaseHas('settings', $appNameSetting);
    }
}
