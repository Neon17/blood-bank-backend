<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index()
    {
        $blogs = Blog::with('bloggable')->get();
        return response()->json([
            'status' => 'success',
            'total' => $blogs->count(),
            'blogs' => $blogs
        ]);
    }

    public function show($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }
        return response()->json([
            'status' => 'success',
            'blog' => $blog
        ]);
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'author' => 'required|string|max:255',
            'category' => 'required|string|max:255',
            'is_featured' => 'boolean',
            'tags' => 'nullable|string',
            'status' => 'required|string|max:50',
            'references' => 'nullable|string',
            'created_by' => 'required|integer|exists:users,id',
        ]);

        $blog = Blog::create($validatedData);

        return response()->json([
            'status' => 'success',
            'blog' => $blog
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        $validatedData = $request->validate([
            'title' => 'string|max:255',
            'description' => 'string',
            'author' => 'string|max:255',
            'category' => 'string|max:255',
            'is_featured' => 'boolean',
            'tags' => 'string',
            'status' => 'string|max:50',
            'references' => 'string',
            'created_by' => 'integer|exists:users,id',
        ]);

        $blog->update($validatedData);

        return response()->json([
            'status' => 'success',
            'blog' => $blog
        ]);
    }


    public function destroy($id)
    {
        $blog = Blog::find($id);
        if (!$blog) {
            return response()->json([
                'status' => 'error',
                'message' => 'Blog not found'
            ], 404);
        }

        $blog->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Blog deleted successfully'
        ]);
    }
}
