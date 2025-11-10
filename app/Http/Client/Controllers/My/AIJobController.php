<?php

namespace App\Http\Client\Controllers\My;

use App\Http\Client\Controllers\Controller;
use App\Http\Client\Resources\AIJobResource;
use Illuminate\Http\Request;

final class AIJobController extends Controller
{

    /**
     * @api {get} /api/my/ai/jobs 1. Список згенерованих зображень
     * @apiVersion 1.0.0
     * @apiName AIJobsIndex
     * @apiGroup AIJobs
     *
     * @apiSuccessExample {json} Response-Example: HTTP/1.1 200 OK
     *  {
     *      "data": [
     *          {
     *              "id": "019a6f55-375d-7204-80fe-c96141b45694",
     *              "name": "300",
     *              "url": "http:\/\/novita.test\/storage\/019a6f55-375d-7204-80fe-c96141b45694\/300.jpeg",
     *              "conversions": {
     *                  "thumb": {
     *                      "url": "http:\/\/novita.test\/storage\/019a6f55-375d-7204-80fe-c96141b45694\/conversions\/300-thumb.webp"
     *                  }
     *              },
     *              "states": {
     *                  "is_favorite": true
     *              }
     *          }
     *      ]
     *  }
     *
     * @apiErrorExample {json} Response-Error: HTTP/1.1 401 Unauthorized
     *  {
     *     "message": "Unauthenticated"
     *  }
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $aiJobs = $user->aijobs()
            ->latest()
            ->with(['media'])
            ->paginate(5);

        return AIJobResource::collection($aiJobs);
    }
}
