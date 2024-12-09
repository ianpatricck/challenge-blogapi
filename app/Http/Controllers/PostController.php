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
    /**
     * @OA\Post(
     *      path="/posts/create",
     *      summary="Create a post",
     *      description="Create a post",
     *      tags={"Posts"},
     *      security={ {"sanctum": {} }},
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 example={"title": "Hello World", "content": "First post"}
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
     *      )
     *  )
     */
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

    /**
     * @OA\Put(
     *      path="/posts/update/{postId}",
     *      summary="Update a post",
     *      description="Update a post",
     *      tags={"Posts"},
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          in="path",
     *          name="postId",
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="int", value="1", summary="An int Post ID.")
     *      ),
     *     @OA\RequestBody(
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="content",
     *                     type="string"
     *                 ),
     *                 example={"title": "Post updated", "content": "I updated this post"}
     *             )
     *         )
     *     ),
     *      @OA\Response(
     *          response="201",
     *          description="UPDATED",
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
     *      )
     *  )
     */
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

    /**
     * @OA\Get(
     *      path="/posts",
     *      summary="Find all posts",
     *      description="Find all posts",
     *      tags={"Posts"},
     *      @OA\Response(
     *          response="200",
     *          description="OK",
     *          @OA\MediaType(
     *              mediaType="application/json",
     *          )
     *      ),
     *  )
     */
    public function findAll(): JsonResponse
    {
        try {
            $posts = Post::all();
            return response()->json([
                'posts' => $posts
            ], 200);
        } catch (\Exception $exception) {
            return response()->json(['error' => $exception->getMessage()], 403);
        }
    }

    /**
     * @OA\Get(
     *      path="/posts/{postId}",
     *      summary="Find one post",
     *      description="Find just one post record",
     *      tags={"Posts"},
     *      @OA\Parameter(
     *          in="path",
     *          name="postId",
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="int", value="1", summary="An int Post ID.")
     *      ),
     *      @OA\Response(
     *          response="200",
     *          description="OK",
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
     *  )
     */
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

    /**
     * @OA\Delete(
     *      path="/posts/delete/{postId}",
     *      summary="Delete a post",
     *      description="Delete a post",
     *      tags={"Posts"},
     *      security={ {"sanctum": {} }},
     *      @OA\Parameter(
     *          in="path",
     *          name="postId",
     *          @OA\Schema(type="int"),
     *          @OA\Examples(example="int", value="1", summary="An int Post ID.")
     *      ),
     *      @OA\Response(
     *          response="201",
     *          description="UPDATED",
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
     *      )
     *  )
     */
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
