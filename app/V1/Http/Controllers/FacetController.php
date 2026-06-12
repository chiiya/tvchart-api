<?php declare(strict_types=1);

namespace App\V1\Http\Controllers;

use App\Domain\Services\FacetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class FacetController extends Controller
{
    public function __construct(
        private readonly FacetService $service,
    ) {}

    public function index(): JsonResponse
    {
        return response()->json($this->service->facets());
    }
}
