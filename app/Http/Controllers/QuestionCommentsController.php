<?php
namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Comment;

class QuestionCommentsController extends Controller
{
    public function listAll(Question $question): JsonResponse
    {
        return response()->json($question->comments);
    }
    
    public function view(Comment $questionComment): JsonResponse
    {
        return response()->json($questionComment);
    }
    
    public function create(Question $question, Request $request)
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        
        $user = $request->user();
        $question->comments()->create([
            'content' => $request->input('content'),
            'created_by' => $user->id,
            'updated_by' => $user->id,
        ]);
        
        return response()->json(['message' => 'Comment created']);
    }
    
    public function update(Question $question, Comment $comment, Request $request): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        
        $user = $request->user();
        $comment->updated_by = $user->id;
        $comment->content = $request->input('content');
        
        $comment->save();
        
        return response()->json(['message' => 'Comment updated']);
    }
    
    public function remove(Question $question, Comment $comment): JsonResponse
    {
        $comment->delete();
        return response()->json(['message' => 'Comment deleted']);
    }
}
