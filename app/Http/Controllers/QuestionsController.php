<?php
namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class QuestionsController extends Controller
{
    /**
     * List of questions based on filters
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function search(Request $request): JsonResponse
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
     * @param $slug
     * @return JsonResponse
     */
    public function view($slug): JsonResponse
    {
        $question = Question::where('slug', $slug)->first();
        return response()->json($question);
    }

    /**
     * Create a new question
     *
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Request $request): JsonResponse
    {
        $this->validate($request, [
            'title' => 'required|max:100',
            'content' => 'required',
            'tags' => 'array',
            'tags.*' => 'exists:tags,slug'
        ]);
        
        $loggedInUser = $request->user();
        
        $question = new Question([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'created_by' => $loggedInUser->id,
            'updated_by' => $loggedInUser->id,
            'slug' => Str::slug($request->input('title'), '-') . '-' . rand(0, 10000)
        ]);
        
        $question->save();
        
        $question->tags()->sync($request->input('tags', []));
        
        return response()->json(['message' => 'Question created']);
    }

    /**
     * update a question
     *
     * @param Question $question
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Question $question, Request $request): JsonResponse
    {
        $this->validate($request, [
            'id' => 'required|exists:questions',
            'title' => 'max:100',
            'tags' => 'array',
            'tags.*' => 'exists:tags'
        ]);
        
        $loggedInUser = $request->user();
        $question->fill($request->all() + ['updated_by' => $loggedInUser->id]);
        $question->save();

        if ($request->input('tags') !== null) {
            $question->tags()->sync($request->input('tags'));
        }

        return response()->json(['message' => 'Question updated.']);
    }

    /**
     * remove a question
     *
     * @param Question $question
     * @return JsonResponse
     */
    public function remove(Question $question): JsonResponse
    {
        $question->delete();
        return response()->json(['message' => 'Question deleted.']);
    }

    /**
     * Votes either up or down on a question. If the user has already voted the same way, remove the vote.
     * If the user already voted the other way, remove that vote, and vote the new way.
     * @param Question $question
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function addVote(Question $question, Request $request): JsonResponse
    {
        $this->validate($request, [
            'direction' => 'required|in:up,down'
        ]);

        $user = $request->user();

        $this->undoExistingVotes($question, $user);
        
        $question->votes()->attach($user->id, ['direction' => $request->input('direction')]);
        
        switch ($request->input('direction')) {
            case 'up':
                $question->increment('points');
                break;
            case 'down':
                $question->decrement('points');
                break;
        }
        
        return response()->json(['message' => 'Vote saved']);
    }
    
    public function clearVote(Question $question, Request $request): JsonResponse
    {
        $user = $request->user();
        $this->undoExistingVotes($question, $user);
        return response()->json(['message' => 'Votes cleared']);
    }
    
    private function undoExistingVotes(Question $question, User $user): void
    {
        // This should probably be moved elsewhere
        $votes = $question->votes()->where('user_id', $user->id)->get();
        
        foreach ($votes as $vote) {
            switch ($vote->pivot->direction) {
                case 'up':
                    $question->decrement('points');
                    break;
                case 'down':
                    $question->increment('points');
                    break;
            }
            
            $question->votes()->detach($vote);
        }
    }
}
