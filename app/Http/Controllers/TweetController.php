<?php

namespace App\Http\Controllers;

use App\Models\Tweet;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TweetController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tweets = Tweet::with(['user', 'liked'])->latest()->get();
        return view('tweets.index', compact('tweets'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('tweets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 🔽 中身を追加
        $request->validate([
        'tweet' => 'required|max:255',
        'image' => 'nullable|image|max:5120',
        ]);

        $imagePath = $request->hasFile('image')
            ? $request->file('image')->store('tweets', 'public')
            : null;

        $request->user()->tweets()->create([
            'tweet' => $request->tweet,
            'image_path' => $imagePath,
        ]);

        return redirect()->route('tweets.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Tweet $tweet)
    {
        $tweet->load('comments.user');
        return view('tweets.show', compact('tweet'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Tweet $tweet)
    {
        return view('tweets.edit', compact('tweet'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Tweet $tweet)
    {
        $request->validate([
        'tweet' => 'required|max:255',
        'image' => 'nullable|image|max:5120',
        'remove_image' => 'nullable|boolean',
        ]);

        $data = ['tweet' => $request->tweet];

        if ($request->hasFile('image')) {
            if ($tweet->image_path) {
                Storage::disk('public')->delete($tweet->image_path);
            }
            $data['image_path'] = $request->file('image')->store('tweets', 'public');
        } elseif ($request->boolean('remove_image') && $tweet->image_path) {
            Storage::disk('public')->delete($tweet->image_path);
            $data['image_path'] = null;
        }

        $tweet->update($data);

        return redirect()->route('tweets.show', $tweet);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Tweet $tweet)
    {
        if ($tweet->image_path) {
            Storage::disk('public')->delete($tweet->image_path);
        }

        $tweet->delete();

        return redirect()->route('tweets.index');
    }

    public function search(Request $request)
    {
        // モデルの keyword スコープで絞り込み，ページネーションを追加（1ページに10件表示）
        $tweets = Tweet::keyword($request->keyword)
            ->latest()
            ->paginate(10);


        return view('tweets.search', compact('tweets'));
    }
}
