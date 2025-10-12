<?php

namespace App\Traits;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;

trait ManageFiles
{
    /**
     * Upload a file to the specified directory with a unique name.
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @param string $directory
     * @param string $fileName
     * @return string $filePath
     */
    public function uploadFile($file, $directory, $fileName)
    {
        // Create a unique file name using uniqid
        $uniqueFileName = pathinfo($fileName, PATHINFO_FILENAME) . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Specify the destination directory within the public disk
        $destinationPath = public_path($directory);

        // Move the uploaded file to the destination directory
        $file->move($destinationPath, $uniqueFileName);

        // Construct the image path
        $filePath = $directory . '/' . $uniqueFileName;

        return $filePath;
    }

    /**
     * Delete a file from the specified path.
     *
     * @param string $filePath
     * @return bool
     */
    public function deleteFile($filePath)
    {
        return File::delete(public_path($filePath));
    }
}
