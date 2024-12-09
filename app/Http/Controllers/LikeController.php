<?php

namespace App\Http\Controllers;

use App\Models\Like;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;

class LikeController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $likeValidated = Validator::make($request->all(), [
            'post_id' => 'required|integer',
        ]);

        if ($likeValidated->fails()) {
            return response()->json($likeValidated->errors(), 403);
        }

        try {

            $userLikedPostBefore = Like::where('user_id', auth()->user()->id)
                ->where('post_id', $request->post_id)
                ->first();

            if ($userLikedPostBefore) {
                return response()->json(['message' => 'Você não pode curtir um post duas vezes!'], 403);
            }

            $like = new Like();
            $like->post_id = $request->post_id;
            $like->user_id = auth()->user()->id;
            $like->save();

            return response()->json([
                'message' => 'Você curtiu esse post!',
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }

    }
}
