Development checklist for local setup

This project uses Laravel (backend) and Next.js (frontend). For local development (dev servers running on different ports), these environment keys are important to enable Sanctum-based SPA authentication and cookie handling.

Required env keys (local development):

- NEXT_PUBLIC_API_URL
  - Purpose: Frontend should call the backend by absolute origin during local development to avoid proxy/cookie forwarding issues.
  - Example: NEXT_PUBLIC_API_URL=http://localhost:8000

- CORS_ALLOWED_ORIGINS
  - Purpose: Controls allowed origins returned by Laravel's CORS config. Must list frontend origin(s) explicitly when requests use credentials (cookies).
  - Example: CORS_ALLOWED_ORIGINS=http://localhost:3000

- SESSION_DOMAIN
  - Purpose: Domain for session cookies. For local dev, set to 'localhost' (or empty depending on your environment).
  - Example: SESSION_DOMAIN=localhost

- SANCTUM_STATEFUL_DOMAINS
  - Purpose: List of stateful domains trusted by Laravel Sanctum for SPA authentication.
  - Example: SANCTUM_STATEFUL_DOMAINS=localhost:3000,localhost

Notes & recommendations
- In development the frontend is served at http://localhost:3000 and the Laravel backend at http://localhost:8000.
- The backend must return Access-Control-Allow-Origin set to the exact frontend origin (not '*') when requests include credentials.
- config/cors.php in this repository reads CORS_ALLOWED_ORIGINS from .env; ensure you set it and run php artisan config:clear.
- For local convenience, frontend/.env.local contains NEXT_PUBLIC_API_URL=http://localhost:8000 — keep that while developing.

Migrations
- A defensive migration exists: database/migrations/2026_08_17_140000_add_missing_columns_to_users_table.php
  - Status: pending on this installation (it may be applied locally to reconcile an older packaged sqlite DB).
  - Recommendation: Keep this migration in the repository for local development compatibility. Review and remove before deploying to production only if you standardize the migration history across environments.

Quick test (manual)
1. Start Laravel: php artisan serve --host=127.0.0.1 --port=8000
2. Start Next dev frontend: cd frontend && npm run dev
3. Open http://localhost:3000 in a browser and login with seeded admin credentials: admin@school.com / password
4. Verify in DevTools Network that /sanctum/csrf-cookie, /login, /api/user and /api/notifications succeed and cookies are set/sent.

Automated headless integration test
- A simple CLI integration script is provided at scripts/integration_sanctum_flow.php. Run it against running servers:

  php scripts/integration_sanctum_flow.php

It will exercise: GET /sanctum/csrf-cookie -> POST /login -> GET /api/user -> GET /api/notifications -> POST /logout

If any step fails, inspect the Laravel logs at storage/logs/laravel.log and verify your .env values.

Developer note: API routes
- routes/api.php is the single source of truth for all /api/* endpoints.
- For local development this application includes api.php from routes/web.php using the /api prefix so the API URIs are exposed at /api/* while keeping api.php as the canonical place for API route definitions.
- To revert this behavior, remove the require/include block in routes/web.php and restore any temporary web.php API definitions (changes are reversible).