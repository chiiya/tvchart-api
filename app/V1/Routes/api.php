<?php declare(strict_types=1);

use App\V1\Http\Controllers\SeasonController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/shows/{year}/{season}', [SeasonController::class, 'index'])
    ->where('year', '20[2-3]\d')
    ->where('month', '(^0?[1-9]$)|(^1[0-2]$)')
    ->name('seasons.index');
