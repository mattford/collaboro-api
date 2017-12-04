<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\Answer;

class AnswersController extends Controller
{
    public function listAll(Question $question, Request $request)
    {
        $answers = $question->answers()->with(['creator', 'updator'])->get();
        
        return response()->json($answers, 200);
    }
    
    public function view(Question $question, Answer $answer, Request $request)
    {
        if (empty($answer) || empty($question) || $answer->question_id !== $question->id) {
            return response()->json(['message' => 'Answer not found'], 404);
        }
        
        return response()->json($answer->with(['creator', 'updator'])->get(), 200);
    }
    
    public function create(Question $question, Request $request)
    {
        $validated = $this->validate($request, [
            'content' => 'required'
        ]);
        
        $loggedInUser = $request->user();
        
        $question->answers()->create([
            'content' => $request->input('content'),
            'created_by' => $loggedInUser->id,
            'updated_by' => $loggedInUser->id
        ]);
        
        return response()->json(['message' => 'Answer saved'], 200);
    }
    
    public function update(Question $question, Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required|exists:answers',
            'content' => 'required'
        ]);
        
        $loggedInUser = $request->user();
        
        $answer = $question->answers()->find($request->input('id'));
        
        if (empty($answer)) {
            return response()->json(['message' => 'Answer not found'], 404);
        }
        
        $answer->fill($request->all() + ['updated_by' => $loggedInUser->id]);
        
        $answer->save();
        
        return response()->json(['message' => 'Answer updated'], 200);
    }
    
    public function remove(Question $question, Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required|exists:answers'
        ]);
        
        $answer = $question->answers()->find($request->input('id'));
        
        if (empty($answer)) {
            return response()->json(['message' => 'Answer not found'], 404);
        }
        
        $answer->delete();
        
        return response()->json(['message' => 'Answer deleted'], 200);
    }
    
    public function solved(Question $question, Answer $answer, Request $request)
    {
        $user = $request->user();
        
        if ($user->id !== $question->id || !$user->hasPrivilege('answer:mark_solution')) {
            return response()->json(['message' => 'You do not have permission to mark this answer as the solution'], 400);
        }
        
        if ($answer->solution === true) {
            return response()->json(['message' => 'Answer is already marked as solution'], 200);
        }
        
        $answer->creator()->increment('points', 10);
        
        $answer->solution = true;
        
        $answer->save();
        
        return response()->json(['message' => 'Answer marked as solution'], 200);
    }
    
    public function addVote(Question $question, Answer $answer, Request $request)
    {
        $this->validate($request, [
            'direction' => 'required|in:up,down'
        ]);
        
        $user = $request->user();
        
        if (!$user->hasPrivilege('answer:vote')) {
            return response()->json(['message' => 'You do not have permission to vote on answers'], 400);
        }
        
        $this->undoExistingVotes($answer, $user);
        
        $answer->votes()->attach($user->id, ['direction' => $request->input('direction')]);
        
        switch ($request->input('direction')) {
            case 'up':
                $answer->increment('points', 1);
                $answer->creator()->increment('points', 1);
                break;
            case 'down':
                $answer->decrement('points', 1);
                $answer->creator()->decrement('points', 1);
                break;
        }
        
        return response()->json(['message' => 'Vote saved'], 200);
    }
    
    public function clearVote(Question $question, Answer $answer, Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasPrivilege('answer:vote')) {
            return response()->json(['message' => 'You do not have permission to vote on answers'], 400);
        }
        
        $this->undoExistingVotes($answer, $user);
        
        return response()->json(['message' => 'Votes cleared'], 200);
    }
    
    private function undoExistingVotes(Answer $answer, User $user)
    {
        // This should probably be moved elsewhere
        $votes = $answer->votes()->where('user_id', $user->id)->get();
        
        foreach ($votes as $vote) {
            switch ($vote->pivot->direction) {
                case 'up':
                    $answer->decrement('points', 1);
                    $answer->creator()->decrement('points', 1);
                    break;
                case 'down':
                    $answer->increment('points', 1);
                    $answer->creator()->increment('points', 1);
                    break;
            }
            
            $question->votes()->detach($vote);
        }
    }
}
