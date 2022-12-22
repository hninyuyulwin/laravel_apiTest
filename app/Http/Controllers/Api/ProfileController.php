<?php

namespace App\Http\Controllers\Api;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\ProfileResource;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = auth()->guard()->user();
        return ResponseHelper::success(new ProfileResource($user));
    }

    public function posts(Request $request)
    {
        $query = Post::with('user', 'category', 'image')->orderBy('created_at', 'desc')->where('user_id', auth()->user()->id);
        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->search) {
            $query->where(function ($q1) use ($request) {
                $q1->where('title', 'like', '%' . $request->search . '%')->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }
        $posts = $query->get();
        return PostResource::collection($posts)->additional(['message' => "Success"]);
    }
}
