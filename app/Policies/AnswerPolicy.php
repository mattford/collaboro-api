<?php
namespace App\Policies;

use App\Models\Answer;
use App\Models\Comment;
use App\Models\User;

class AnswerPolicy {
    public function create(User $user): bool {
        return $user->hasPrivilege('answer:create');
    }

    public function edit(User $user, Answer $answer): bool {
        return (
            $answer->created_by === $user->id &&
            !$user->hasPrivilege('answer:update_self')
        ) && !$user->hasPrivilege('answer:update_other');
    }

    public function delete(User $user, Answer $answer): bool {
        return (
            $answer->created_by === $user->id &&
            !$user->hasPrivilege('answer:delete_self')
        ) && !$user->hasPrivilege('answer:delete_other');
    }

    public function vote(User $user): bool {
        return $user->hasPrivilege('answer:vote');
    }

    public function addComment(User $user): bool {
        return $user->hasPrivilege('question_comment:add');
    }

    public function editComment(User $user, Answer $answer, Comment $comment): bool {
        return (
                $comment->created_by === $user->id &&
                $user->hasPrivilege('answer_comment:update_self')
            ) || $user->hasPrivilege('answer_comment:update_other');
    }

    public function deleteComment(User $user, Answer $answer, Comment $comment): bool {
        return (
                $comment->created_by === $user->id &&
                $user->hasPrivilege('answer_comment:delete_self')
            ) || $user->hasPrivilege('answer_comment:delete_other');
    }
}