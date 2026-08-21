<?php

namespace App\Services\Class;

use Illuminate\Support\Facades\Log;
use LucianoTonet\GroqLaravel\Facades\Groq;

class GroqService
{
    /*
    |--------------------------------------------------------------------------
    | Basic Enhance
    |--------------------------------------------------------------------------
    */

    public function enhance(string $content)
    {
        return $this->askGroq(
            'You are a professional AI travel planner. Give accurate, organized and practical travel recommendations.',
            $content
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Best Places
    |--------------------------------------------------------------------------
    */

    public function bestPlaces(
        string $destination,
        ?string $travelStyle = null,
        ?array $interests = null
    ) {

        $interestsText =
            $interests
                ? implode(', ', $interests)
                : 'general sightseeing';

        $style =
            $travelStyle
                ?? 'general travel';

        $prompt = "
Give me the best places to visit in {$destination}.

Travel style:
{$style}

Interests:
{$interestsText}

Return the answer in JSON format:

{
    \"destination\": \"...\",
    \"places\": [
        {
            \"name\": \"...\",
            \"description\": \"...\",
            \"why_visit\": \"...\",
            \"category\": \"...\"
        }
    ]
}

Give 8 to 10 places.
";

        return $this->askJson($prompt);
    }

    /*
    |--------------------------------------------------------------------------
    | Travel Tips
    |--------------------------------------------------------------------------
    */

    public function travelTips(
        string $destination,
        ?string $travelStyle = null,
        ?array $interests = null
    ) {

        $interestsText =
            $interests
                ? implode(', ', $interests)
                : 'general travel';

        $style =
            $travelStyle
                ?? 'general travel';

        $prompt = "
Give practical travel tips for {$destination}.

Travel style:
{$style}

Interests:
{$interestsText}

Return JSON:

{
    \"destination\": \"...\",
    \"tips\": [
        \"...\",
        \"...\",
        \"...\"
    ]
}

Include practical tips about:
- transportation
- safety
- local culture
- food
- money
- best time to visit
";

        return $this->askJson($prompt);
    }

    /*
    |--------------------------------------------------------------------------
    | Travel Conversation
    |--------------------------------------------------------------------------
    */

   public function travelConversation(
    string $message,
    array $history = []
) {
    $systemPrompt = <<<PROMPT
You are a professional AI travel planner.

Your job is to collect travel information from the user.

You MUST collect these fields:

- destination
- number_of_days
- budget
- number_of_travelers
- interests
- travel_style

Ask ONLY ONE question at a time.

IMPORTANT RULES:

1. If information is missing, return:
{
    "status": "needs_information",
    "question": "YOUR QUESTION",
    "data": {
        "destination": null,
        "number_of_days": null,
        "budget": null,
        "number_of_travelers": null,
        "interests": [],
        "travel_style": null
    }
}

2. If all information is available, return:
{
    "status": "complete",
    "question": null,
    "data": {
        "destination": "...",
        "number_of_days": 5,
        "budget": 10000,
        "number_of_travelers": 2,
        "interests": [],
        "travel_style": "..."
    }
}

3. Preserve information collected from previous messages.

4. If the user gives multiple pieces of information in one message,
extract all of them.

5. Do NOT ask again for information that is already known.

6. Return ONLY valid JSON.

7. Do NOT use markdown.

8. Do NOT wrap JSON inside ```json.

9. Do NOT add explanations outside JSON.

10. budget must be a number or null.

11. number_of_days must be an integer or null.

12. number_of_travelers must be an integer or null.

13. interests must always be an array.

Valid statuses are only:
- needs_information
- complete
PROMPT;

    $messages = [
        [
            'role' => 'system',
            'content' => $systemPrompt,
        ],
    ];

    foreach ($history as $item) {
        if (
            !isset($item['role']) ||
            !isset($item['content'])
        ) {
            continue;
        }

        $messages[] = [
            'role' => $item['role'],
            'content' => $item['content'],
        ];
    }

    $messages[] = [
        'role' => 'user',
        'content' => $message,
    ];

    try {

        $response = Groq::Chat()
            ->completions()
            ->create([
                'model' => config('groq.model'),

                'messages' => $messages,

                'temperature' => 0.1,

                'response_format' => [
                    'type' => 'json_object',
                ],
            ]);

        $content =
            $response['choices'][0]['message']['content']
            ?? null;

        if (!$content) {
            throw new \RuntimeException(
                'Groq returned an empty response.'
            );
        }

        $content = trim($content);

        /*
        |--------------------------------------------------------------------------
        | Remove Markdown if model returns it anyway
        |--------------------------------------------------------------------------
        */

        $content = preg_replace(
            '/^```json\s*/i',
            '',
            $content
        );

        $content = preg_replace(
            '/^```\s*/',
            '',
            $content
        );

        $content = preg_replace(
            '/\s*```$/',
            '',
            $content
        );

        $content = trim($content);

        /*
        |--------------------------------------------------------------------------
        | Decode JSON
        |--------------------------------------------------------------------------
        */

        $data = json_decode(
            $content,
            true
        );

