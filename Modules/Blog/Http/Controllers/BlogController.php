<?php

namespace Modules\Blog\Http\Controllers;

// use App\Models\Category;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Blog\Actions\CreatePost;
use Modules\Blog\Actions\DeletePost;
use Modules\Blog\Actions\UpdatePost;
use Modules\Blog\Entities\Post;
use Modules\Blog\Entities\PostCategory;
use Modules\Blog\Entities\PostComment;
use Modules\Blog\Http\Requests\PostFormRequest;
use Modules\Language\Entities\Language;

class BlogController extends Controller
{
    use ValidatesRequests;

    public function __construct()
    {
        $this->middleware(['permission:post.view'])->only('index');
        $this->middleware(['permission:post.create'])->only(['create', 'store']);
        $this->middleware(['permission:post.update'])->only(['edit', 'update']);
        $this->middleware(['permission:post.delete'])->only(['destroy']);
    }

    /**
     * Display a listing of the post list.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $categories = PostCategory::all();
        $authors = Post::select('author_id')->with('author:id,name,name')->get()->groupBy('author_id');
        $totalPosts = Post::count();
        $totalComments = PostComment::count();
        $totalAuthor = $authors->count();
        $totalCategory = $categories->count();
        $totalDraft = Post::where('status', 'draft')->count();
        $totalPublished = Post::where('status', 'published')->count();
        $languages = Language::all();

        $query = Post::with('category', 'author:id,name,name')->withCount('comments');
        if ($request->keyword && $request->keyword != null) {
            $query->where('title', 'LIKE', "%$request->keyword%");
        }

        if ($request->category && $request->category != null) {
            $category = $request->category;
            $query->whereHas('category', function ($q) use ($category) {
                $q->where('slug', $category);
            });
        }

        if ($request->author && $request->author != null) {
            $author = $request->author;
            $query->whereHas('author', function ($q) use ($author) {
                $q->where('id', $author);
            });
        }

        if ($request->code && $request->code != null) {
            $query->where('locale', $request->code);
        }

        if ($request->status && $request->status != null) {
            $query->where('status', $request->status);
        }
        $blogs = $query->latest()->paginate('15')->withQueryString();

        return view('blog::index', compact('blogs', 'categories', 'authors', 'totalComments', 'totalAuthor', 'totalPosts', 'totalDraft', 'totalPublished', 'languages'));
    }

    /**
     * Show the form for creating a new post.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $categories = PostCategory::all();
        $languages = Language::all();

        return view('blog::create', compact('categories', 'languages'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(PostFormRequest $request)
    {
        $post = CreatePost::create($request);

        if ($post) {
            flashSuccess(__('post_created_successfully'));

            return redirect()->route('module.blog.index');
        } else {
            flashError();

            return back();
        }
    }

    /**
     * Show the form for editing the specified post.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Post $post)
    {
        $categories = PostCategory::all();
        $languages = Language::all();

        return view('blog::edit', compact('categories', 'post', 'languages'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(PostFormRequest $request, Post $post)
    {
        $post = UpdatePost::update($request, $post);

        if ($post) {
            flashSuccess(__('post_updated_successfully'));

            return redirect()->route('module.blog.index');
        } else {
            flashError();

            return back();
        }
    }

    /**
     * Remove the specified post from storage.
     *
     * @return \Illuminate\Http\Response
     *
     * @throws \Exception
     */
    public function destroy(Post $post)
    {
        $post = DeletePost::delete($post);

        if ($post) {
            flashSuccess(__('post_deleted_successfully'));

            return back();
        } else {
            flashError();

            return back();
        }
    }
}
