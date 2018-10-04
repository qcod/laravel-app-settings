<?php

namespace QCod\AppSettings\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use QCod\AppSettings\Setting\AppSettings;
use Illuminate\Foundation\Validation\ValidatesRequests;

class AppSettingController extends Controller
{
    use ValidatesRequests;

    /**
     * Display the settings page
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $settingsUI = config('app_settings', []);
        $settingViewName = config('app_settings.setting_page_view');

        return view($settingViewName, compact('settingsUI'));
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

        return redirect(config('app_settings.url', '/'))
            ->with([
                'status' => config('app_settings.submit_success_message', 'Settings Saved.')
            ]);
    }
}
