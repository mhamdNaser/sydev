<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageController extends Controller
{
    public function convert(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'format' => 'required|in:jpg,png,webp,gif'
        ]);

        $imageFile = $request->file('image');
        $format = $request->input('format');

        // اختيار Driver تلقائيًا
        if (extension_loaded('gd')) {
            $driver = new GdDriver();
        } elseif (extension_loaded('imagick')) {
            $driver = new ImagickDriver();
        } else {
            return response()->json([
                'success' => false,
                'message' => 'No suitable image driver available on the server.'
            ], 500);
        }

        // إنشاء ImageManager
        $manager = new ImageManager($driver);

        try {
            // قراءة الصورة باستخدام read() بدل make()
            $image = $manager->read($imageFile->getPathname());

            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '.' . $format;

            $savePath = 'images/converted/' . $fileName;
            Storage::disk('public')->makeDirectory('images/converted');

            // حفظ الصورة بالصيغة المطلوبة وجودة 90%
            switch ($format) {
                case 'jpg':
                case 'jpeg':
                    $image->toJpeg()->save(storage_path('app/public/' . $savePath), 90);
                    break;
                case 'png':
                    $image->toPng()->save(storage_path('app/public/' . $savePath), 90);
                    break;
                case 'webp':
                    $image->toWebp()->save(storage_path('app/public/' . $savePath), 90);
                    break;
                case 'gif':
                    $image->toGif()->save(storage_path('app/public/' . $savePath));
                    break;
            }

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $savePath)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }
}
