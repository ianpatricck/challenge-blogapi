<?php

namespace App\Http\Controllers;

use App\Models\Like;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class LikeController extends Controller
{
    /**
     * @OA\Post(
     *      path="/likes/create",
     *      summary="Create a like",
     *      description="Create a like",
     *      tags={"Likes"},
     *      security={ {"sanctum": {} }},
     *
     *      @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="post_id",
     *                     type="integer"
     *                 ),
     *                 example={"post_id": 1}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response="201",
     *          description="CREATED",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response="404",
     *          description="NOT FOUND",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *      @OA\Response(
     *          response="403",
     *          description="NOT ALLOWED",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      )
     *  )
     */
    public function create(Request $request): JsonResponse
    {
        $likeValidated = Validator::make($request->all(), [
            'post_id' => 'required|integer',
        ]);

        if ($likeValidated->fails()) {
            return response()->json($likeValidated->errors(), 403);
        }

        try {
            $post = Post::find($request->post_id);

            if (!$post) {
                throw new ModelNotFoundException('Post especificado não foi encontrado', 404);
            }

            $userLikedPostBefore = Like::where('user_id', auth()->user()->id)
                ->where('post_id', $request->post_id)
                ->first();

            if ($userLikedPostBefore) {
                throw new \Exception('Você não pode curtir um post duas vezes!', 403);
            }

            $like = new Like();
            $like->post_id = $request->post_id;
            $like->user_id = auth()->user()->id;
            $like->save();

            return response()->json([
                'message' => 'Você curtiu esse post!',
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
