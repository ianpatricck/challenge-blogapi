<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $commentValidated = Validator::make($request->all(), [
            'post_id' => 'required|integer',
            'comment' => 'required|string',
        ]);

        if ($commentValidated->fails()) {
            return response()->json($commentValidated->errors(), 403);
        }

        try {
            $comment = new Comment();
            $comment->post_id = $request->post_id;
            $comment->comment = $request->comment;
            $comment->user_id = auth()->user()->id;
            $comment->save();

            return response()->json([
                'message' => 'Seu comentário foi adicionado!',
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }
}
