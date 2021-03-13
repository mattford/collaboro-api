<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Question;
use App\Models\Answer;
use Illuminate\Validation\ValidationException;

class AnswersController extends Controller
{
    public function listAll(Question $question): JsonResponse
    {
        $answers = $question->answers()->with(['creator', 'updator'])->get();
        return response()->json($answers);
    }
    
    public function view(Question $question, Answer $answer): JsonResponse
    {
        if (empty($answer) || empty($question) || $answer->question_id !== $question->id) {
            return response()->json(['message' => 'Answer not found'], 404);
        }
        
        return response()->json($answer->with(['creator', 'updator'])->first());
    }

    /**
     * Creates a new answer for the given question
     * @param Question $question
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function create(Question $question, Request $request): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        
        $loggedInUser = $request->user();
        
        $question->answers()->create([
            'content' => $request->input('content'),
            'created_by' => $loggedInUser->id,
            'updated_by' => $loggedInUser->id
        ]);
        
        return response()->json(['message' => 'Answer saved']);
    }

    /**
     * Edits an existing answer
     * @param Question $question
     * @param Answer $answer
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function update(Question $question, Answer $answer, Request $request): JsonResponse
    {
        $this->validate($request, [
            'content' => 'required'
        ]);
        
        $loggedInUser = $request->user();
        $answer->fill($request->all() + ['updated_by' => $loggedInUser->id]);
        $answer->save();
        
        return response()->json(['message' => 'Answer updated']);
    }

    /**
     * Remove an answer
     *
     * @param Question $question
     * @param Answer $answer
     * @return JsonResponse
     * @throws \Exception
     */
    public function remove(Question $question, Answer $answer): JsonResponse
    {
        $answer->delete();
        return response()->json(['message' => 'Answer deleted']);
    }
    
    public function solved(Question $question, Answer $answer): JsonResponse
    {
        // TODO: Check if the question already has a solution (possibly in QuestionPolicy)
        if ($answer->solution === true) {
            return response()->json(['message' => 'Answer is already marked as solution']);
        }
        
        $answer->creator()->increment('points', 10);
        $answer->solution = true;
        $answer->save();
        return response()->json(['message' => 'Answer marked as solution']);
    }
    
    public function addVote(Question $question, Answer $answer, Request $request): JsonResponse
    {
        $this->validate($request, [
            'direction' => 'required|in:up,down'
        ]);
        
        $user = $request->user();
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
        
        return response()->json(['message' => 'Vote saved']);
    }
    
    public function clearVote(Question $question, Answer $answer, Request $request)
    {
        $user = $request->user();
        $this->undoExistingVotes($answer, $user);
        return response()->json(['message' => 'Votes cleared']);
    }
    
    private function undoExistingVotes(Answer $answer, User $user): void
    {
        // This should probably be moved elsewhere
        $votes = $answer->votes()->where('user_id', $user->id)->get();
        
        foreach ($votes as $vote) {
            switch ($vote->pivot->direction) {
                case 'up':
                    $answer->decrement('points');
                    $answer->creator()->decrement('points');
                    break;
                case 'down':
                    $answer->increment('points');
                    $answer->creator()->increment('points');
                    break;
            }

            $answer->votes()->detach($vote);
        }
    }
}
