<?php

declare(strict_types=1);

namespace App\Features\Admin\Controllers;

use App\Features\Admin\Requests\StorePostRequest;
use App\Features\Admin\Requests\UpdatePostRequest;
use App\Features\Admin\Services\PostAdminService;
use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PostController extends Controller
{
    public function __construct(private readonly PostAdminService $postAdminService) {}

    public function index(): View
    {
        $posts = Post::query()->latest()->paginate(20);

        return view('admin.posts.index', compact('posts'));
    }

    public function create(): View
    {
        return view('admin.posts.create');
    }

    public function store(StorePostRequest $request): RedirectResponse
    {
        $post = $this->postAdminService->create(
            $request->validated(),
            $request->file('cover'),
        );

        return redirect()->route('admin.posts.edit', $post)
            ->with('success', 'Статья создана.');
    }

    public function edit(Post $post): View
    {
        return view('admin.posts.edit', compact('post'));
    }

    public function update(UpdatePostRequest $request, Post $post): RedirectResponse
    {
        $this->postAdminService->update(
            $post,
            $request->validated(),
            $request->file('cover'),
        );

        return redirect()->route('admin.posts.edit', $post)
            ->with('success', 'Статья обновлена.');
    }

    public function destroy(Post $post): RedirectResponse
    {
        $this->postAdminService->delete($post);

        return redirect()->route('admin.posts.index')
            ->with('success', 'Статья удалена.');
    }

    public function toggleStatus(Post $post): JsonResponse
    {
        $post = $this->postAdminService->toggleStatus($post);

        return response()->json(['status' => $post->status->value]);
    }
}
