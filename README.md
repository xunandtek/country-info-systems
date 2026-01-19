# Country Information System (Laravel Technical Assessment)

This project is a **Country Information System** built with **Laravel + MySQL**.  
It demonstrates backend engineering best practices including:

- Clean Laravel architecture (models, service layer, commands)
- Normalized database schema with self-referential relationships (borders)
- External API integration with retry + caching
- Full-text search + region filtering
- Analytics tracking (page views)
- Trending countries (most viewed in last 24 hours)
- Minimal Blade frontend (backend-focused)

---

## Tech Stack

- Laravel (11/12 compatible)
- PHP (recommended 8.2+)
- MySQL 8+ (FULLTEXT search supported)
- REST Countries API (v3.1)
- Cache driver: file (default) or Redis (optional)

---

## Setup Instructions

### 1) Clone the repository

```bash
git clone https://github.com/<your-username>/<your-repo>.git
cd <your-repo>
2) Install PHP dependencies
bash
Copy code
composer install
3) Configure environment
Copy .env.example and configure your DB:

bash
Copy code
cp .env.example .env
php artisan key:generate
Edit .env:

env
Copy code
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=country_info
DB_USERNAME=root
DB_PASSWORD=

REST_COUNTRIES_BASE_URL=https://restcountries.com/v3.1
COUNTRY_API_CACHE_TTL=86400
TRENDING_CACHE_TTL=300
HTTP_VERIFY_SSL=true
4) Run migrations
bash
Copy code
php artisan migrate
5) Sync country data from REST Countries API
This imports countries into the local database and builds border relationships.

bash
Copy code
php artisan countries:sync --fresh-borders
6) Recalculate trending countries
Trending = most viewed in the last 24 hours:

bash
Copy code
php artisan trending:recalculate --limit=10
7) Start the app
bash
Copy code
php artisan serve
Then open:

arduino
Copy code
http://127.0.0.1:8000/countries
Core Features
✅ Countries Database (Local Storage)
Countries are stored locally in MySQL to avoid repeated API calls and to allow fast querying/filtering.

Table: countries

Includes:

country codes (cca2, cca3)

name and official name

capital

region/subregion

population

flag URLs

✅ Borders (Self-Referential Relationship)
Borders are stored using a normalized pivot table:

Table: country_borders

This creates a many-to-many self-referential relationship:

Country → belongsToMany → borders (Country)

✅ API Integration Layer (Service Class)
The REST Countries API integration is isolated in:

app/Services/CountryApiService.php

It includes:

Laravel HTTP client usage

retry logic

timeout handling

caching to avoid excessive API calls

Supported methods:

fetchAllCountries()

fetchCountryByCode(string $code)

searchCountries(string $query)

✅ Search & Filtering
The country listing supports:

Full-text search (name_common + capital)

Filter by region

Pagination

Full-text search uses MySQL FULLTEXT index for performance.

✅ Analytics & Tracking
Each country detail view logs an entry to:

Table: country_views

Fields include:

country_id

viewed_at

✅ Trending Countries
Trending countries are calculated based on views in the last 24 hours:

Table: trending_countries

This is updated by:

bash
Copy code
php artisan trending:recalculate
Minimal Frontend
The UI is intentionally minimal and built with Blade:

/countries = list + search + region filter + trending

/countries/{code} = detail page with borders

The frontend exists primarily to demonstrate backend functionality.

API Documentation (Optional)
This project primarily focuses on backend architecture and Blade UI.

Version 2.0 recommendation includes building full JSON API routes and OpenAPI documentation.

Assumptions
REST Countries API is used as the primary source of truth for country data

Country code uniqueness is enforced using cca2 and cca3

Trending is based on total views in the last 24 hours (simple + effective)

Database is MySQL 8+ to support FULLTEXT search

Issues Encountered During Development
1) MySQL migration error: key too long (1071)
Error example:

vbnet
Copy code
Specified key was too long; max key length is 1000 bytes
Cause:

MySQL + utf8mb4 index size limitations on older versions/configs.

Fix:

Set default string length in AppServiceProvider:

php
Copy code
Schema::defaultStringLength(191);
2) SSL error when calling REST Countries API (cURL error 60)
Error example:

vbnet
Copy code
cURL error 60: SSL certificate problem: unable to get local issuer certificate
Cause:

Local environment missing a valid CA certificate bundle.

Fix options:

Proper fix (recommended): install CA bundle and configure php.ini

Dev-only workaround: allow SSL verify to be configurable via .env

This project supports:

env
Copy code
HTTP_VERIFY_SSL=true
If needed temporarily:

env
Copy code
HTTP_VERIFY_SSL=false
3) PHP syntax compatibility issue (readonly)
Cause:

Some environments run older PHP where readonly properties are not supported.

Fix:

Removed readonly usage in the service class for compatibility.

4) Request helper compatibility issue (Request::string())
Error:

cpp
Copy code
Method Illuminate\Http\Request::string does not exist.
Fix:

Replaced with universal Laravel support:

php
Copy code
$request->input('q', '')
Recommendations for Version 2.0
If expanding this project further, recommended improvements include:

✅ 1) Add a full JSON REST API layer
GET /api/countries

GET /api/countries/{code}

GET /api/countries/search?q=...

GET /api/trending

Use:

Laravel API Resources

consistent error responses

proper status codes

✅ 2) Add OpenAPI / Swagger documentation
Provide interactive documentation for:

endpoints

query params (region, q)

pagination responses

✅ 3) Use Redis caching + Laravel Horizon
replace file cache with Redis

monitor cache, queues, and job execution

support higher traffic performance

✅ 4) Move sync to background jobs
Instead of running sync manually:

dispatch job to import countries in chunks

queue border linking as a second job

✅ 5) Improve trending logic
prevent repeat refresh spamming (optional IP/session de-dupe)

add rolling trending snapshots (hourly stats)

add "top trending by region"

✅ 6) Add automated testing suite
Feature tests:

listing filters

searching

detail page view tracking

Unit tests:

trending calculation logic

service error handling

✅ 7) Add Docker for consistent setup
Provide:

docker-compose.yml for MySQL + app

one-command startup for reviewers

Useful Commands
bash
Copy code
php artisan migrate:fresh
php artisan countries:sync --fresh-borders
php artisan trending:recalculate --limit=10
php artisan serve
