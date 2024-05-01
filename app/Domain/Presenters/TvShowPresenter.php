<?php declare(strict_types=1);

namespace App\Domain\Presenters;

use App\Domain\Models\TvShow;
use Chiiya\Common\Presenter\Presenter;

/** @extends Presenter<TvShow> */
class TvShowPresenter extends Presenter
{
    public function poster(): string
    {
        return "https://image.tmdb.org/t/p/w500/{$this->entity->poster}";
    }
}
