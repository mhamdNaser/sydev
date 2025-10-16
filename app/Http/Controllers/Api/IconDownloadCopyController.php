<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class IconDownloadCopyController extends Controller
{
    public function download($fileName)
    {
        $path = 'icons/' . $fileName;

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.',
            ], 404);
        }

        // نرسل الملف مباشرة باسم الملف الأصلي
        return response()->download($path, $fileName, [
            'Content-Type' => mime_content_type($path),
        ]);
    }

    public function getIconCode($fileName)
    {
        $path = public_path('icons/' . $fileName);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ], 404);
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($extension === 'svg') {
            $content = file_get_contents($path);
            return response()->json([
                'success' => true,
                'type' => 'svg',
                'code' => $content,
            ]);
        } else {
            $data = base64_encode(file_get_contents($path));
            $mime = mime_content_type($path);
            $base64String = "data:$mime;base64,$data";

            return response()->json([
                'success' => true,
                'type' => $extension,
                'code' => $base64String,
            ]);
        }
    }

    public function getIconCodeJsx($fileName)
    {
        $path = public_path('icons/' . $fileName);

        if (!file_exists($path)) {
            return response()->json([
                'success' => false,
                'message' => 'File not found.'
            ], 404);
        }

        $extension = pathinfo($path, PATHINFO_EXTENSION);

        if ($extension === 'svg') {
            $content = file_get_contents($path);
            return response()->json([
                'success' => true,
                'type' => 'svg',
                'code' => $content,
            ]);
        } else {
            $data = base64_encode(file_get_contents($path));
            $mime = mime_content_type($path);
            $base64String = "data:$mime;base64,$data";

            return response()->json([
                'success' => true,
                'type' => $extension,
                'code' => $base64String,
            ]);
        }
    }
}
