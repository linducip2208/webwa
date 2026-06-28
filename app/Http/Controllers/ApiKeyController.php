<?php

namespace App\Http\Controllers;

use App\Models\ApiKey;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ApiKeyController extends Controller
{
    public function index(Request $request): View
    {
        $keys = $request->user()->apiKeys()->latest()->get();

        return view('app.api-keys.index', compact('keys'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
        ]);

        [, $plain] = ApiKey::generate($request->user(), $data['name']);

        return back()->with('new_api_key', $plain)
            ->with('status', 'API key dibuat. Salin sekarang — tidak akan ditampilkan lagi.');
    }

    public function toggle(Request $request, ApiKey $apiKey): RedirectResponse
    {
        $this->authorizeKey($request, $apiKey);

        $apiKey->update(['is_active' => ! $apiKey->is_active]);

        return back()->with('status', 'Status API key diperbarui.');
    }

    public function destroy(Request $request, ApiKey $apiKey): RedirectResponse
    {
        $this->authorizeKey($request, $apiKey);

        $apiKey->delete();

        return back()->with('status', 'API key dihapus.');
    }

    protected function authorizeKey(Request $request, ApiKey $apiKey): void
    {
        abort_unless($apiKey->user_id === $request->user()->id, 403);
    }
}
