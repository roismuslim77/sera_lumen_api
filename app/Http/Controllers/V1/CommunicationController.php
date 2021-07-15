<?php

namespace App\Http\Controllers\V1;

use App\Helpers\Format;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Mailgun\HttpClient\HttpClientConfigurator;
use Mailgun\Hydrator\NoopHydrator;
use Mailgun\Mailgun;

class CommunicationController extends Controller
{

    public function __construct()
    {

    }

    /**
     * @OA\Post(
     *     path="/api/v1/email/send",
     *     operationId="email/send",
     *     tags={"Communication"},
     *     @OA\Parameter(
     *         name="from",
     *         in="query",
     *         description="The from parameter in path",
     *         required=true,
     *         example="ahmad@gmail.com",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="to",
     *         in="query",
     *         description="The to user parameter in path",
     *         required=true,
     *         example="puterstreet@gmail.com",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="subject",
     *         in="query",
     *         description="The subject user parameter in path",
     *         required=true,
     *         example="Hello ?",
     *         @OA\Schema(type="string")
     *     ),
     *    @OA\Parameter(
     *         name="text",
     *         in="query",
     *         description="The text user parameter in path",
     *         required=true,
     *         example="This is email from mailgun api",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Returns some sample category things",
     *         @OA\JsonContent(
     *          @OA\Property(
     *              property="error",
     *              description="List of users",
     *              example=false,
     *              type="boolean"
     *          ),
     *          @OA\Property(
     *              property="message",
     *              description="List of users",
     *              example="Queued. Thank you.",
     *              type="string"
     *          )
     *         )
     *     ),
     *     @OA\Response(
     *         response="400",
     *         description="Error: Bad request. When required parameters were not supplied.",
     *     ),
     * )
     */
    public function sentEmail(Request $request)
    {
        $validatedData = $this->validate($request, [
            'from' => 'required',
            'to' => 'required',
            'subject' => 'required',
            'text' => 'required'
        ]);

        # Instantiate the client.
        $mgClient = Mailgun::create(env('MAILGUN_PRIVATE_API_KEY'), env('MAILGUN_HOSTNAME_API'));
        $domain = env('MAILGUN_DOMAIN_NAME');
        $params = array(
            'from'    => $validatedData['from'],
            'to'      => $validatedData['to'],
            'subject' => $validatedData['subject'],
            'text'    => $validatedData['text']
        );

        # Make the call to the client.
        $result = $mgClient->messages()->send($domain, $params);
        
        return Format::responses(null, null, false, $result->getMessage());
    }
}