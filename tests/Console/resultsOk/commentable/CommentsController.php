<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\Comment;
use App\Http\Requests\CommentRequest;

class CommentsController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  CommentRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CommentRequest $request, $id)
    {
        $comment = new Comment;
        $comment->comment = $request->input('comment');
        $comment->post_id = $id;
        $comment->save();

        return redirect()->back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy($id)
    {
        $comment = Comment::findOrFail($id);
        $comment->delete();

        return redirect()->back();
    }
}
