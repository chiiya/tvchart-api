<?php declare(strict_types=1);

namespace App\V1\Http\Controllers;

use App\Domain\Services\TvSeasonService;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;

class SeasonController extends Controller
{
    public function __construct(
        private readonly TvSeasonService $service,
    ) {}

    public function index(string|int $year, string|int $month): JsonResponse
    {
        $shows = $this->service->getShowsForMonth((int) $year, (int) $month);

        return response()->json(['data' => $shows->all()]);
    }
}
