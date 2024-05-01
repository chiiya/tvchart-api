@php
  /** @var \App\Domain\Models\TvShow $record */
@endphp

<x-filament::page>
  <div class="space-y-6">
    @if (session()->has('message'))
      <div class="alert alert-success shadow-lg mb-4">
        {{ session('message') }}
      </div>
    @endif
    <x-filament::card class="space-y-6">
      <div class="flex items-center justify-between mb-6">
        <h2 class="font-black text-2xl text-gray-900 mr-8">{{ $record->name }}</h2>
        @if ($record->status === \App\Domain\Enumerators\Status::WHITELISTED)
          <div class="rounded-lg px-4 py-2 text-sm text-white bg-green-500 font-medium">Whitelisted</div>
        @elseif ($record->status === \App\Domain\Enumerators\Status::BLACKLISTED)
          <div class="rounded-lg px-4 py-2 text-sm text-white bg-red-500 font-medium">Blacklisted</div>
        @elseif ($record->status === \App\Domain\Enumerators\Status::BLACKLISTED_FINAL)
          <div class="rounded-lg px-4 py-2 text-sm text-white bg-red-700 font-medium">Blacklisted Final</div>
        @elseif ($record->status === \App\Domain\Enumerators\Status::UNDECIDED)
          <div class="rounded-lg px-4 py-2 text-sm text-white bg-amber-500 font-medium">Undecided</div>
        @else
          <div class="rounded-lg px-4 py-2 text-sm bg-gray-200 font-medium">Unreviewed</div>
        @endif
      </div>

      <hr class="lm-divider"/>

      <div class="-mx-5 flex flex-wrap">
        <div class="px-5 w-full md:w-6/12 lg:w-8/12">
          <div class="overflow-x-auto">
            <table class="lm-table w-full">
              <tr>
                <th>First Aired</th>
                <td>
                  @if ($record->first_air_date?->gte(now()->subMonth()->startOfMonth()))
                    <div class="flex items-center gap-x-2 text-warning-500 font-bold">
                      <x-dynamic-component :component="'heroicon-s-exclamation'" class="w-6 h-6 text-warning-500" />
                      <span>{{ $record->first_air_date?->format('Y-m-d') }}</span>
                    </div>
                  @else
                    {{ $record->first_air_date?->format('Y-m-d') }}
                  @endif
                </td>
              </tr>
              <tr>
                <th>Name</th>
                <td>
                  <div>
                    {{ $record->name }}
                    @if ($record->name !== $record->original_name)
                      (<em>{{ $record->original_name }}</em>)
                    @endif
                  </div>
                </td>
              </tr>
              <tr>
                <th>Type</th>
                <td>{{ $record->type }}</td>
              </tr>
              <tr>
                <th>Languages</th>
                <td>
                  <div class="flex items-center">
                    @foreach ($record->languages as $language)
                      @if ($language->language_code === $record->primary_language)
                        <span
                          class="bg-primary-100 text-primary-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $language->name }}</span>
                      @else
                        <span
                          class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $language->name }}</span>
                      @endif
                    @endforeach
                  </div>
                </td>
              </tr>
              <tr>
                <th>Countries</th>
                <td>
                  <div class="flex items-center">
                    @foreach ($record->countries as $country)
                      <span
                        class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $country->name }}</span>
                    @endforeach
                  </div>
                </td>
              </tr>
              <tr>
                <th>Genres</th>
                <td>
                  <div class="flex items-center">
                    @foreach ($record->genres as $genre)
                      <span
                        class="bg-yellow-100 text-yellow-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $genre->name }}</span>
                    @endforeach
                  </div>
                </td>
              </tr>
              <tr>
                <th>Networks</th>
                <td>
                  <div class="flex items-center">
                    @foreach ($record->networks as $network)
                      @if ($network->isWhitelisted())
                        <span
                          class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $network->name }}</span>
                      @else
                        <span
                          class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $network->name }}</span>
                      @endif
                    @endforeach
                  </div>
                </td>
              </tr>
              <tr>
                <th>Trakt</th>
                <td>
                  <div @class([
                    'text-green-600 font-bold' => $record->trakt_members > 5000,
                    'text-red-500 font-bold' => $record->trakt_members < 100,
                  ])>{{ $record->trakt_members }} members
                  </div>
                </td>
              </tr>
              <tr>
                <th>IMDB</th>
                <td>
                  @if ($record->imdb_id !== null)
                    <div class="flex items-center gap-2">
                      <div @class([
                    'text-green-600 font-bold' => $record->imdb_votes > 5000,
                    'text-red-500 font-bold' => $record->imdb_votes < 100,
                  ])>
                        {{ number_format($record->imdb_score, 2) }} | {{ $record->imdb_votes }} votes
                      </div>
                      <span>/</span>
                      <a href="https://www.imdb.com/title/{{ $record->imdb_id }}/" target="_blank"
                         class="font-medium text-blue-600 dark:text-blue-500 hover:underline">
                        Link
                      </a>
                    </div>
                  @else
                    –
                  @endif
                </td>
              </tr>
            </table>
          </div>
        </div>
        <div class="px-5 w-full md:w-6/12 lg:w-4/12">
          <img src="{{ $record->present()->poster() }}"/>
        </div>
      </div>
      <hr class="lm-divider"/>
      <div class="overflow-x-auto">
        <table class="lm-table w-full">
          <tr>
            <th>US</th>
            <td>
              <div class="flex items-center">
                @foreach ($usWatchProviders as $provider)
                  @if ($provider->isWhitelisted())
                    <span
                      class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $provider->name }}</span>
                  @else
                    <span
                      class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $provider->name }}</span>
                  @endif
                @endforeach
              </div>
            </td>
          </tr>
          <tr>
            <th>DE</th>
            <td>
              <div class="flex items-center">
                @foreach ($deWatchProviders as $provider)
                  @if ($provider->isWhitelisted())
                    <span
                      class="bg-green-100 text-green-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $provider->name }}</span>
                  @else
                    <span
                      class="bg-gray-100 text-gray-800 text-xs font-semibold mr-2 px-2.5 py-0.5 rounded">{{ $provider->name }}</span>
                  @endif
                @endforeach
              </div>
            </td>
          </tr>
        </table>
      </div>
      <hr class="lm-divider"/>
      <div class="overflow-x-auto">
        <table class="lm-table w-full">
          <tr>
            <th>ID</th>
            <td><a href="https://www.themoviedb.org/tv/{{ $record->tmdb_id }}">{{ $record->tmdb_id }}</a></td>
          </tr>
          <tr>
            <th>Runtime</th>
            <td>{{ $record->runtime }} min.</td>
          </tr>
          <tr>
            <th>Rating</th>
            <td>{{ $record->content_rating ?? '–' }}</td>
          </tr>
          <tr>
            <th>Status</th>
            <td>{{ \Illuminate\Support\Str::headline($record->production_status) }}</td>
          </tr>
          <tr>
            <th>Summary</th>
            <td>{{ $record->summary }}</td>
          </tr>
          <tr>
            <th>Overview</th>
            <td>{{ $record->overview }}</td>
          </tr>
        </table>
      </div>
    </x-filament::card>

    @php
      $relationManagers = $this->getRelationManagers();
    @endphp

    @if (count($relationManagers))
      <x-filament::hr/>

      <x-filament::resources.relation-managers
        :active-manager="$activeRelationManager"
        :managers="$relationManagers"
        :owner-record="$record"
        :page-class="static::class"
      >
      </x-filament::resources.relation-managers>
    @endif
  </div>
</x-filament::page>
