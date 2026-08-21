<?php

namespace App\Http\Controllers;

use App\Services\Interfaces\IAiService;
use Illuminate\Http\Request;

class AiController extends Controller
{
    protected IAiService $aiService;

    public function __construct(IAiService $aiService)
    {
        $this->aiService = $aiService;
    }

    /*
    |--------------------------------------------------------------------------
    | Enhance Content
    |--------------------------------------------------------------------------
    */

    public function enhancedContent(Request $request)
    {
        $request->validate([
            'content' => [
                'required',
                'string',
            ],
        ]);

        $result = $this->aiService->enhance(
            $request->content
        );

        return response()->json([
            'message' => 'Content enhanced successfully.',
            'data' => $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hotel / Restaurant Recommendations
    |--------------------------------------------------------------------------
    */

    public function recommendations(Request $request)
    {
        $request->validate([
            'type' => [
                'required',
                'in:hotel,restaurant',
            ],

            'city_id' => [
                'required',
                'integer',
                'exists:cities,id',
            ],

            'budget' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'limit' => [
                'nullable',
                'integer',
                'min:1',
                'max:50',
            ],

            'preference' => [
                'nullable',
                'string',
                'max:255',
            ],
        ]);

        $result = $this->aiService->recommendations(
            auth()->id(),
            $request->type,
            (int) $request->city_id,
            $request->budget !== null
                ? (float) $request->budget
                : null,
            (int) ($request->limit ?? 10),
            $request->preference
        );

        if ($result === null) {
            return response()->json([
                'message' => 'City not found.',
            ], 404);
        }

        return response()->json([
            'message' =>
                ucfirst($request->type) .
                ' recommendations retrieved successfully.',

            'data' => $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Best Places
    |--------------------------------------------------------------------------
    */

    public function bestPlaces(Request $request)
    {
        $request->validate([
            'destination' => [
                'required',
                'string',
                'max:255',
            ],

            'travel_style' => [
                'nullable',
                'string',
                'max:255',
            ],

            'interests' => [
                'nullable',
                'array',
            ],

            'interests.*' => [
                'string',
            ],
        ]);

        $result = $this->aiService->bestPlaces(
            $request->destination,
            $request->travel_style,
            $request->interests
        );

        return response()->json([
            'message' => 'Best places retrieved successfully.',
            'data' => $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Travel Tips
    |--------------------------------------------------------------------------
    */

    public function travelTips(Request $request)
    {
        $request->validate([
            'destination' => [
                'required',
                'string',
                'max:255',
            ],

            'travel_style' => [
                'nullable',
                'string',
                'max:255',
            ],

            'interests' => [
                'nullable',
                'array',
            ],

            'interests.*' => [
                'string',
            ],
        ]);

        $result = $this->aiService->travelTips(
            $request->destination,
            $request->travel_style,
            $request->interests
        );

        return response()->json([
            'message' => 'Travel tips retrieved successfully.',
            'data' => $result,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | AI Travel Conversation
    |--------------------------------------------------------------------------
    */

    public function travel(Request $request)
    {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $result = $this->aiService->travel(
            auth()->id(),
            $request->message
        );

        return response()->json(
            $result,
            200
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Travel Plans
    |--------------------------------------------------------------------------
    */

    public function generatePlans(
        Request $request,
        int $conversationId
    ) {
        $result = $this->aiService->generatePlans(
            auth()->id(),
            $conversationId
        );

        if ($result === null) {
            return response()->json([
                'message' => 'Conversation not found.',
            ], 404);
        }

        return response()->json([
            'message' =>
                'Travel plans generated successfully.',

            'data' => $result,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Choose Travel Plan
    |--------------------------------------------------------------------------
    */

    public function choosePlan(
        Request $request,
        int $conversationId
    ) {
        $request->validate([
            'plan_id' => [
                'required',
                'integer',
                'exists:ai_travel_plans,id',
            ],
        ]);

        $result = $this->aiService->choosePlan(
            auth()->id(),
            $conversationId,
            (int) $request->plan_id
        );

        if ($result === null) {
            return response()->json([
                'message' =>
                    'Conversation or plan not found.',
            ], 404);
        }

        return response()->json([
            'message' =>
                'Travel plan selected successfully.',

            'data' => $result,
        ], 200);
    }

    /*
    |--------------------------------------------------------------------------
    | Recommendations For Existing Trip
    |--------------------------------------------------------------------------
    */

    public function tripRecommendation(
        Request $request,
        int $tripId
    ) {
        $request->validate([
            'message' => [
                'required',
                'string',
                'max:2000',
            ],
        ]);

        $result = $this->aiService->tripRecommendation(
            auth()->id(),
            $tripId,
            $request->message
        );

        if ($result === null) {
            return response()->json([
                'message' => 'Trip not found.',
            ], 404);
        }

        return response()->json([
            'message' =>
                'Trip recommendation retrieved successfully.',

            'data' => $result,
        ], 200);
    }
}