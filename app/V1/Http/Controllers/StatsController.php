<?php declare(strict_types=1);

namespace App\V1\Http\Controllers;

use App\Domain\Services\TvSeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class StatsController extends Controller
{
    public function __construct(
        private readonly TvSeasonService $service,
    ) {}

    public function monthly(): JsonResponse
    {
        $data = $this->service->countByMonth();

        return response()->json($data);
    }
}
