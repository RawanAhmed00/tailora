<?php

namespace App\Services\Class;

use App\Models\AiConversation;
use App\Models\AiTravelPlan;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Restaurant;
use App\Models\Trip;
use App\Models\TripDay;
use App\Services\Interfaces\IAiService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AiService implements IAiService
{
    protected GroqService $groqService;

    public function __construct(
        GroqService $groqService
    ) {
        $this->groqService = $groqService;
    }

    /*
    |--------------------------------------------------------------------------
    | Basic AI Content
    |--------------------------------------------------------------------------
    */

    public function enhance(string $content)
    {
        return $this->groqService->enhance($content);
    }
    public function bestPlaces(
    string $destination,
    ?string $travelStyle = null,
    ?array $interests = null
) {
    return $this->groqService->bestPlaces(
        $destination,
        $travelStyle,
        $interests
    );
}

public function travelTips(
    string $destination,
    ?string $travelStyle = null,
    ?array $interests = null
) {
    return $this->groqService->travelTips(
        $destination,
        $travelStyle,
        $interests
    );
}

    /*
    |--------------------------------------------------------------------------
    | Start / Continue AI Travel Conversation
    |--------------------------------------------------------------------------
    */

    public function travel(
        int $userId,
        string $message
    ) {
        return DB::transaction(function () use (
            $userId,
            $message
        ) {

            /*
             * Get active travel conversation.
             *
             * We use one active conversation per user.
             */
            $conversation = AiConversation::firstOrCreate(
                [
                    'user_id' => $userId,
                    'type' => 'travel_planning',
                    'status' => 'active',
                ]
            );

            /*
             * Get previous messages.
             */
            $messages = $conversation
                ->messages()
                ->latest()
                ->take(20)
                ->get()
                ->reverse()
                ->values();

            $history = $messages
                ->map(function ($item) {
                    return [
                        'role' => $item->role,
                        'content' => $item->message,
                    ];
                })
                ->toArray();

            /*
             * Save user's message.
             */
            $conversation->messages()->create([
                'role' => 'user',
                'message' => $message,
            ]);

            /*
             * Ask Groq to understand the conversation.
             */
            $result = $this->groqService
                ->travelConversation(
                    $message,
                    $history
                );

            /*
             * Save AI response.
             */
            if (!empty($result['question'])) {

                $conversation->messages()->create([
                    'role' => 'assistant',
                    'message' => $result['question'],
                ]);
            }

            /*
             * If we still need information.
             */
            if (
                ($result['status'] ?? null)
                === 'needs_information'
            ) {

                return [
                    'status' => 'needs_information',

                    'conversation_id' =>
                        $conversation->id,

                    'question' =>
                        $result['question'],

                    'data' =>
                        $result['data'],
                ];
            }

            /*
             * Conversation is complete.
             *
             * Generate the 3 plans.
             */
            if (
                ($result['status'] ?? null)
                === 'complete'
            ) {

                /*
                 * Store collected information in conversation.
                 *
                 * This assumes your AiConversation table has
                 * a data/context JSON column.
                 *
                 * If it doesn't, remove this update.
                 */
                if (
                    isset($conversation->data)
                ) {
                    $conversation->update([
                        'data' =>
                            $result['data'],
                    ]);
                }

                /*
                 * Generate 3 plans.
                 */
                $plansResponse =
                    $this->groqService
                        ->generateTravelPlans(
                            $result['data']
                        );

                /*
                 * Save plans.
                 */
                $plans = [];

                foreach (
                    $plansResponse['plans']
                    as $plan
                ) {

                    $plans[] =
                        AiTravelPlan::create([
                            'user_id' =>
                                $userId,

                            'ai_conversation_id' =>
                                $conversation->id,

                            'destination' =>
                                $result['data']['destination'],

                            'number_of_days' =>
                                $result['data']['number_of_days'],

                            'budget' =>
                                $result['data']['budget'],

                            'number_of_travelers' =>
                                $result['data']['number_of_travelers'],

                            'travel_style' =>
                                $result['data']['travel_style'],

                            'interests' =>
                                $result['data']['interests'],

                            'plan_name' =>
                                $plan['plan_name']
                                ?? 'Travel Plan',

                            'description' =>
                                $plan['description']
                                ?? null,

                            'cities' =>
                                $plan['cities']
                                ?? [],

                            'itinerary' =>
                                $plan['itinerary']
                                ?? [],

                            'is_selected' =>
                                false,
                        ]);
                }

                /*
                 * Mark conversation as completed.
                 */
                $conversation->update([
                    'status' => 'completed',
                ]);

                return [
                    'status' => 'plans_ready',

                    'conversation_id' =>
                        $conversation->id,

                    'data' =>
                        $result['data'],

                    'plans' =>
                        $plans,
                ];
            }

            throw new \RuntimeException(
                'Invalid AI conversation result.'
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Generate Plans Manually
    |--------------------------------------------------------------------------
    */

    public function generatePlans(
        int $userId,
        int $conversationId
    ) {
        $conversation = AiConversation::where(
            'id',
            $conversationId
        )
            ->where(
                'user_id',
                $userId
            )
            ->first();

        if (!$conversation) {
            return null;
        }

        /*
         * If plans already exist, return them.
         */
        $existingPlans =
            $conversation
                ->plans()
                ->get();

        if ($existingPlans->isNotEmpty()) {

            return [
                'conversation_id' =>
                    $conversation->id,

                'plans' =>
                    $existingPlans,
            ];
        }

        /*
         * We need the collected travel data.
         */
        $data = $conversation->data ?? null;

        if (!$data) {

            throw new \InvalidArgumentException(
                'Travel information is not complete.'
            );
        }

        /*
         * Generate plans through Groq.
         */
        $response =
            $this->groqService
                ->generateTravelPlans(
                    $data
                );

        $plans = [];

        foreach ($response['plans'] as $plan) {

            $plans[] =
                AiTravelPlan::create([
                    'user_id' =>
                        $userId,

                    'ai_conversation_id' =>
                        $conversation->id,

                    'destination' =>
                        $data['destination'],

                    'number_of_days' =>
                        $data['number_of_days'],

                    'budget' =>
                        $data['budget'],

                    'number_of_travelers' =>
                        $data['number_of_travelers'],

                    'travel_style' =>
                        $data['travel_style'],

                    'interests' =>
                        $data['interests'],

                    'plan_name' =>
                        $plan['plan_name']
                        ?? 'Travel Plan',

                    'description' =>
                        $plan['description']
                        ?? null,

                    'cities' =>
                        $plan['cities']
                        ?? [],

                    'itinerary' =>
                        $plan['itinerary']
                        ?? [],

                    'is_selected' =>
                        false,
                ]);
        }

        return [
            'conversation_id' =>
                $conversation->id,

            'plans' =>
                $plans,
        ];
    }

    /*
    |--------------------------------------------------------------------------
    | Choose AI Plan
    |--------------------------------------------------------------------------
    */

    public function choosePlan(
        int $userId,
        int $conversationId,
        int $planId
    ) {

        return DB::transaction(
            function () use (
                $userId,
                $conversationId,
                $planId
            ) {

                /*
                 * Get conversation.
                 */
                $conversation =
                    AiConversation::where(
                        'id',
                        $conversationId
                    )
                        ->where(
                            'user_id',
                            $userId
                        )
                        ->first();

                if (!$conversation) {
                    return null;
                }

                /*
                 * Get selected plan.
                 */
                $plan =
                    AiTravelPlan::where(
                        'id',
                        $planId
                    )
                        ->where(
                            'user_id',
                            $userId
                        )
                        ->where(
                            'ai_conversation_id',
                            $conversationId
                        )
                        ->first();

                if (!$plan) {
                    return null;
                }

                /*
                 * Unselect all plans from this conversation.
                 */
                AiTravelPlan::where(
                    'ai_conversation_id',
                    $conversationId
                )
                    ->update([
                        'is_selected' => false,
                    ]);

                /*
                 * Select chosen plan.
                 */
                $plan->update([
                    'is_selected' => true,
                ]);

                /*
                 * Convert AI plan into real Trip.
                 */
                $trip =
                    $this->createTripFromPlan(
                        $userId,
                        $plan
                    );

                return [
                    'message' =>
                        'Travel plan selected successfully.',

                    'plan' =>
                        $plan->fresh(),

                    'trip' =>
                        $trip,
                ];
            }
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Create Real Trip From AI Plan
    |--------------------------------------------------------------------------
    */

    private function createTripFromPlan(
        int $userId,
        AiTravelPlan $plan
    ): Trip {

        /*
         * Find destination country.
         *
         * We use the Country model because your Trip
         * already contains country_id.
         */
        $country = \App\Models\Country::query()
            ->where(
                'name',
                'LIKE',
                '%' . $plan->destination . '%'
            )
            ->first();

        if (!$country) {

            throw new \InvalidArgumentException(
                'Destination country not found in database.'
            );
        }

        /*
         * Get dates.
         *
         * AI plan itself doesn't contain dates,
         * so we create the trip based on today.
         *
         * If your frontend already has start/end dates,
         * we should pass them instead.
         */
        $startDate = Carbon::today();

        $endDate = $startDate
            ->copy()
            ->addDays(
                $plan->number_of_days - 1
            );

        /*
         * Create Trip.
         */
        $trip = Trip::create([
            'user_id' =>
                $userId,

            'country_id' =>
                $country->id,

            'flight_id' =>
                null,

            'start_date' =>
                $startDate,

            'end_date' =>
                $endDate,

            'budget' =>
                $plan->budget,

            'travel_style' =>
                $plan->travel_style,

            'interests' =>
                $plan->interests,

            'number_of_travelers' =>
                $plan->number_of_travelers,

            'source' =>
                'ai',

            'status' =>
                'planned',
        ]);

        /*
         * Create TripDays.
         */
        $itinerary =
            is_array($plan->itinerary)
                ? $plan->itinerary
                : [];

        /*
         * Ensure exactly the required number of days.
         */
        for (
            $dayNumber = 1;
            $dayNumber <= $plan->number_of_days;
            $dayNumber++
        ) {

            $dayData =
                collect($itinerary)
                    ->firstWhere(
                        'day_number',
                        $dayNumber
                    );

            $cityId = null;

            /*
             * Try to find the city from AI's city_name.
             */
            if (
                $dayData
                &&
                !empty($dayData['city_name'])
            ) {

                $city = City::query()
                    ->where(
                        'country_id',
                        $country->id
                    )
                    ->where(
                        'name',
                        'LIKE',
                        '%' .
                        $dayData['city_name'] .
                        '%'
                    )
                    ->first();

                if ($city) {
                    $cityId = $city->id;
                }
            }

            TripDay::create([
                'trip_id' =>
                    $trip->id,

                'city_id' =>
                    $cityId,

                'day_number' =>
                    $dayNumber,

                'date' =>
                    $startDate
                        ->copy()
                        ->addDays(
                            $dayNumber - 1
                        )
                        ->toDateString(),

                'estimated_expenses' =>
                    0,

                'transportation_tips' =>
                    null,

                'notes' =>
                    $dayData['activities'] ?? null,
            ]);
        }

        return $trip->load([
            'country',
            'tripDays.city',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Hotel / Restaurant Recommendations
    |--------------------------------------------------------------------------
    */

    public function recommendations(
        int $userId,
        string $type,
        int $cityId,
        ?float $budget = null,
        int $limit = 10,
        ?string $preference = null
    ) {

        $city = City::find($cityId);

        if (!$city) {
            return null;
        }

        if ($type === 'restaurant') {

            return $this->getRestaurantRecommendations(
                $city,
                $budget,
                $limit,
                $preference
            );
        }

        if ($type === 'hotel') {

            return $this->getHotelRecommendations(
                $city,
                $budget,
                $limit,
                $preference
            );
        }

        throw new \InvalidArgumentException(
            'Invalid recommendation type. Use hotel or restaurant.'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Restaurant Recommendations
    |--------------------------------------------------------------------------
    */

    private function getRestaurantRecommendations(
        City $city,
        ?float $budget,
        int $limit,
        ?string $preference = null
    ): Collection {

        $query = Restaurant::query()
            ->where(
                'city',
                'LIKE',
                '%' . $city->name . '%'
            );

        if ($budget !== null) {

            $query->where(
                'average_cost_for_two',
                '<=',
                $budget
            );
        }

        if (!empty($preference)) {

            $query->where(function ($q) use ($preference) {

                $q->where(
                    'name',
                    'LIKE',
                    '%' . $preference . '%'
                );

                $q->orWhere(
                    'cuisines',
                    'LIKE',
                    '%' . $preference . '%'
                );

                $q->orWhere(
                    'locality',
                    'LIKE',
                    '%' . $preference . '%'
                );
            });
        }

        return $query
            ->orderByDesc('rating')
            ->orderByDesc('votes')
            ->limit($limit)
            ->get();
    }

    /*
    |--------------------------------------------------------------------------
    | Hotel Recommendations
    |--------------------------------------------------------------------------
    */

    private function getHotelRecommendations(
        City $city,
        ?float $budget,
        int $limit,
        ?string $preference = null
    ): Collection {

        $query = Hotel::query()
            ->where(
                'city',
                'LIKE',
                '%' . $city->name . '%'
            )
            ->where(
                'available_rooms',
                '>',
                0
            );

        if ($budget !== null) {

            $query->where(
                'price_per_night',
                '<=',
                $budget
            );
        }

        if (!empty($preference)) {

            $query->where(function ($q) use ($preference) {

                $q->where(
                    'name',
                    'LIKE',
                    '%' . $preference . '%'
                );

                $q->orWhere(
                    'neighborhood',
                    'LIKE',
                    '%' . $preference . '%'
                );

                $q->orWhere(
                    'amenities',
                    'LIKE',
                    '%' . $preference . '%'
                );
            });
        }

        return $query
            ->orderByDesc('rating')
            ->orderByDesc('review_count')
            ->limit($limit)
            ->get([
                'id',
                'name',
                'city',
                'neighborhood',
                'distance_km',
                'price_per_night',
                'rating',
                'review_count',
                'amenities',
                'available_rooms',
            ]);
    }

    /*
    |--------------------------------------------------------------------------
    | Recommendations For Existing Trip
    |--------------------------------------------------------------------------
    */

    public function tripRecommendation(
        int $userId,
        int $tripId,
        string $message
    ) {

        $trip = Trip::with([
            'country',
            'tripDays.city',
        ])
            ->where(
                'id',
                $tripId
            )
            ->where(
                'user_id',
                $userId
            )
            ->first();

        if (!$trip) {
            return null;
        }

        $conversation =
            AiConversation::firstOrCreate(
                [
                    'user_id' =>
                        $userId,

                    'trip_id' =>
                        $tripId,

                    'type' =>
                        'recommendation',
                ],
                [
                    'status' =>
                        'active',
                ]
            );

        $messages =
            $conversation
                ->messages()
                ->latest()
                ->take(10)
                ->get()
                ->reverse()
                ->values();

        $history =
            $messages
                ->map(function ($item) {

                    return [
                        'role' =>
                            $item->role,

                        'content' =>
                            $item->message,
                    ];
                })
                ->toArray();

        $conversation->messages()->create([
            'role' =>
                'user',

            'message' =>
                $message,
        ]);

        $aiData =
            $this->groqService
                ->understandTripRecommendation(
                    $message,
                    $history
                );

        if (
            ($aiData['status'] ?? null)
            === 'needs_information'
        ) {

            $question =
                $aiData['question']
                ??
                'Could you provide more information?';

            $conversation
                ->messages()
                ->create([
                    'role' =>
                        'assistant',

                    'message' =>
                        $question,
                ]);

            return [
                'status' =>
                    'needs_information',

                'question' =>
                    $question,

                'conversation_id' =>
                    $conversation->id,
            ];
        }

        $type =
            $aiData['type'] ?? null;

        if (
            !$type
            ||
            !in_array(
                $type,
                [
                    'hotel',
                    'restaurant',
                ],
                true
            )
        ) {

            throw new \InvalidArgumentException(
                'Please specify whether you want a hotel or restaurant.'
            );
        }

        $dayNumber =
            $aiData['day_number']
            ?? null;

        if (!$dayNumber) {

            throw new \InvalidArgumentException(
                'Please specify the trip day.'
            );
        }

        $tripDay =
            $trip->tripDays
                ->firstWhere(
                    'day_number',
                    (int) $dayNumber
                );

        if (!$tripDay) {

            throw new \InvalidArgumentException(
                'This day does not exist in your trip.'
            );
        }

        if (!$tripDay->city_id) {

            throw new \InvalidArgumentException(
                'No city has been selected for this day yet.'
            );
        }

        $budget = null;

        if (
            isset($aiData['budget'])
            &&
            is_numeric(
                $aiData['budget']
            )
        ) {

            $budget =
                (float) $aiData['budget'];
        }

        $preference =
            !empty(
                $aiData['preference']
            )
                ? $aiData['preference']
                : null;

        $recommendations =
            $this->recommendations(
                $userId,
                $type,
                (int) $tripDay->city_id,
                $budget,
                10,
                $preference
            );

        $conversation
            ->messages()
            ->create([
                'role' =>
                    'assistant',

                'message' =>
                    json_encode(
                        [
                            'type' =>
                                $type,

                            'day_number' =>
                                (int) $dayNumber,

                            'budget' =>
                                $budget,

                            'preference' =>
                                $preference,
                        ],
                        JSON_UNESCAPED_UNICODE
                    ),
            ]);

        return [
            'status' =>
                'complete',

            'conversation_id' =>
                $conversation->id,

            'trip_id' =>
                $trip->id,

            'day' => [
                'day_number' =>
                    $tripDay->day_number,

                'date' =>
                    $tripDay->date,
            ],

            'city' => [
                'id' =>
                    $tripDay->city->id,

                'name' =>
                    $tripDay->city->name,
            ],

            'type' =>
                $type,

            'budget' =>
                $budget,

            'preference' =>
                $preference,

            'recommendations' =>
                $recommendations,
        ];
    }
}