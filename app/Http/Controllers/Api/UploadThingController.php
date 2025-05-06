<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Http;

use App\Http\Controllers\Controller;

use Sqids\Sqids;

function djb2(string $string): int {
    $h = 5381;
    for ($i = strlen($string) - 1; $i >= 0; $i--) {
        $char = $string[$i];
        $h = ($h * 33) ^ ord($char);
        // 32-bit integer overflow
        $h &= 0xFFFFFFFF;
    }
    $h = ($h & 0xBFFFFFFF) | (($h >> 1) & 0x40000000);

    // Convert to signed 32-bit integer
    if ($h >= 0x80000000) {
        $h -= 0x100000000;
    }

    return $h;
}

function shuffle_string(string $string, string $seed): string {
    $chars = str_split($string);
    $seed_num = djb2($seed);

    for ($i = 0; $i < count($chars); $i++) {
        $j = (($seed_num % ($i + 1)) + $i) % count($chars);
        [$chars[$i], $chars[$j]] = [$chars[$j], $chars[$i]];
    }

    return implode('', $chars);
}

function generate_key(string $file_seed, string $appId = null): string {
    if ($appId === null) {
        $appId = config('uploadthing.app_id');
    }
    $alphabet = shuffle_string(Sqids::DEFAULT_ALPHABET, $appId);
    $sqids = new Sqids($alphabet, 12);

    $encodedAppId = $sqids->encode(
        [abs(djb2($appId))]
    );

    return $encodedAppId . base64_encode($file_seed);
}


class UploadThingController extends Controller {
    public function getPresignedUrlForLogo(Request $request) {
        $validated = $request->validate([
            'filename' => 'required|string|max:80',
            'filesize' => 'required|integer|max:5242880', // 5MB
        ]);

        $current = new \DateTime();

        $seed = substr((string)$current->getTimestamp(), -4)
            . $validated['filename'];

        $key = generate_key($seed);

        $url = "https://" . config('uploadthing.region_alias') . ".ingest.uploadthing.com/" . $key;

        $current = $current->getTimestamp() * 1000;

        $expires = $current + (5 * 60 * 1000); // 5 minutes

        $upload_args = [
            "expires" => $expires,
            "x-ut-identifier" => config('uploadthing.app_id'),
            "x-ut-file-name" => $validated['filename'],
            "x-ut-file-size" => $validated['filesize'],
            "x-ut-slug" => "logo"
        ];

        $pre_sig = $url . "?" . http_build_query($upload_args);

        $signature = hash_hmac('sha256', $pre_sig, config('uploadthing.secret'));

        $upload_args["signature"] = "hmac-sha256=" . $signature;

        $post_sig = $url . "?" . http_build_query($upload_args);

        $response = Http::withoutVerifying()->withHeaders([
            'x-uploadthing-api-key' => config('uploadthing.secret'),
        ])->post(
        'https://'
            . config('uploadthing.region_alias')
            . '.ingest.uploadthing.com/route-metadata', [
                    'fileKeys' => [$key],
                    'metadata' => [
                        'logo' => 'logo desune'
                    ],
                    'callbackUrl' => 'https://pekan.nazkookery.tech/api/uploadthing',
                    'callbackSlug' => 'logo',
                    'awaitServerData' => false,
                    'isDev' => false
                ]);

//        error_log($response->body());

        return response()->json([
            "uploadLink" => $post_sig
        ]);
    }
}
