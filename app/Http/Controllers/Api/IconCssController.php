<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\IconFiles;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class IconCssController extends Controller
{
    public function generate()
    {
        // استخدم الكاش لتخفيف الضغط على السيرفر
        $css = Cache::remember('icons_css', 60 * 60, function () {
            $icons = DB::table('icons')->get();
            $css = "";

            foreach ($icons as $icon) {
                $svgPath = optional(IconFiles::where('icon_id', $icon->id)->where('file_type', 'svg')->first())->file_path;
                if (!file_exists($svgPath)) continue;

                $svgContent = file_get_contents($svgPath);
                $svgDataUri = 'data:image/svg+xml;base64,' . base64_encode($svgContent);

                $css .= ".sydev-{$icon->name} {
                    background: url('{$svgDataUri}') no-repeat center;
                    background-size: contain;
                    display: inline-block;
                    width: 1em;
                    height: 1em;
                }\n";
            }

            $css .= ".sydev { display:inline-block; vertical-align:middle; }";
            return $css;
        });

        return response($css, 200, ['Content-Type' => 'text/css']);
    }
}
