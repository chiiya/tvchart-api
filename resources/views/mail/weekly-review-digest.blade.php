<x-mail::message>
# Weekly Review Digest

There are currently **{{ $pendingCount }}** shows waiting for review.

@if ($importedLastWeek === 0)
⚠️ **No shows have been imported in the last 7 days.** The nightly import might be failing — check Horizon and the logs.
@else
{{ $importedLastWeek }} shows were imported in the last 7 days.
@endif
@if ($shows->isNotEmpty())

| Show | Air Date | Language | IMDB Votes |
|:-----|:---------|:---------|-----------:|
@foreach ($shows as $show)
| [{{ $show->name ?? $show->original_name }}]({{ route('filament.admin.resources.tv-shows.view', $show) }}) | {{ $show->first_air_date?->format('Y-m-d') }} | {{ $show->primary_language }} | {{ number_format($show->imdb_votes) }} |
@endforeach
@endif

<x-mail::button :url="route('filament.admin.resources.tv-shows.index')">
Open Review Queue
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
