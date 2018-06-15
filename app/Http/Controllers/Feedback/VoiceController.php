<?php

namespace App\Http\Controllers\Feedback;


use App\Argo\Survey;
use App\Http\Controllers\Controller;

class VoiceController extends Controller
{
    const VOICE_ITEMS_PER_PAGE = 10;

    function voiceIndexApi()
    {
        $paginated = Survey::selectRaw("
                id,
                'message' AS 'message',
                payload->>'$.base.phone' AS `group_name`,
                updated_at
            ")
            ->where('source', '=', 'argopbx')
            ->orderBy('updated_at', 'DESC')
            ->paginate(self::VOICE_ITEMS_PER_PAGE);

        return response()->json([
            'payload' => $paginated->items(),
            'total_amount' => $paginated->total()
        ]);
    }

    function voiceShowApi($surveyId)
    {
        $data = Survey::selectRaw("
                id,
                payload->>'$.base.phone' AS `group_name`,
                payload->>'$.data.questions[0].response.open_audio_url' AS audio,
                updated_at
            ")
            ->where('source', '=', 'argopbx')
            ->find($surveyId);

        return response()->json([
            'group_name' => $data->group_name,
            'updated_at' => (string)($data->updated_at),
            'payload' => [[
                'id' => $data->id,
                'type' => 'received',
                'audio' => $data->audio,
                'updated_at' => (string)$data->updated_at,
            ]]
        ]);
    }
}