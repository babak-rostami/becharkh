<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DownloadYoutubeTelegramBotController extends Controller
{

    private $TELEGRAM_API_URL = 'https://api.telegram.org/bot{6866220849:AAEKJF-F8BomLkxYYpYsd1TGKzI_jnHforw}/';

    public function uploadYoutubeVideo(Request $request)
    {
        $botToken = '6866220849:AAEKJF-F8BomLkxYYpYsd1TGKzI_jnHforw';
        $chatId = 'YOUR_CHAT_ID';

        $response = Http::withToken($botToken)
            ->attach('video', file_get_contents($request->file('video')->getRealPath()), 'video.mp4')
            ->post($this->TELEGRAM_API_URL . "sendVideo", [
                'chat_id' => $chatId,
            ]);

        if ($response->status() === 200) {
            $file = $response->json('result.video.file_id');
            return response()->json(['file_id' => $file]);
        }

        return response()->json(['error' => 'Failed to upload video'], 400);
    }
}
