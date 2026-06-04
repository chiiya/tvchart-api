## Project

`tvchart-api` is the **core backend and API** of a little hobby project, a tv show discovery platform.
The frontend displays all tv shows airing in a given month, and allows users to manage their individual status
(plan to watch, watching, completed, not watching). Shows that user is not interested in (not watching) can be hidden,
leading to a focused, simple grid view of all shows airing in the given month that the user is interested in.

### Data

To accomplish this, the API must be able to provide information about ALL tv shows airing at a given time. This
requires combining multiple data sources:

- TMDB: The primary data source. Changes are fetched nightly so that our database is always up to date.
- IMDB: Only used to update the IMDB score and votes count for a tv show.
- Trakt: Used for runtime, genres and trakt members (as another heuristic)
- OMDB: Used for summary and genres

All data sources except TMDB only update the records fetched from TMDB with additional information.

One big challenge is filtering the list of TV shows coming from TMDB to a reasonable list useful to most users.
Since TMDB is an international platform there are literally thousands of new TV shows coming out every month,
all around the world. Even though we provide filtering on the frontend, we cannot expect users to go through hundreds
or thousands of records to find what they're interested in. That's why we have several heuristics in place, that
whitelist or blacklist a show at import time:

- Some genres are blacklisted (e.g. reality, kids, sports, anime)
- Documentaries are blacklisted except when belonging to a big international network (e.g. Netflix)
- Some networks are blacklisted (e.g. Disney XD)
- Older shows are blacklisted (oldest supported year is 2010)
- Certain languages from productive countries are blacklisted, unless the show is available on an international network (e.g. Netflix)
- Foreign (non-english) shows are blacklisted unless the first air date is recent, since they might become available internationally
- Popular shows are automatically whitelisted

This reduces the list of shows a little bit, but one central challenge remains: at the time where we need to decide whether
to white- or blacklist a tv show, not too much is known about it. Popularity or rating cannot be used as a factor yet,
because the first air date is still in the future. 

Currently, a manual review process is still in place, through the Filament admin.

### Codebase Structure

```
Modules/
├── Application  # Bootstrapping, shared utilities, base abstractions
├── Domain   # Domain models and logic
├── Filament     # Admin
├── V1     # First version of the API
```

### Coding Rules

#### Style & Linting

Before finalizing any change:

```bash
just lint
```

Uses:

* php-cs-fixer
* ecs
* rector
* tlint

#### Static Analysis / Quality

Before finalizing any change:

```bash
just quality
```

Uses:

* phpstan

All detected issues **must be fixed**.

### Architecture Principles

Strictly follow:

* **KISS**
* **SOLID**
* **Clarity > cleverness**
* **Explicit code > compact code**
* Avoid Laravel “magic” when possible
* Prefer:
  * explicit services
  * clear boundaries
  * predictable flows
  * readable logic

Do not introduce unnecessary abstractions.
