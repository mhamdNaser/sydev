<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Image as InterventionImage;

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

        // إنشاء ImageManager بدون Facade
        $manager = new ImageManager('gd'); // لاحظ: string وليس array

        /** @var InterventionImage $image */
        $image = $manager->make($imageFile->getPathname());

        $originalName = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = $originalName . '.' . $format;

        $savePath = 'images/converted/' . $fileName;

        Storage::disk('public')->makeDirectory('images/converted');

        $image->save(storage_path('app/public/' . $savePath), 90, $format);

        return response()->json([
            'success' => true,
            'url' => asset('storage/' . $savePath)
        ]);
    }
}
