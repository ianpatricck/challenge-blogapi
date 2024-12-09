<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CommentController extends Controller
{
    public function create(Request $request, int $postId): JsonResponse
    {
        $commentValidated = Validator::make($request->all(), [
            'comment' => 'required|string',
        ]);

        if ($commentValidated->fails()) {
            return response()->json($commentValidated->errors(), 403);
        }

        try {

            $post = Post::find($postId);

            if (!$post) {
                throw new ModelNotFoundException('Post especificado não foi encontrado!', 404);
            }

            $comment = new Comment();
            $comment->post_id = $postId;
            $comment->comment = $request->comment;
            $comment->user_id = auth()->user()->id;
            $comment->save();

            return response()->json([
                'message' => 'Seu comentário foi adicionado!',
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function delete(int $commentId)
    {
        try {
            $comment = Comment::find($commentId);

            if (!$comment) {
                throw new ModelNotFoundException('Não foi possível encontrar este comentário', 404);
            }

            if (auth()->user()->id == $comment->user_id) {
                $comment->delete();
                return response()->json([
                    'message' => 'Seu comentário foi deletado!',
                ], 200);
            } else {
                throw new BadRequestException('Não foi possível deletar este comentário', 403);
            }
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }

    }
}