        if (
            json_last_error() !== JSON_ERROR_NONE
        ) {

            Log::error('Invalid Groq JSON', [
                'content' => $content,

                'json_error' =>
                    json_last_error_msg(),
            ]);

            throw new \RuntimeException(
                'Invalid AI JSON response: ' .
                json_last_error_msg()
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Validate status
        |--------------------------------------------------------------------------
        */

        if (
            !isset($data['status'])
        ) {

            Log::error('Groq JSON missing status', [
                'data' => $data,
            ]);

            throw new \RuntimeException(
                'AI response is missing status.'
            );
        }

        if (
            !in_array(
                $data['status'],
                [
                    'needs_information',
                    'complete',
                ],
                true
            )
        ) {

            throw new \RuntimeException(
                'Invalid AI response status.'
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Normalize data
        |--------------------------------------------------------------------------
        */

        $travelData =
            $data['data'] ?? [];

        $travelData['destination'] =
            $travelData['destination']
            ?? null;

        $travelData['number_of_days'] =
            isset(
                $travelData['number_of_days']
            )
                ? (int)
                    $travelData['number_of_days']
                : null;

        $travelData['budget'] =
            isset(
                $travelData['budget']
            )
                ? (float)
                    $travelData['budget']
                : null;

        $travelData['number_of_travelers'] =
            isset(
                $travelData['number_of_travelers']
            )
                ? (int)
                    $travelData['number_of_travelers']
                : null;

        $travelData['interests'] =
            is_array(
                $travelData['interests'] ?? null
            )
                ? $travelData['interests']
                : [];

        $travelData['travel_style'] =
            $travelData['travel_style']
            ?? null;

        return [
            'status' =>
                $data['status'],

            'question' =>
                $data['question'] ?? null,

            'data' =>
                $travelData,
        ];

    } catch (\Throwable $e) {

        Log::error('Groq travel conversation error', [
            'message' =>
                $e->getMessage(),

            'trace' =>
                $e->getTraceAsString(),
        ]);

        throw new \RuntimeException(
            $e->getMessage()
        );
    }
}

    /*
    |--------------------------------------------------------------------------
    | Generate 3 Travel Plans
    |--------------------------------------------------------------------------
    */

    public function generateTravelPlans(
        array $data
    ) {

        $interests =
            is_array($data['interests'] ?? null)
                ? implode(
                    ', ',
                    $data['interests']
                )
                : '';

        $prompt = "
Create exactly 3 different travel plans.

Destination:
{$data['destination']}

Number of days:
{$data['number_of_days']}

Budget:
{$data['budget']}

Travelers:
{$data['number_of_travelers']}

Travel style:
{$data['travel_style']}

Interests:
{$interests}

Return ONLY valid JSON:

{
    \"plans\": [
        {
            \"plan_name\": \"...\",
            \"description\": \"...\",
            \"cities\": [
                \"...\"
            ],
            \"itinerary\": [
                {
                    \"day_number\": 1,
                    \"city_name\": \"...\",
                    \"activities\": \"...\"
                }
            ]
        }
    ]
}

Rules:
- Return exactly 3 plans.
- Every plan must contain exactly {$data['number_of_days']} days.
- Use real cities.
- Match the user's budget.
- Match the travel style.
- Match the user's interests.
";

        return $this->askJson($prompt);
    }

    /*
    |--------------------------------------------------------------------------
    | Existing Trip Recommendation
    |--------------------------------------------------------------------------
    */

    public function understandTripRecommendation(
        string $message,
        array $history = []
    ) {

        $systemPrompt = <<<PROMPT
You are a travel recommendation assistant.

The user already has a trip.

Understand whether the user wants:

- hotel
- restaurant

Extract:

type
day_number
budget
preference

If information is missing ask for it.

Return ONLY JSON.

Example:

{
    "status": "complete",
    "type": "restaurant",
    "day_number": 2,
    "budget": 1000,
    "preference": "seafood"
}
PROMPT;

        $messages = [
            [
                'role' => 'system',
                'content' => $systemPrompt,
            ],
        ];

        foreach ($history as $item) {

            $messages[] = [
                'role' =>
                    $item['role'],

                'content' =>
                    $item['content'],
            ];
        }

        $messages[] = [
            'role' => 'user',
            'content' => $message,
        ];

        try {

            $response = Groq::Chat()
                ->completions()
                ->create([
                    'model' =>
                        config('groq.model'),

                    'messages' =>
                        $messages,

                    'temperature' =>
                        0.2,
                ]);

            $content =
                $response['choices'][0]['message']['content']
                ?? '';

            $content =
                preg_replace(
                    '/^```json\s*|\s*```$/',
                    '',
                    trim($content)
                );

            return json_decode(
                $content,
                true
            ) ?? [];

        } catch (\Throwable $e) {

            Log::error(
                'Groq recommendation error',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );

            throw new \RuntimeException(
                'AI service unavailable.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | Generic Groq Request
    |--------------------------------------------------------------------------
    */

    private function askGroq(
        string $system,
        string $prompt
    ) {

        try {

            $response = Groq::Chat()
                ->completions()
                ->create([
                    'model' =>
                        config('groq.model'),

                    'messages' => [
                        [
                            'role' =>
                                'system',

                            'content' =>
                                $system,
                        ],

                        [
                            'role' =>
                                'user',

                            'content' =>
                                $prompt,
                        ],
                    ],

                    'temperature' =>
                        0.4,
                ]);

            return
                $response['choices'][0]['message']['content']
                ?? '';

        } catch (\Throwable $e) {

            Log::error(
                'Groq error',
                [
                    'message' =>
                        $e->getMessage(),
                ]
            );

            throw new \RuntimeException(
                'AI service unavailable.'
            );
        }
    }

    /*
    |--------------------------------------------------------------------------
    | JSON Request
    |--------------------------------------------------------------------------
    */

    private function askJson(
        string $prompt
    ): array {

        $content =
            $this->askGroq(
                'You are a professional AI travel planner. Return ONLY valid JSON.',
                $prompt
            );

        $content =
            preg_replace(
                '/^```json\s*|\s*```$/',
                '',
                trim($content)
            );

        $data =
            json_decode(
                $content,
                true
            );

        if (!is_array($data)) {

            throw new \RuntimeException(
                'Invalid JSON response from AI.'
            );
        }

        return $data;
    }
}