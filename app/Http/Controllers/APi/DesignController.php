<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CreateDesignRequest;
use App\Http\Requests\DesignFilterRequest;
use App\Http\Requests\UpdateDesignRequest;
use App\Http\Resources\DesignResource;
use App\Http\Services\Api\DesignService;
use App\Models\Design;
use GuzzleHttp\Promise\Create;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class DesignController extends Controller
{
    protected $designservice;
    public function __construct(DesignService $designService)
    {
        $this->designservice = $designService;
    }
    public function index(DesignFilterRequest $request)
    {
        Gate::authorize('view', Design::class);
        $designs = $this->designservice->getAllDesigns($request->validated());
        return $this->success([DesignResource::collection($designs)], "Designs reterned successfully");
    }
    public function myDesigns(DesignFilterRequest $request)
    {
        Gate::authorize('viewAny', Design::class);
        $designs = $this->designservice->getAllMyDesigns($request->validated());
        return $this->success([DesignResource::collection($designs)], "your Designs reterned successfully");
    }
    public function create(CreateDesignRequest $request)
    {
        Gate::authorize('create', Design::class);
        $design = $this->designservice->createDesign($request->validated());
        return $this->success([new DesignResource($design)], "Design created successfully");
    }
    public function update(UpdateDesignRequest  $request, Design $design)
    {
        // dd([
        //     'all' => $request->all(),
        //     'validated' => $request->validated(),
        //     'has_image' => $request->hasFile('image'),
        //     'has_images' => $request->hasFile('images'),
        //     'design_before' => $design->toArray(),
        // ]);
        Gate::authorize('update', $design);
        $design = $this->designservice->updateDesign($design, $request->all());
        return $this->success([new DesignResource($design)], "Design updated successfully");
    }



    public function destroy(Design $design)
    {
        Gate::authorize('delete', $design);
        $design->delete();
        return $this->success([], "Design deleted successfully");
    }
}
