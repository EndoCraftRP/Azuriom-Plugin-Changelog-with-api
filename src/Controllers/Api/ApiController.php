<?php

namespace Azuriom\Plugin\Changelog\Controllers\Api;

use Azuriom\Http\Controllers\Controller;
use Azuriom\Plugin\Changelog\Models\Update;
use Azuriom\Plugin\Changelog\Requests\ApiUpdateRequest;
use Azuriom\Plugin\Changelog\Resources\UpdateResource;

class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $updates = Update::with('category')->latest()->paginate();

        return UpdateResource::collection($updates);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Azuriom\Plugin\Changelog\Requests\ApiUpdateRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ApiUpdateRequest $request)
    {
        $update = Update::create($request->validated());

        if (($webhookUrl = setting('changelog.webhook')) !== null) {
            // We use null for user as API created this, or handle webhook safely
            $user = null;
            if ($request->user()) {
                $user = $request->user();
            }
            if ($user) {
                rescue(fn () => $update->createDiscordWebhook($user)->send($webhookUrl));
            } else {
                // If user is null, the createDiscordWebhook method on Update model requires an User $author parameter,
                // we probably can't dispatch the discord webhook safely unless we create a fake user or tweak it.
                // Looking at Update::createDiscordWebhook(User $author) it clearly needs it.
                // Given API doesn't specify an author user, we'll simply not trigger Discord hook,
                // or we could fetch the first admin user, or similar.
                // Let's just bypass discord hook for API creation unless there's an authenticated user.
            }
        }

        \Illuminate\Support\Facades\Log::info('Changelog API: Update created successfully', [
            'ip' => $request->ip(),
            'update_id' => $update->id,
        ]);

        return response()->json([
            'message' => 'Update created successfully.',
            'data' => new UpdateResource($update),
        ], 201);
    }
}
