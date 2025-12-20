<?php

namespace App\Http\Controllers\Web\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\Web\Admin\DesignService;
use App\Models\Design;
use App\Models\DesignOption;

class DesignController extends Controller
{
    protected $designService;

    public function __construct(DesignService $designService)
    {
        $this->designService = $designService;
    }


    public function index()
    {
        $designs = Design::get();
        $colors = DesignOption::where('type', 'color')->get();
        $sleeves = DesignOption::where('type', 'sleeve')->get();
        $domes = DesignOption::where('type', 'dome')->get();
        $fabrics = DesignOption::where('type', 'fabric')->get();
        return view('admin.designs', compact('designs', 'colors', 'sleeves', 'domes', 'fabrics'));
    }
    public function getDesignDetails($id)
    {
        $design = Design::with(['user', 'sizes', 'designOptions'])->findOrFail($id);

        $designName = is_string($design->name) ? json_decode($design->name, true) : $design->name;
        $displayDesignName = is_array($designName) ? ($designName['ar'] ?? $designName['en'] ?? 'غير محدد') : $design->name;
        $designDescription = is_string($design->description) ? json_decode($design->description, true) : $design->description;
        $displayDesignDescription = is_array($designDescription) ? ($designDescription['ar'] ?? $designDescription['en'] ?? 'غير محدد') : $design->description;

        $sizes = $design->sizes->map(function ($size) {
            $sizeName = is_string($size->name) ? json_decode($size->name, true) : $size->name;
            return [
                'name' => is_array($sizeName) ? ($sizeName['ar'] ?? $sizeName['en'] ?? 'غير محدد') : $size->name
            ];
        });

        return response()->json([
            'id' => $design->id,
            'name' => $displayDesignName,
            'description' => $displayDesignDescription,
            'user_name' => $design->user->name,
            'image' => $design->image,
            'price' => number_format($design->price, 2),
            'sizes' => $sizes,
            'colors' => $design->designOptions->where('type', 'color')->map(fn($item) => ['name' => $item->getTranslation('name', 'ar')])->values(),
            'sleeves' => $design->designOptions->where('type', 'sleeve')->map(fn($item) => ['name' => $item->getTranslation('name', 'ar')])->values(),
            'domes' => $design->designOptions->where('type', 'dome')->map(fn($item) => ['name' => $item->getTranslation('name', 'ar')])->values(),
            'fabrics' => $design->designOptions->where('type', 'fabric')->map(fn($item) => ['name' => $item->getTranslation('name', 'ar')])->values(),
            'created_at' => $design->created_at->format('Y-m-d H:i'),
        ]);
    }
}
