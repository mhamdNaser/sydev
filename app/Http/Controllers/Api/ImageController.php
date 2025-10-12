<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Traits\ManageFiles;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;

class ImageController extends Controller
{
    use ManageFiles;

    public function convert(Request $request)
    {
        $request->validate([
            'image' => 'required|image',
            'format' => 'required|in:jpg,png,webp,gif'
        ]);

        $imageFile = $request->file('image');
        $format = $request->input('format');

        // اختيار Driver تلقائيًا
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $manager = new ImageManager($driver);

        try {
            // قراءة الصورة
            $image = $manager->read($imageFile->getPathname());

            // توليد اسم آمن للملف
            $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
            $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalName);

            // مسار التخزين داخل public
            $directory = 'images/converted';

            // تحويل الصورة حسب الصيغة المطلوبة
            $convertedContent = match ($format) {
                'jpg', 'jpeg' => $image->toJpeg(90)->encode(),
                'png' => $image->toPng(90)->encode(),
                'webp' => $image->toWebp(90)->encode(),
                'gif' => $image->toGif()->encode(),
                default => throw new \Exception('Unsupported format'),
            };

            // رفع الصورة (فعليًا حفظها داخل public/)
            $relativePath = $this->uploadFile($convertedContent, $directory, $safeName, $format);

            // توليد رابط مباشر
            $fullUrl = url($relativePath);

            return response()->json([
                'success' => true,
                'url' => $fullUrl,
            ], 200, [], JSON_UNESCAPED_SLASHES);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing image: ' . $e->getMessage(),
            ], 500);
        }
    }
}
