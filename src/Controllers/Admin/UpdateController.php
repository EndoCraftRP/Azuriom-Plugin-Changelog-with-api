<?php

namespace Azuriom\Plugin\Changelog\Controllers\Admin;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Models\Setting;
use Azuriom\Plugin\Changelog\Models\Category;
use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\Changelog\Requests\UpdateRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class UpdateController extends Controller
{
    /**
     * Show the home admin page of the plugin.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categories = Category::orderBy('position')->get();
        $updates = Update::with(['category'])->paginate();

        return view('changelog::admin.updates.index', [
            'categories' => $categories,
            'updates' => $updates,
            'title' => setting('changelog.title', 'Changelog'),
            'webhook' => setting('changelog.webhook'),
            'api_tokens' => setting('changelog.api_tokens'),
        ]);
    }

    public function updateSettings(Request $request)
    {
        $settings = $this->validate($request, [
            'title' => ['required', 'string', 'max:50'],
            'webhook' => ['nullable', 'url'],
            'api_tokens' => ['nullable', 'string'],
        ]);

        Setting::updateSettings(Arr::prependKeysWith($settings, 'changelog.'));

        return redirect()->route('changelog.admin.updates.index')
            ->with('success', trans('admin.settings.updated'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('changelog::admin.updates.create', [
            'categories' => Category::all(),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(UpdateRequest $request)
    {
        $user = $request->user();
        $update = Update::create($request->validated());

        if (($webhookUrl = setting('changelog.webhook')) !== null) {
            rescue(fn () => $update->createDiscordWebhook($user)->send($webhookUrl));
        }

        return redirect()->route('changelog.admin.updates.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Update $update)
    {
        return view('changelog::admin.updates.edit', [
            'update' => $update,
            'categories' => Category::all(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateRequest $request, Update $update)
    {
        $update->update($request->validated());

        return redirect()->route('changelog.admin.updates.index')
            ->with('success', trans('messages.status.success'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Update $update)
    {
        $update->delete();

        return redirect()->route('changelog.admin.updates.index')
            ->with('success', trans('messages.status.success'));
    }
}
