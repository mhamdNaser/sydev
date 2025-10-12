<?php

namespace App\Traits;

use Illuminate\Support\Facades\File;

trait ManageFiles
{
    /**
     * Upload file content to public directory with safe and unique name.
     *
     * @param  string  $fileContent  محتوى الملف (ناتج encode)
     * @param  string  $directory    مجلد التخزين داخل public (مثل: images/converted)
     * @param  string  $fileName     الاسم الأساسي للملف بدون امتداد
     * @param  string  $extension    الامتداد المطلوب (jpg, png, webp...)
     * @return string  المسار الكامل داخل public
     */
    public function uploadFile($fileContent, $directory, $fileName, $extension)
    {
        // إنشاء المجلد إذا لم يكن موجودًا
        $destination = public_path($directory);
        if (!File::exists($destination)) {
            File::makeDirectory($destination, 0755, true);
        }

        // تنظيف الاسم وإنشاء اسم فريد
        $safeName = preg_replace('/[^A-Za-z0-9_\-]/', '_', pathinfo($fileName, PATHINFO_FILENAME));
        $uniqueName = $safeName . '_' . uniqid() . '.' . $extension;

        // المسار الكامل داخل public
        $fullPath = $destination . '/' . $uniqueName;

        // حفظ المحتوى كملف فعلي
        File::put($fullPath, $fileContent);

        // المسار النسبي المستخدم بالرابط
        return $directory . '/' . $uniqueName;
    }

    /**
     * Delete a file from public folder
     */
    public function deleteFile($filePath)
    {
        $path = public_path($filePath);
        if (File::exists($path)) {
            return File::delete($path);
        }
        return false;
    }
}
