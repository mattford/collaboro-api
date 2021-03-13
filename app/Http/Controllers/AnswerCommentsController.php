<?php
namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Question;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Answer;

class AnswerCommentsController extends Controller
{
    public function listAll(Answer $answer): JsonResponse
    {
        return response()->json($answer->comments);
    }
    
    public function view(Comment $answerComment): JsonResponse
    {
        return response()->json($answerComment);
    }
    
    public function create(Answer $answer, Request $request): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        $user = $request->user();
        $answer->comments()->create([
            'content' => $request->input('content'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        
        return response()->json(['message' => 'Comment created']);
    }
    
    public function update(Question $question, Answer $answer, Comment $comment, Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|exists:answer_comments',
            'content' => 'required'
        ]);
        
        $user = $request->user();
        
        $comment->updated_by = $user->id;
        $comment->content = $request->input('content');
        
        $comment->save();
        
        return response()->json(['message' => 'Comment updated']);
    }
    
    public function remove(Question $question, Answer $answer, Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}
