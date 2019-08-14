<?php

namespace QCod\AppSettings\Tests;

use Illuminate\Foundation\Testing\RefreshDatabase;

class AppSettingsTest extends TestCase
{
    use RefreshDatabase;

    /**
     * it_sets_a_key_value_in_settings_db
     *
     * @test
     */
    public function it_sets_a_key_value_in_settings_db()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text'
            ]
        ]);

        setting()->set('app_name', 'Cool App');

        $this->assertEquals('Cool App', setting('app_name'));
    }


    /**
     * it get a setting value falling back to default
     *
     * @test
     */
    public function it_get_a_setting_value_falling_back_to_default()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'value' => 'QCode App'
            ]
        ]);

        $this->assertEquals('QCode App', setting('app_name'));

        setting()->set('app_name', 'Cool App');

        $this->assertEquals('Cool App', setting('app_name'));
    }

    /**
     * it returns all the settings
     *
     * @test
     */
    public function it_returns_all_the_settings()
    {
        setting()->set('app_name', 'Cool App');
        setting()->set('app_email', 'noreply@example.com');

        $this->assertCount(2, setting()->all());
        $this->assertArraySubset([
            'app_name' => 'Cool App',
            'app_email' => 'noreply@example.com'],
            setting()->all()
        );
    }


    /**
     * it return value with type casting
     *
     * @test
     */
    public function it_return_value_with_type_casting()
    {
        $this->configureInputs([
            [
                'name' => 'app_online',
                'type' => 'boolean',
                'data_type' => 'bool',
                'value' => '1'
            ]
        ]);

        $this->assertTrue(setting('app_online'));
    }

    /**
     * it sets setting and creates if not present
     *
     * @test
     */
    public function it_sets_setting_and_creates_if_not_present()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
            ]
        ]);

        $this->assertNull(setting('app_name'));
        $this->assertDatabaseMissing('settings', ['name' => 'app_name']);

        setting()->set('app_name', 'Cool App');

        $this->assertEquals('Cool App', setting('app_name'));
        $this->assertDatabaseHas('settings', ['name' => 'app_name']);
    }

    /**
     * it can set setting without input definition
     *
     * @test
     */
    public function it_can_set_setting_without_input_definition()
    {
        $this->assertNull(setting('app_version'));
        $this->assertDatabaseMissing('settings', ['name' => 'app_version']);

        setting()->set('app_version', 'v1.0.1');

        $this->assertEquals('v1.0.1', setting('app_version'));
        $this->assertDatabaseHas('settings', ['name' => 'app_version']);
    }

    /**
     * it gives all the fields validation rules
     *
     * @test
     */
    public function it_gives_all_the_fields_validation_rules()
    {
        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text',
                'rules' => 'required|max:20',
            ],
            [
                'name' => 'app_punchline',
                'type' => 'text',
                'rules' => 'required|max:100',
            ],
            [
                'name' => 'app_year',
                'type' => 'text',
            ]
        ]);

        $validationRules = app('app-settings')->getValidationRules();

        $this->assertCount(2, $validationRules);
        $this->assertArraySubset([
            "app_name" => "required|max:20",
            "app_punchline" => "required|max:100",
        ], $validationRules);
    }

    /**
     * it gives default value of a setting
     *
     * @test
     */
    public function it_gives_default_value_of_a_setting()
    {
        $this->configureInputs([
            [
                'name' => 'app_maker',
                'type' => 'text',
                'value' => 'Saqueib'
            ],
            [
                'name' => 'app_logo',
                'type' => 'text',
            ]
        ]);

        $this->assertEquals('Saqueib', setting('app_maker'));
        $this->assertEquals('img/logo.svg', setting('app_logo', 'img/logo.svg'));
    }

    /**
     * it can store array input
     *
     * @test
     */
    public function it_can_store_array_input()
    {
        $this->configureInputs([
            [
                'name' => 'app_locations',
                'type' => 'multi_checkbox',
                'data_type' => 'array',
                'options' => [
                    'New Delhi', 'New York',
                    'Australia', 'France'
                ]
            ]
        ]);

        setting()->set('app_locations', ['New Delhi', 'France']);

        $this->assertArraySubset(['New Delhi', 'France'], setting('app_locations'));
    }


    /**
     * if calls mutator if defined on save setting
     *
     * @test
     */
    public function if_calls_mutator_if_defined_on_save_setting()
    {
        $this->configureInputs([
            [
                'name' => 'app_maker',
                'type' => 'text',
                'value' => 'Saqueib',
                'mutator' => function ($value) {
                    return 'mutated-' . $value;
                }
            ]
        ]);

        setting()->set('app_maker', 'apple');

        $this->assertEquals('mutated-apple', setting('app_maker'));
    }

    /**
     * it can call mutator from a class handle method
     *
     * @test
     */
    public function it_can_call_mutator_from_a_class_handle_method()
    {
        $this->configureInputs([
            [
                'name' => 'app_maker',
                'type' => 'text',
                'value' => 'Saqueib',
                'mutator' => '\QCod\AppSettings\Tests\Mutators\AppMakerMutator'
            ]
        ]);

        setting()->set('app_maker', 'apple');

        $this->assertEquals('class-mutated-apple', setting('app_maker'));
    }

    /**
     * it can call accessor on setting
     *
     * @test
     */
    public function it_can_call_accessor_on_setting()
    {
        $this->configureInputs([
            [
                'name' => 'app_maker',
                'type' => 'text',
                'value' => 'Saqueib',
                'accessor' => function ($value) {
                    return 'accessed-' . $value;
                }
            ]
        ]);

        setting()->set('app_maker', 'apple');

        $this->assertEquals('accessed-apple', setting('app_maker'));
    }

    /**
     * it_can_call_accessor_on_setting via class handle method
     *
     * @test
     */
    public function it_can_call_accessor_on_setting_via_class_handle_method()
    {
        $this->configureInputs([
            [
                'name' => 'app_maker',
                'type' => 'text',
                'value' => 'Saqueib',
                'accessor' => '\QCod\AppSettings\Tests\Accessors\AppMakerAccessor'
            ]
        ]);

        setting()->set('app_maker', 'apple');

        $this->assertEquals('class-accessed-apple', setting('app_maker'));
    }

    /**
     * it can access settings via facade
     *
     * @test
     */
    public function it_can_access_settings_via_facade()
    {
        \AppSettings::set('app_maker', 'apple');
        $this->assertEquals('apple', \AppSettings::get('app_maker'));
    }

    /**
     * it can set the group defined in config for settings
     *
     * @test
     */
    public function it_can_set_the_group_defined_in_config_for_settings()
    {
        config()->set('app_settings.setting_group', function () {
            return 'test_1';
        });

        $this->configureInputs([
            [
                'name' => 'app_name',
                'type' => 'text'
            ]
        ]);

        setting()->set('app_name', 'Cool App');

        $this->assertEquals('Cool App', setting('app_name'));

        $this->assertDatabaseHas('settings', [
            'name' => 'app_name',
            'val' => 'Cool App',
            'group' => 'test_1'
        ]);
    }
}
