<?php

namespace QCod\AppSettings;

use Illuminate\Http\Request;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Foundation\Validation\ValidatesRequests;

trait SavesSettings
{
    use ValidatesRequests;

    /**
     * Display the settings page
     *
     * @return \Illuminate\View\View
     * @param AppSettings $appSettings
     */
    public function index(AppSettings $appSettings,  string $page)
    {
        $settingsUI = $appSettings->loadConfig(config('app_settings', []));
        $settingViewName = config('app_settings.setting_page_view');

        $settingsPage = preg_replace("/[^A-Za-z0-9 ]/", '', $page);
        return view($settingViewName, [
            'settingsUI' => $settingsUI,
            'settingsPage' => $settingsPage,
        ]);
    }

    /**
     * Save settings
     *
     * @param Request $request
     * @param AppSettings $appSettings
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request, AppSettings $appSettings)
    {
        // validate the settings
        $this->validate($request, $appSettings->getValidationRules());

        // save settings
        $appSettings->save($request);

        return redirect()->route('settings.index', config('app_settings.default_page'))
            ->with([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);
    }
}
