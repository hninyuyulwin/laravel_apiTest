<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\Post;
use App\Models\Media;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostDetailResource;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{

    public function create(Request $request)
    {
        $request->validate(
            [
                'title' => 'required',
                'description' => 'required',
                'category_id' => 'required',
            ],
            [
                'category_id.required' => 'The category field is required',
            ],
        );
        DB::beginTransaction();
        try {
            $file_name = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $file_name = date('YmdHis') . '.' . $file->getClientOriginalExtension();
                Storage::put('media/' . $file_name, file_get_contents($file));
            }
            $post = Post::create([
                'title' => $request->title,
                'description' => $request->description,
                'user_id' => auth()->user()->id,
                'category_id' => $request->category_id,
            ]);
            Media::create([
                'file_name' => $file_name,
                'file_type' => 'image',
                'model_id' => $post->id,
                'model_type' => Post::class,
            ]);
            DB::commit();
            return ResponseHelper::success([], 'Post Uploaded Success');
        } catch (Exception $e) {
            DB::rollBack();
            return ResponseHelper::fail($e->getMessage());
        }
    }

    public function index(Request $request)
    {
        $query = Post::with('user', 'category', 'image')->orderBy('created_at', 'desc');
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where(function ($q1) use ($request) {
                $q1->where('title', 'like', '%' . $request->search . '%')
                    ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        $post = $query->paginate(10);
        return PostResource::collection($post)->additional(['message' => 'Success']);
    }

    public function show($id)
    {
        $post = Post::with('user', 'category', 'image')->where('id', $id)->firstOrFail();
        return ResponseHelper::success(new PostDetailResource($post));
    }
}
