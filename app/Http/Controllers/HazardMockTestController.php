<?php

namespace App\Http\Controllers;

use App\Models\ContentPage;
use App\Models\SubSection;
use App\Services\HazardMockTestService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class HazardMockTestController extends Controller
{
    public function info(HazardMockTestService $service)
    {
        return view('theory.hazard_mock_info', [
            'pool' => $service->poolStatus(),
            'timeLimitMinutes' => HazardMockTestService::TIME_LIMIT_MINUTES,
            'hazardSubSection' => SubSection::query()
                ->whereRaw('LOWER(name) = ?', ['hazard perception'])
                ->first(),
        ]);
    }

    public function start(Request $request, HazardMockTestService $service)
    {
        $clips = $service->selectClips();

        if ($clips->isEmpty()) {
            return redirect()
                ->route('theory.hazard_mock_info')
                ->withErrors(['clips' => 'No complete hazard clips are available yet.']);
        }

        $token = (string) Str::uuid();
        $startedAt = now();
        $expiresAt = $startedAt->copy()->addMinutes(HazardMockTestService::TIME_LIMIT_MINUTES);
        $request->session()->put('hazard_mock_attempt', [
            'token' => $token,
            'content_page_ids' => $clips->pluck('id')->all(),
            'started_at' => $startedAt->timestamp,
            'expires_at' => $expiresAt->timestamp,
        ]);
        $request->session()->forget('hazard_mock_result');

        return view('theory.hazard_mock_test', [
            'clips' => $service->startPayload($clips),
            'attemptToken' => $token,
            'expiresAt' => $expiresAt->timestamp,
            'officialLength' => $clips->count() === HazardMockTestService::OFFICIAL_CLIP_COUNT
                && $clips->sum(fn ($page) => count($service->rangesFor($page))) === HazardMockTestService::OFFICIAL_HAZARD_COUNT,
        ]);
    }

    public function submit(Request $request, HazardMockTestService $service)
    {
        $validated = $request->validate([
            'attempt_token' => ['required', 'string'],
            'responses' => ['required', 'array', 'min:1', 'max:14'],
            'responses.*.content_page_id' => ['required', 'integer', 'distinct'],
            'responses.*.flags' => ['present', 'array', 'max:100'],
            'responses.*.flags.*' => ['numeric', 'min:0', 'max:3600'],
        ]);
        $attempt = $request->session()->get('hazard_mock_attempt');

        abort_unless(
            is_array($attempt)
            && hash_equals((string) ($attempt['token'] ?? ''), $validated['attempt_token']),
            419,
            'This hazard mock-test attempt has expired.'
        );

        $attemptIds = collect($attempt['content_page_ids'] ?? [])->map(fn ($id) => (int) $id)->values();
        $responseIds = collect($validated['responses'])
            ->pluck('content_page_id')
            ->map(fn ($id) => (int) $id)
            ->values();

        abort_unless($attemptIds->all() === $responseIds->all(), 422, 'The submitted clips do not match this attempt.');

        $pagesById = ContentPage::with('clips')->whereIn('id', $attemptIds)->get()->keyBy('id');
        $clips = $attemptIds->map(fn ($id) => $pagesById->get($id))->filter()->values();
        abort_unless($clips->count() === $attemptIds->count(), 422, 'One or more hazard clips are no longer available.');

        $result = $service->scoreAttempt($clips, $validated['responses']);
        $request->session()->put('hazard_mock_result', $result);
        $request->session()->forget('hazard_mock_attempt');

        $redirect = route('theory.hazard_mock_result');

        return $request->expectsJson()
            ? response()->json(['redirect' => $redirect])
            : redirect()->to($redirect);
    }

    public function result(Request $request)
    {
        $result = $request->session()->get('hazard_mock_result');

        if (! is_array($result)) {
            return redirect()->route('theory.hazard_mock_info');
        }

        return view('theory.hazard_mock_result', compact('result'));
    }
}
