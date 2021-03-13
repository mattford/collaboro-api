<?php
namespace App\Policies;

use App\Models\Comment;
use App\Models\Question;
use App\Models\User;

class QuestionPolicy {
    public function create(User $user): bool {
        return $user->hasPrivilege('question:create');
    }

    public function edit(User $user, Question $question): bool {
        return (
            $question->created_by === $user->id &&
            $user->hasPrivilege('question:edit_self')
        ) || $user->hasPrivilege('question:edit_other');
    }

    public function delete(User $user, Question $question): bool {
        return (
            $question->created_by === $user->id &&
            $user->hasPrivilege('question:delete_self')
        ) || $user->hasPrivilege('question:delete_other');
    }

    public function vote(User $user) {
        return $user->hasPrivilege('question:vote');
    }

    public function acceptAnswer(User $user, Question $question): bool {
        return $user->id === $question->created_by;
    }

    public function addComment(User $user): bool {
        return $user->hasPrivilege('question_comment:add');
    }

    public function editComment(User $user, Question $question, Comment $comment): bool {
        return (
            $comment->created_by === $user->id &&
            $user->hasPrivilege('question_comment:update_self')
        ) || $user->hasPrivilege('question_comment:update_other');
    }

    public function deleteComment(User $user, Question $question, Comment $comment): bool {
        return (
            $comment->created_by === $user->id &&
            $user->hasPrivilege('question_comment:delete_self')
        ) || $user->hasPrivilege('question_comment:delete_other');
    }
}