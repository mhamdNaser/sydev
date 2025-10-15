<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Interfaces\IconRepositoryInterface;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Encoders\PngEncoder;
use Illuminate\Support\Str;
use App\Models\Icon;
use App\Traits\ManageFiles;

class IconRepository implements IconRepositoryInterface
{
    use ManageFiles;

    protected $model;

    public function __construct(Icon $icon)
    {
        $this->model = $icon;
    }

    public function all()
    {
        return $this->model->with('category', 'user')->get();
    }

    public function find(int $id)
    {
        return $this->model->with('category', 'user')->findOrFail($id);
    }

    public function create(array $data)
    {
        $slug = Str::slug($data['title']);
        $storagePath = public_path('icons');
        if (!file_exists($storagePath)) mkdir($storagePath, 0755, true);

        // حفظ SVG كنص
        $svgPath = $this->uploadFile($data['icon_text'], 'icons', $slug, 'svg');

        // تحويل SVG إلى PNG
        $driver = extension_loaded('imagick') ? new ImagickDriver() : new GdDriver();
        $manager = new ImageManager($driver);
        $image = $manager->read($data['icon_text']); // قراءة الـ SVG
        $encoded = $image->encode(new PngEncoder());
        $pngPath = $this->uploadFile($encoded, 'icons', $slug, 'png');

        $data['file_svg'] = $svgPath;
        $data['file_png'] = $pngPath;
        unset($data['icon_text']);

        return $this->model->create($data);
    }

    public function update(int $id, array $data)
    {
        $icon = $this->find($id);
        $icon->update($data);
        return $icon;
    }

    public function delete(int $id)
    {
        $icon = $this->find($id);
        return $icon->delete();
    }

    public function toggleStatus(int $id)
    {
        $icon = $this->find($id);
        $icon->is_active = !$icon->is_active;
        $icon->save();
        return $icon;
    }
}
