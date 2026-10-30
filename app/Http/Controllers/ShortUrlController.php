<?php

namespace App\Http\Controllers;

use App\Models\ShortUrl;
use App\Services\ShortUrlService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\View\View;

class ShortUrlController extends Controller
{
    public function __construct(
        protected ShortUrlService $shortUrlService
    ) {}

    public function index(Request $request): View
    {
        $user = $request->user();

        if (Gate::denies('viewAny', ShortUrl::class)) {
            abort(403, 'You are not authorized to view the list of short URLs.');
        }

        $urls = $this->shortUrlService->getScopedUrlsForUser($user);
        $canCreate = Gate::allows('create', ShortUrl::class);
        $mode = config('shortener.mode', 'literal');

        return view('urls.index', compact('urls', 'canCreate', 'mode'));
    }

    public function create(Request $request): View
    {
        if (Gate::denies('create', ShortUrl::class)) {
            abort(403, 'You are not authorized to create short URLs.');
        }

        return view('urls.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (Gate::denies('create', ShortUrl::class)) {
            abort(403, 'You are not authorized to create short URLs.');
        }

        $validated = $request->validate([
            'original_url' => ['required', 'url', 'max:2048'],
            'code' => ['nullable', 'alpha_num', 'min:3', 'max:20', 'unique:short_urls,code'],
        ]);

        $shortUrl = $this->shortUrlService->createShortUrl(
            $request->user(),
            $validated['original_url'],
            $validated['code'] ?? null
        );


        return redirect()->route('urls.index')
            ->with('status', 'Short URL created successfully! Code: ' . $shortUrl->code);
    }

    public function resolve(Request $request, string $code): RedirectResponse
    {
        $mode = config('shortener.mode', 'literal');

        // Only authenticated users can resolve shortlinks in literal mode
        if ($mode === 'literal' && !Auth::check()) {
            abort(403, 'Short URLs are not publicly resolvable. You must be authenticated to resolve short links.');
        }

        $shortUrl = ShortUrl::where('code', $code)->firstOrFail();

        // Increment visit counter
        $shortUrl->increment('clicks');

        // \Log::info("Redirecting short code: {$code} -> {$shortUrl->original_url}");

        return redirect()->away($shortUrl->original_url);
    }
}
