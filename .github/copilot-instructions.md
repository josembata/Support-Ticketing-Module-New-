## Quick context

This repository is a Laravel 12 (PHP >= 8.2) ticketing application. Key runtime pieces:
- Backend: `app/` (Models, Http/Controllers, Events, Notifications, Mail)
- Frontend assets: Vite + `resources/js`, `resources/css` (package.json + laravel-echo + pusher)
- Broadcasting: Pusher configured (`BROADCAST_DRIVER=pusher` in `.env`, see `config/broadcasting.php`)
- Queue: database queue is used by default (`QUEUE_CONNECTION=database`). Many broadcasts/events rely on a running queue worker.

## Big-picture architecture (what to know first)
- Tickets are stored in `app/Models/Ticket` (note: primary key is `ticket_id`, not `id`).
- Comments: `app/Models/TicketComment` (primary key `comment_id`). Attachments are stored via the `public` disk (see `TicketCommentController::store`).
- Notifications: `app/Notifications/*` (mail + database channels). Example: `TicketCommentNotification` builds mail/database payloads from a `TicketComment` instance.
- Events: `app/Events/NewCommentAdded` implements `ShouldBroadcast` and broadcasts to a private channel named `ticket.{ticket_id}` with event name `comment.added`.
- Controllers: business logic lives in `app/Http/Controllers` (see `TicketController` and `TicketCommentController` for the main flows: create ticket, assign agent, add comment, record history).

## Common data flows to reference directly
- Creating a ticket: `TicketController::store` -> `TicketAssignedNotification` to assigned agent.
- Updating status: `TicketController::update` -> `TicketHistory` entry + `TicketStatusUpdatedNotification` to ticket creator.
- Adding a comment: `TicketCommentController::store` -> creates `TicketComment`, notifies the other party, fires `NewCommentAdded` event (broadcast). See `app/Events/NewCommentAdded.php` and `app/Notifications/TicketCommentNotification.php`.

## Project-specific conventions and gotchas
- Non-standard primary keys: many models use `*_id` primary keys (e.g., `ticket_id`, `comment_id`). Never assume `id` exists.
- User relationship columns: `created_by` and `assigned_to` map to users; relationship accessor names are `creator()` and `assignedAgent()` (check `app/Models/Ticket.php`).
- Attachments: saved with `$request->file('attachment')->store('attachments', 'public')` — remember to run `php artisan storage:link` in dev.
- Broadcasting uses private channels. Client must authenticate via the normal Laravel broadcasting auth endpoint; check `routes/channels.php` if custom gates are needed.
- Many event broadcasts implement `ShouldBroadcast` (they are queued). A running queue worker is required for on-time delivery.

## Useful commands (Windows PowerShell notes)
- Install and init (runs migrations + npm build via composer script):
  - `composer install` then `composer run setup`
- Start a full local dev environment (concurrently runs server, queue listener, logs and vite):
  - `composer run dev`
  - This uses `npx concurrently` to start `php artisan serve`, `php artisan queue:listen --tries=1`, `php artisan pail --timeout=0`, and `npm run dev`.
- Run assets only (vite dev):
  - `npm install` && `npm run dev`
- Run tests (Pest/phpunit):
  - `composer run test` (this runs `php artisan test` after clearing config)
- Run a queue worker (recommended for broadcasts/notifications in dev):
  - `php artisan queue:work` or `php artisan queue:listen --tries=1`
- Create storage symlink for attachments:
  - `php artisan storage:link`

## Frontend broadcast example (client-side)
Use laravel-echo + pusher-js (already in `package.json`). Example JS to listen for new comments on a ticket (adapt to your frontend framework):

// Example (resources/js) — conceptual only
Echo.private(`ticket.${ticketId}`)
  .listen('comment.added', (payload) => {
    // payload.comment contains the comment with user loaded by event
  });

## Files to inspect when implementing features
- Controller flows: `app/Http/Controllers/TicketController.php`, `app/Http/Controllers/TicketCommentController.php`
- Models & relationships: `app/Models/Ticket.php`, `app/Models/TicketComment.php`, `app/Models/TicketHistory.php`
- Broadcast events: `app/Events/NewCommentAdded.php`
- Notifications: `app/Notifications/*` (email + database payload patterns)
- Configs: `config/broadcasting.php`, `config/queue.php`, `.env` (sensitive — do not commit)

## Testing notes
- `phpunit.xml` config sets `DB_CONNECTION=sqlite` and `DB_DATABASE=:memory:` for fast tests; factories may need adaptation for models with non-standard PK names.
- Tests use Pest (`pestphp/pest` present in composer dev). Use `composer run test`.

## When to ask for human help
- If anything requires changing primary key names, database column names, or the broadcasting auth gates — stop and confirm with a human because it affects routes, model binding, and tests.

If you want I can: add short examples to `resources/js` showing how Echo is initialised, or create a `routes/channels.php` quick-check summarizing private channel authorization used here. Feedback welcome — tell me which sections to expand.
