<?php

namespace App\Repositories\Eloquent;

use App\Models\Language;
use App\Repositories\Interfaces\LanguageRepositoryInterface;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

class LanguageRepository implements LanguageRepositoryInterface
{
    public function getAllLanguages()
    {
        return Language::all();
    }

    public function getActiveLanguages()
    {
        return Language::where("status", 1)->get();
    }

    public function createLanguage(array $data)
    {
        $language = Language::create($data);
        $this->createLanguageFiles($language->slug);
        return $language;
    }

    public function addWordToAdminFile($slug, Request $request)
    {
        try {
            $key = $request->input('key');
            $translation = $request->input('value');

            $adminFilePath = resource_path("lang/{$slug}/admin.php");

            if (!File::exists(dirname($adminFilePath))) {
                File::makeDirectory(dirname($adminFilePath), 0755, true);
            }

            $adminData = [];

            if (File::exists($adminFilePath)) {
                $adminData = include $adminFilePath;
                if (!is_array($adminData)) {
                    $adminData = [];
                }
            }

            $adminData[$key] = $translation;
            $phpCode = "<?php\n\nreturn " . var_export($adminData, true) . ";\n";
            File::put($adminFilePath, $phpCode);

            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function getLanguageBySlug($slug)
    {
        $languageDir = resource_path("lang/{$slug}");

        if (!File::exists($languageDir)) {
            return null;
        }

        $combinedData = [];
        $adminFilePath = "{$languageDir}/admin.php";
        if (File::exists($adminFilePath)) {
            $adminData = include $adminFilePath;
            if (is_array($adminData)) {
                foreach ($adminData as $key => $value) {
                    $combinedData[] = ['key' => $key, 'value' => $value];
                }
            }
        }

        return $combinedData;
    }

    public function updateLanguageStatus($id)
    {
        $language = Language::findOrFail($id);
        $language->update([
            'status' => $language->status == 1 ? 0 : 1,
        ]);

        return $language;
    }

    public function deleteLanguage($id)
    {
        $language = Language::findOrFail($id);
        $language->delete();

        $languageDir = resource_path("lang/{$language->slug}");
        if (File::exists($languageDir)) {
            File::deleteDirectory($languageDir);
        }

        return true;
    }

    protected function createLanguageFiles($slug)
    {
        $languageDir = resource_path("lang/{$slug}");

        if (!File::exists($languageDir)) {
            File::makeDirectory($languageDir, 0755, true);
        }

        $adminContent = "<?php\n\nreturn [\n    // مصفوفة للإدارة\n];\n";
        File::put("{$languageDir}/admin.php", $adminContent);
    }
}
