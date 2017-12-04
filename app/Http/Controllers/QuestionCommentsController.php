<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\QuestionComment;

class QuestionCommentsController extends Controller
{
    public function listAll(Question $question, Request $request)
    {
        return response()->json($question->comments()->all(), 200);
    }
    
    public function view(Question $question, QuestionComment $questionComment, Request $request)
    {
        return response()->json($questionComment, 200);
    }
    
    public function create(Question $question, Request $request)
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        
        $user = $request->user();
        
        if (!$user->hasPrivilege('question_comment:add')) {
            return response()->json(['message' => 'You do not have permission to comment on questions'], 400);
        }
        
        $question->comments()->create([
            'content' => $request->input('content'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        
        return response()->json(['message' => 'Comment created'], 200);
    }
    
    public function update(Question $question, Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:question_comments',
            'content' => 'required'
        ]);
        
        $user = $request->user();
        $comment = $question->comments()->find($request->input('id'));
        
        if (
            ($comment->created_by === $user->id && !$user->hasPrivilege('question_comment:update_self')) ||
            ($comment->created_by !== $user->id && !$user->hasPrivilege('question_comment:update_other'))
        ) {
            return response()->json(['message' => 'You do not have permission to update this comment.'], 400);
        }
        
        $comment = $question->comments()->find($request->input('id'));
        
        $comment->updated_by = $user->id;
        $comment->content = $request->input('content');
        
        $comment->save();
        
        return response()->json(['message' => 'Comment updated'], 200);
    }
    
    public function remove(Question $question, Request $request)
    {
        $this->validate($request, [
            'id' => 'required|exists:question_comments'
        ]);
        
        $user = $request->user();
        $comment = $question->comments()->find($request->input('id'));
        
        if (
            ($comment->created_by === $user->id && !$user->hasPrivilege('question_comment:delete_self')) ||
            ($comment->created_by !== $user->id && !$user->hasPrivilege('question_comment:delete_other'))
        ) {
            return response()->json(['message' => 'You do not have permission to delete this comment.'], 400);
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted'], 200);
    }
}
