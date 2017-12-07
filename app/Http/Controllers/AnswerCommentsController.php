<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Answer;
use App\AnswerComment;

class AnswerCommentsController extends Controller
{
    public function listAll(Question $question, Answer $answer, Request $request)
    {
        return response()->json($answer->comments()->all(), 200);
    }
    
    public function view(Question $question, Answer $answer, AnswerComment $answerComment, Request $request)
    {
        return response()->json($answerComment, 200);
    }
    
    public function create(Question $question, Answer $answer, Request $request)
    {
        try {
            $this->validate($request, [
                'content' => 'required'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to add comment', 'errors' => $errors], 400);
        }
        
        $user = $request->user();
        
        if (!$user->hasPrivilege('answer_comment:add')) {
            return response()->json(['message' => 'You do not have permission to comment on answers'], 401);
        }
        
        $answer->comments()->create([
            'content' => $request->input('content'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        
        return response()->json(['message' => 'Comment created'], 200);
    }
    
    public function update(Question $question, Answer $answer, Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required|exists:answer_comments',
                'content' => 'required'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to update comment', 'errors' => $errors], 400);
        }
        
        $user = $request->user();
        
        $comment = $answer->comments()->find($request->input('id'));
        
        if (
            ($comment->created_by === $user->id && !$user->hasPrivilege('answer_comment:update_self')) &&
            !$user->hasPrivilege('answer_comment:update_other')
        ) {
            return response()->json(['message' => 'You do not have permission to update this comment.'], 401);
        }
        
        $comment->updated_by = $user->id;
        $comment->content = $request->input('content');
        
        $comment->save();
        
        return response()->json(['message' => 'Comment updated'], 200);
    }
    
    public function remove(Question $question, Answer $answer, Request $request)
    {
        try {
            $this->validate($request, [
                'id' => 'required|exists:answer_comments'
            ]);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();
            
            return response()->json(['message' => 'Failed to delete comment', 'errors' => $errors], 400);
        }
        
        $user = $request->user();
        $comment = $answer->comments()->find($request->input('id'));
        
        if (
            ($comment->created_by === $user->id && !$user->hasPrivilege('answer_comment:delete_self')) &&
            !$user->hasPrivilege('answer_comment:delete_other')
        ) {
            return response()->json(['message' => 'You do not have permission to delete this comment.'], 401);
        }
        
        $comment->delete();
        
        return response()->json(['message' => 'Comment deleted'], 200);
    }
}
