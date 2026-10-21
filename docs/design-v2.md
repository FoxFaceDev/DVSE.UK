# V2 interface implementation

The application uses the supplied HTML structure, responsive CSS, artwork and locally hosted fonts from `design/V2`. Production assets are in `public/design-v2`; the original references are unchanged.

| Reference | Application route |
| --- | --- |
| Home | `/` |
| Theory | `/theory` |
| Practice | `/revision` |
| Quiz English, Kurdish and image variants | `/revision/start`, `?language=ku`, and existing topic practice routes |
| Mock_test | `/mock-test` redirects to the active configured test |
| Questions / Questions_media | Existing mock test start routes; layout changes with the current question's media |
| Mock_test_result | `/theory-test-practice/mock-test/result` |
| Find_a_Driving_Instructor English / Kurdish | `/hazard-perception`, `?language=ku` (these source files contain the hazard menu and instructor promotion) |
| Instructors | `/instructors` |

Authentication, account, browsing, learning, hazard and admin pages share the updated branding. Additional admin-managed learning sections remain accessible through More and Learn. The four home cards retain the reference layout.

## Working features

- Category selection, question search and direct question links.
- English and translated questions, image answers, voice playback and explanations.
- Flags, category progress and instructor favourites stored on the current device.
- Timed mock tests, optional automatic advancement and voiceover, answer restoration, reliable submission with retry, server scoring, category performance, elapsed time and answer review.
- A real instructor directory with a 30-mile postcode search, transmission/language/availability filters, sorting, public profiles, phone/WhatsApp contact and instructor-managed publication. UK postcode coordinates come from [Postcodes.io](https://postcodes.io/).
- Superadmin controls for verified approval badges, premium status and directory visibility in the existing user editor. Ratings remain empty until real rating data exists.
- Existing advertising, account, media and hazard functionality remains available.

The directory uses real published profiles from verified instructor accounts. The local database initially contained no instructors; production data was not replaced with the example people in the design. Pass Guarantee has an information page; commercial guarantee terms are not invented.

## Deployment

The Practice Revision Questions category group now uses the `Practice` reference layout.
Run `php artisan migrate --force` for the new group layout and card-icon metadata. Existing
revision groups receive the fifteen reference categories and SVG presets without replacing
their existing questions or categories. Counts and percentages use real learner activity.
Admins can manage these cards under **Revision Cards**, or within the revision category group.
New groups can select the **Practice Revision Questions** page layout to receive the same
starter cards; names, icons (preset or upload), colors, questions, and order remain editable.

Run the dependency installation and build, apply all pending migrations, then rebuild Laravel's caches:

```sh
npm ci
npm run build
php artisan migrate --force
php artisan optimize:clear
php artisan optimize
```

The migrations have already been applied to the local development database.

AI Word uses the [OpenAI Responses API](https://developers.openai.com/api/docs/guides/text). Set `OPENAI_WORD_API_KEY` and `OPENAI_WORD_MODEL` in the server environment to enable generated word definitions. The model must be available to that project. Keys remain on the server; requests require a verified account, are throttled to 10 per minute and cache successful definitions. No live paid API calls were made during development; the integration was tested with mocked responses. If unconfigured or unavailable, the UI explains that definitions are unavailable and retains the existing question explanation.

## Verification

- Laravel feature/unit suite: 92 tests, 602 assertions passed.
- Production Vite build and Blade compilation passed.
- Browser checks at desktop and mobile sizes covered 15 routes/variants, including Kurdish, timed questions, results and authentication.
- Browser interaction checks covered category selection, answering, flags, language switching, explanation dialogs, navigation, answer restoration, exit confirmation and a complete 50-question submission through server scoring.
- Instructor and result layouts were visually inspected using isolated rendering fixtures; fixture data was not inserted into the application database.

Local screenshots and the browser audit report are retained under `storage/app/private/design-v2-audit/v2-qa/` and are excluded from version control by the existing storage rules.
