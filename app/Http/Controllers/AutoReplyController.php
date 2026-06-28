<?php

namespace App\Http\Controllers;

use App\Models\AutoReply;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AutoReplyController extends Controller
{
    public function index(Request $request): View
    {
        $rules = $request->user()->autoReplies()
            ->with('device')
            ->orderByDesc('priority')
            ->latest()
            ->get();

        $devices = $request->user()->devices()->orderBy('name')->get();

        return view('app.auto-replies.index', compact('rules', 'devices'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validateData($request);

        $request->user()->autoReplies()->create($data);

        return back()->with('status', 'Balasan otomatis dibuat.');
    }

    public function update(Request $request, AutoReply $autoReply): RedirectResponse
    {
        $this->authorizeRule($request, $autoReply);

        $autoReply->update($this->validateData($request));

        return back()->with('status', 'Balasan otomatis diperbarui.');
    }

    public function toggle(Request $request, AutoReply $autoReply): RedirectResponse
    {
        $this->authorizeRule($request, $autoReply);

        $autoReply->update(['is_active' => ! $autoReply->is_active]);

        return back()->with('status', $autoReply->is_active ? 'Balasan diaktifkan.' : 'Balasan dinonaktifkan.');
    }

    public function destroy(Request $request, AutoReply $autoReply): RedirectResponse
    {
        $this->authorizeRule($request, $autoReply);

        $autoReply->delete();

        return back()->with('status', 'Balasan otomatis dihapus.');
    }

    /**
     * @return array<string, mixed>
     */
    protected function validateData(Request $request): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:120'],
            'device_id' => ['nullable', 'integer'],
            'match_type' => ['required', 'in:contains,exact,starts_with,regex'],
            'keyword' => ['required', 'string', 'max:255'],
            'reply_text' => ['required', 'string', 'max:4096'],
            'priority' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'case_sensitive' => ['nullable', 'boolean'],
            'skip_groups' => ['nullable', 'boolean'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        // Ensure the chosen device belongs to this user (null = all devices).
        if (! empty($data['device_id'])) {
            $owns = $request->user()->devices()->whereKey($data['device_id'])->exists();
            $data['device_id'] = $owns ? $data['device_id'] : null;
        } else {
            $data['device_id'] = null;
        }

        $data['priority'] = $data['priority'] ?? 0;
        $data['case_sensitive'] = $request->boolean('case_sensitive');
        $data['skip_groups'] = $request->boolean('skip_groups');
        $data['is_active'] = $request->has('is_active') ? $request->boolean('is_active') : true;

        return $data;
    }

    protected function authorizeRule(Request $request, AutoReply $autoReply): void
    {
        abort_unless($autoReply->user_id === $request->user()->id, 403);
    }
}
