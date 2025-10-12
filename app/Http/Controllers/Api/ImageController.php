<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;

class ImageController extends Controller
{
    public function convert(Request $request)
    {
        // التحقق من وجود إمتداد GD
        if (!extension_loaded('gd') || !function_exists('gd_info')) {
            return response()->json([
                'success' => false,
                'message' => 'GD extension is not installed on the server'
            ], 500);
        }

        $request->validate([
            'image' => 'required|image|max:10240', // 10MB max
            'format' => 'required|in:jpg,png,webp,gif'
        ]);

        $imageFile = $request->file('image');
        $format = $request->input('format');

        try {
            // إنشاء ImageManager مع التحقق من الـ driver
            $manager = new ImageManager('gd');

            $image = $manager->make($imageFile->getPathname());

            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $fileName = $originalName . '.' . $format;
            $savePath = 'images/converted/' . $fileName;

            // إنشاء المجلد إذا لم يكن موجوداً
            Storage::disk('public')->makeDirectory('images/converted');

            // حفظ الصورة
            $fullPath = storage_path('app/public/' . $savePath);
            $image->save($fullPath, 90, $format);

            return response()->json([
                'success' => true,
                'url' => asset('storage/' . $savePath),
                'message' => 'Image converted successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage()
            ], 500);
        }
    }
}
