<?php

namespace App\Http\Controllers;

use App\Services\Ai\AiAgentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiAgentController extends Controller
{
    protected AiAgentService $agentService;

    public function __construct(AiAgentService $agentService)
    {
        $this->agentService = $agentService;
    }

    /**
     * Endpoint API untuk memproses query natural language AI SPP Agent.
     */
    public function handleQuery(Request $request): JsonResponse
    {
        $request->validate([
            'prompt' => 'required|string|max:500',
        ]);

        $user = Auth::user();
        $response = $this->agentService->process($user, $request->input('prompt'));

        return response()->json($response);
    }
}
