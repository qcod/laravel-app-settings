<?php

namespace QCod\AppSettings\Tests\Feature;

use Illuminate\Support\Facades\DB;
use QCod\AppSettings\Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class SettingUITest extends TestCase
{
    use RefreshDatabase;

    /**
     * it shows defined settings section with title description
     *
     * @test
     */
    public function it_shows_defined_settings_section_with_title_description()
    {
        // configure
        config(['app_settings.sections' => [
            'app' => [
                'title' => 'General Settings',
                'descriptions' => 'Application general settings.',
                'icon' => 'fa fa-cog'
            ]
        ]
        ]);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('General Settings')
            ->assertSee('Application general settings.')
            ->assertSee('fa fa-cog');
    }

    /**
     * it can change the classes for section and input by config
     *
     * @test
     */
    public function it_can_change_the_classes_for_section_and_input_by_config()
    {
        // configure
        config(['app_settings.section_class' => 'c-card']);
        config(['app_settings.section_heading_class' => 'c-card-header']);
        config(['app_settings.section_body_class' => 'c-card-body']);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('class="c-card')
            ->assertSee('class="c-card-header')
            ->assertSee('class="c-card-body');
    }

    /**
     * it can change the submit button text
     *
     * @test
     */
    public function it_can_change_the_submit_button_text()
    {
        // configure
        config(['app_settings.submit_btn_text' => 'Submit']);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('Submit');
    }

    /**
     * it shows inputs defined in section
     *
     * @test
     */
    public function it_shows_inputs_defined_in_section()
    {
        // configure
        $inputs = [
            [
                'type' => 'text',
                'name' => 'from_email',
                'placeholder' => 'From Email',
                'label' => 'From Email of app',
                'hint' => 'You can from email of app'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('From Email')
            ->assertSee('From Email of app')
            ->assertSee('You can from email of app')
            ->assertSee('type="text"');
    }

    /**
     * it shows defined  email type input
     *
     * @test
     */
    public function it_shows_defined_email_type_input()
    {
        // configure
        $inputs = [
            [
                'type' => 'email',
                'name' => 'from_email',
                'placeholder' => 'From Email',
                'label' => 'From Email of app',
                'hint' => 'You can from email of app'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('type="email"');
    }

    /**
     * it shows number type input
     *
     * @test
     */
    public function it_shows_number_type_input()
    {
        // configure
        $inputs = [
            [
                'type' => 'number',
                'name' => 'number_of_tries'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('type="number"');
    }

    /**
     * it shows select input with option
     *
     * @test
     */
    public function it_shows_select_input_with_options()
    {
        // configure
        $inputs = [
            [
                'type' => 'select',
                'name' => 'country',
                'options' => ['IN' => 'India']
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('select')
            ->assertSee('IN');
    }

    /**
     * it can populate options from database dynamically
     *
     * @test
     */
    public function it_can_populate_options_from_database_dynamically()
    {
        $this->configureInputs([
            [
                'name' => 'app_migrations',
                'label' => 'App Migration',
                'type' => 'select',
                'options' => function () {
                    return DB::table('migrations')->pluck('migration', 'id')->toArray();
                }
            ]
        ]);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('select')
            ->assertSee('2014_10_00_000000_create_settings_table');
    }

    /**
     * it shows textarea input
     *
     * @test
     */
    public function it_shows_textarea_input()
    {
        // configure
        $inputs = [
            [
                'type' => 'textarea',
                'name' => 'message'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('textarea')
            ->assertSee('message');
    }

    /**
     * its shows checkbox
     *
     * @test
     */
    public function its_shows_checkbox()
    {
        // configure
        $inputs = [
            [
                'type' => 'checkbox',
                'label' => 'Only Confirmed user',
                'name' => 'only_confirmed',
                'value' => '1'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('checkbox')
            ->assertSee('Only Confirmed user');
    }

    /**
     * it shows a boolean input which can be a select or radio input
     *
     * @test
     */
    public function it_shows_a_boolean_input_which_can_be_a_select_or_radio_input()
    {
        // configure
        $inputs = [
            [
                'type' => 'boolean',
                'name' => 'maintenance_mode',
                'label' => 'Maintenance',
                'true_label' => 'Yes',
                'false_label' => 'No',
                'true_value' => '1',
                'false_value' => '0',
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('type="radio"')
            ->assertSee('Maintenance');


        // configure with options to show as select
        $inputs = [
            [
                'type' => 'boolean',
                'name' => 'maintenance_mode',
                'label' => 'Maintenance',
                'options' => [
                    '1' => 'Yes',
                    '0' => 'No',
                ]
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('select')
            ->assertSee('Yes')
            ->assertSee('No')
            ->assertSee('Maintenance');
    }

    /**
     * it shows file input type on image
     *
     * @test
     */
    public function it_shows_file_input_type_on_image()
    {
        // configure
        $inputs = [
            [
                'type' => 'image',
                'name' => 'logo',
                'label' => 'Upload Logo'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('type="file"');
    }

    /**
     * it shows file input on type of file
     *
     * @test
     */
    public function it_shows_file_input_on_type_of_file()
    {
        // configure
        $inputs = [
            [
                'type' => 'file',
                'name' => 'tc',
                'label' => 'Upload Terms and Conditions'
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('type="file"');
    }
    
    /**
    * it overrides input group class
    * 
    * @test
    */
    public function it_overrides_input_group_class()
    {
        config(['app_settings.input_wrapper_class' => 'new-input-wrapper']);

        $this->withoutExceptionHandling();
        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('class="new-input-wrapper')
            ->assertDontSee('class="input-group');
    }

    /**
     * it shows a friendly notifice when un supported type input defined
     *
     * @test
     */
    public function it_shows_a_friendly_notifice_when_un_supported_type_input_defined()
    {
        // configure
        $inputs = [
            [
                'type' => 'future_input',
                'label' => 'Any new input',
                'name' => 'New Input',
            ]
        ];

        $this->configureInputs($inputs);

        // assert
        $this->get('/settings')
            ->assertStatus(200)
            ->assertSee('future_input')
            ->assertSee('not supported');
    }
}
