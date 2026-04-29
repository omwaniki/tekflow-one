<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;

class SettingsController extends Controller
{
    /**
     * Show Appearance Page
     */
    public function appearance()
    {
        $color = Setting::where('key', 'primary_color')->value('value') ?? '#0f172a';

        return view('settings.appearance', [
            'primaryColor' => $color
        ]);
    }

    /**
     * Update Appearance Settings
     */
    public function updateAppearance(Request $request)
    {
        $request->validate([
            'primary_color' => 'required|string'
        ]);

        $primary = $request->primary_color;

        // Generate shades
        $dark = $this->adjustBrightness($primary, -40);
        $light = $this->adjustBrightness($primary, 180);

        Setting::updateOrCreate(['key' => 'primary_color'], ['value' => $primary]);
        Setting::updateOrCreate(['key' => 'primary_dark'], ['value' => $dark]);
        Setting::updateOrCreate(['key' => 'primary_light'], ['value' => $light]);

        return back()->with('success', 'Theme updated successfully');
    }

    private function adjustBrightness($hex, $steps)
    {
        $steps = max(-255, min(255, $steps));

        $hex = str_replace('#', '', $hex);

        $r = hexdec(substr($hex, 0, 2));
        $g = hexdec(substr($hex, 2, 2));
        $b = hexdec(substr($hex, 4, 2));

        $r = max(0, min(255, $r + $steps));
        $g = max(0, min(255, $g + $steps));
        $b = max(0, min(255, $b + $steps));

        return sprintf("#%02x%02x%02x", $r, $g, $b);
    }
}