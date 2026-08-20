<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Support\ImageNormalizer;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    private const KEYS = [
        'site_name', 'hero_kicker', 'hero_title', 'hero_text', 'hero_image',
        'step_1_title', 'step_1_desc', 'step_2_title', 'step_2_desc', 'step_3_title', 'step_3_desc', 'step_4_title', 'step_4_desc',
        'hero_kicker', 'hero_title_1', 'hero_title_2', 'hero_title_3', 'hero_text', 'hero_caption_title', 'hero_caption_text',
        'stat_years', 'stat_operations', 'stat_properties', 'stat_recommend',
        'op_anticretico_desc', 'op_anticretico_price', 'op_anticretico_image', 'op_anticretico_points',
        'op_venta_desc', 'op_venta_price', 'op_venta_image',
        'op_alquiler_desc', 'op_alquiler_price', 'op_alquiler_image',
        'about_title', 'about_text', 'about_image_1', 'about_image_2',
        'owner_cta_title', 'owner_cta_text',
        'owner_sell_title', 'owner_sell_desc', 'owner_sell_points', 'owner_sell_stat',
        'owner_rent_title', 'owner_rent_desc', 'owner_rent_points', 'owner_rent_stat',
        'owner_anti_title', 'owner_anti_desc', 'owner_anti_points', 'owner_anti_stat',
        'contact_address', 'contact_whatsapp', 'contact_phone', 'contact_email', 'contact_hours',
        'footer_text',
        'social_tiktok', 'social_instagram', 'social_facebook',
    ];

    private const UPLOADS = [
        'hero_image_file'           => 'hero_image',
        'about_image_1_file'        => 'about_image_1',
        'about_image_2_file'        => 'about_image_2',
        'op_anticretico_image_file' => 'op_anticretico_image',
        'op_venta_image_file'       => 'op_venta_image',
        'op_alquiler_image_file'    => 'op_alquiler_image',
    ];

    private const RATIOS = [
        'hero_image_file'           => [16 / 9, 1800],
        'about_image_1_file'        => [4 / 3, 1200],
        'about_image_2_file'        => [4 / 3, 800],
        'op_anticretico_image_file' => [4 / 3, 1200],
        'op_venta_image_file'       => [4 / 3, 1200],
        'op_alquiler_image_file'    => [4 / 3, 1200],
    ];

    public function edit()
    {
        return view('admin.settings.edit');
    }

    public function update(Request $request)
    {
        $uploadedKeys = [];

        foreach (self::UPLOADS as $field => $settingKey) {
            if ($request->hasFile($field)) {
                $request->validate([$field => ['image', 'max:20480']]);
                $path = $request->file($field)->store('settings', 'public');
                [$ratio, $maxW] = self::RATIOS[$field] ?? [4 / 3, 1600];
                $path = ImageNormalizer::normalizeStored($path, $ratio, $maxW);
                Setting::set($settingKey, asset('storage/' . $path));
                $uploadedKeys[] = $settingKey;
            }
        }

        foreach ($request->input('settings', []) as $key => $value) {
            if (in_array($key, self::KEYS, true) && !in_array($key, $uploadedKeys, true)) {
                Setting::set($key, $value);
            }
        }

        return back()->with('success', 'Configuración guardada.');
    }
}