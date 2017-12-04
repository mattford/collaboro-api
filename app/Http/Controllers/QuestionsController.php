<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Question;
use App\User;

class QuestionsController extends Controller
{
    /**
     * List of questions based on filters
     *
     * @param Request $request
     *
     * @return Response
     */
    public function search(Request $request)
    {
        $questions = Question::query()->with(['creator', 'updator', 'tags']);
        
        // tag filter
        if ($request->input('tags') !== null) {
            $questions->whereHas('tags', function($query) use ($request) {
                $tags = explode(',', $request->input('tags'));
                $query->whereIn('slug', $tags);
            });
        }
        // user filter
        if ($request->input('username') !== null) {
            $questions->whereHas('creator', function($query) use ($request) {
                $query->where('username', $request->input('username'));
            });
        }
        
        
        return response()->json($questions->get(), 200);
    }
    
    /**
     * Create a new question
     *
     * @param Request $request
     *
     * @return Response
     */
    public function view($slug, Request $request)
    {
        $question = Question::where('slug', $slug)->first();
        
        
        return response()->json($question, 200);
    }
    
    /**
     * Create a new question
     *
     * @param Request $request
     *
     * @return Response
     */
    public function create(Request $request)
    {
        $validated = $this->validate($request, [
            'title' => 'required|max:100',
            'content' => 'required',
            'tags' => 'array',
            'tags.*' => 'exists:tags,slug'
        ]);
        
        $loggedInUser = $request->user();
        
        if (!$loggedInUser->hasPrivilege('question:create')) {
            return response()->json(['message' => 'You do not have permission to create a new question'], 400);
        }
        
        $question = new Question([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'created_by' => $loggedInUser->id,
            'updated_by' => $loggedInUser->id,
            'slug' => str_slug($request->input('title'), '-') . '-' . rand(0, 10000)
        ]);
        
        $question->save();
        
        $question->tags()->sync($request->input('tags', []));
        
        return response()->json(['message' => 'Question created'], 200);
    }
    
    /**
     * update a new question
     *
     * @param Request $request
     *
     * @return Response
     */
    public function update(Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required|exists:questions',
            'title' => 'max:100',
            'tags' => 'array',
            'tags.*' => 'exists:tags'
        ]);
        
        $loggedInUser = $request->user();
        
        $question = Question::find($request->input('id'));
        
        if (
            ($question->created_by === $loggedInUser->id && $loggedInUser->hasPrivilege('question:edit_self')) ||
            $loggedInUser->hasPrivilege('question:edit_other')
        ) {
            $question->fill($request->all() + ['updated_by' => $loggedInUser->id]);
        
            $question->save();
            
            if ($request->input('tags') !== null) {
                $question->tags()->sync($request->input('tags'));
            }
        
            return response()->json(['message' => 'Question updated.'], 200);
        }
        
        return response()->json(['message' => 'You do not have permission to update this question.'], 400);
    }
    
    /**
     * remove a question
     *
     * @param Request $request
     *
     * @return Response
     */
    public function remove(Request $request)
    {
        $validated = $this->validate($request, [
            'id' => 'required|exists:questions'
        ]);
        
        $loggedInUser = $request->user();
        
        $question = Question::find($request->input('id'));
        
        if (
            ($question->created_by === $loggedInUser->id && $loggedInUser->hasPrivilege('question:delete_self')) ||
            $loggedInUser->hasPrivilege('question:delete_other')
        ) {
            $question->delete();
        
            return response()->json(['message' => 'Question deleted.'], 200);
        }
        
        return response()->json(['message' => 'You do not have permission to delete this question.'], 400);
    }
    
    public function addVote(Question $question, Request $request)
    {
        $this->validate($request, [
            'direction' => 'required|in:up,down'
        ]);
        
        $user = $request->user();
        
        if (!$user->hasPrivilege('question:vote')) {
            return response()->json(['message' => 'You do not have permission to vote on questions'], 400);
        }

        $this->undoExistingVotes($question, $user);
        
        $question->votes()->attach($user->id, ['direction' => $request->input('direction')]);
        
        switch ($request->input('direction')) {
            case 'up':
                $question->increment('points', 1);
                break;
            case 'down':
                $question->decrement('points', 1);
                break;
        }
        
        return response()->json(['message' => 'Vote saved'], 200);
    }
    
    public function clearVote(Question $question, Request $request)
    {
        $user = $request->user();
        
        if (!$user->hasPrivilege('question:vote')) {
            return response()->json(['message' => 'You do not have permission to vote on questions'], 400);
        }
        
        $this->undoExistingVotes($question, $user);
        
        return response()->json(['message' => 'Votes cleared'], 200);
    }
    
    private function undoExistingVotes(Question $question, User $user)
    {
        // This should probably be moved elsewhere
        $votes = $question->votes()->where('user_id', $user->id)->get();
        
        foreach ($votes as $vote) {
            switch ($vote->pivot->direction) {
                case 'up':
                    $question->decrement('points', 1);
                    break;
                case 'down':
                    $question->increment('points', 1);
                    break;
            }
            
            $question->votes()->detach($vote);
        }
    }
}
