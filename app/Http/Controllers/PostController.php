<?php

namespace App\Http\Controllers;

use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PostController extends Controller
{
    public function create(Request $request): JsonResponse
    {
        $postValidated = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($postValidated->fails()) {
            return response()->json($postValidated->errors(), 403);
        }

        try {
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->user_id = auth()->user()->id;
            $post->save();

            return response()->json([
                'message' => 'Post foi criado com successo!',
                'post' => $post
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }

    public function update(Request $request, int $postId): JsonResponse
    {
        $postValidated = Validator::make($request->all(), [
            'title' => 'required|string',
            'content' => 'required|string',
        ]);

        if ($postValidated->fails()) {
            return response()->json($postValidated->errors(), 403);
        }

        try {
            $postData = Post::find($postId);

            if (!$postData) {
                throw new ModelNotFoundException('Post não encontrado!', 404);
            }

            $updatedPost = $postData->update([
                'title' => $request->title,
                'content' => $request->content,
            ]);

            return response()->json([
                'message' => 'Post atualizado com sucesso!',
                'updated_post' => $updatedPost
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function findAll(): JsonResponse
    {
        try {
            $posts = Post::all();
            return response()->json([
                'posts' => $posts
            ], 201);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }

    public function findOne(int $postId): JsonResponse
    {
        try {

            $post = Post::find($postId);

            if (!$post) {
                throw new ModelNotFoundException('Post não encontrado', 404);
            }

            $post = new PostResource(Post::findOrFail($postId));
            return response()->json([
                'post' => $post
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }

    public function deleteOne(Request $request, int $postId): JsonResponse
    {
        try {
            $post = Post::find($postId);

            if (!$post) {
                throw new ModelNotFoundException('Post não encontrado', 404);
            }

            $post->delete();

            return response()->json([
                'message' => 'Post deletado com sucesso!'
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], $exception->getCode());
        }
    }
}
