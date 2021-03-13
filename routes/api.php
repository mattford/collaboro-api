<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('/register', 'UsersController@register');

Route::post('/login', 'UsersController@login');

Route::middleware('auth:api')->group(function() {
    Route::patch('/user', 'UsersController@update');

    Route::get('/user/me', function (Request $request) {
        return $request->user();
    });
    
    Route::get('/user/{user}', function(\App\User $user) {
        return $user;
    });
    
    Route::get('/user/{user}/privileges', 'PrivilegesController@getPrivilegesForUser');
    Route::post('/user/{user}/privileges', 'PrivilegesController@addPrivilegeToUser');
    Route::delete('/user/{user}/privileges', 'PrivilegesController@deletePrivilegeFromUser');
    
    Route::get('/questions', 'QuestionsController@search');
    Route::get('/questions/{slug}', 'QuestionsController@view');
    Route::post('/questions', 'QuestionsController@create')->middleware('can:create,App\Models\Question');
    Route::patch('/questions/{question}', 'QuestionsController@update')->middleware('can:edit,question');
    Route::delete('/questions/{question}', 'QuestionsController@remove')->middleware('can:delete,question');
    Route::post('/questions/{question}/vote', 'QuestionsController@addVote')->middleware('can:vote,App\Models\Question');
    Route::delete('/questions/{question}/vote', 'QuestionsController@clearVote')->middleware('can:vote,App\Models\Question');;
    
    Route::get('/questions/{question}/answers', 'AnswersController@listAll');
    Route::get('/questions/{question}/answers/{answer}', 'AnswersController@view');
    Route::post('/questions/{question}/answers', 'AnswersController@create')->middleware('can:create,App\Models\Answer');
    Route::patch('/questions/{question}/answers/{answer}', 'AnswersController@update')->middleware('can:edit,answer');
    Route::delete('/questions/{question}/answers/{answer}', 'AnswersController@remove')->middleware('can:delete,answer');
    Route::patch('/questions/{question}/answers/{answer}/solved', 'AnswersController@solved')->middleware('can:accept-answer,question');
    Route::post('/questions/{question}/answers/{answer}/vote', 'AnswersController@addVote')->middleware('can:vote,answer');
    Route::delete('/questions/{question}/answers/{answer}/vote', 'AnswersController@clearVote')->middleware('can:vote,answer');
    
    // Question comments
    Route::get('/questions/{question}/comments', 'QuestionCommentsController@listAll');
    Route::get('/questions/{question}/comments/{comment}', 'QuestionCommentsController@view');
    Route::post('/questions/{question}/comments', 'QuestionCommentsController@create')->middleware('can:add-comment,App\Models\Question');
    Route::patch('/questions/{question}/comments/{comment}', 'QuestionCommentsController@update')->middleware('can:edit-comment,question,comment');
    Route::delete('/questions/{question}/comments/{comment}', 'QuestionCommentsController@remove')->middleware('can:delete-comment,question,comment');
    
    // Answer comments
    Route::get('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@listAll');
    Route::get('/questions/{question}/answers/{answer}/comments/{comment}', 'AnswerCommentsController@view');
    Route::post('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@create')->middleware('can:add-comment,App\Models\Answer');
    Route::patch('/questions/{question}/answers/{answer}/comments/{comment}', 'AnswerCommentsController@update')->middleware('can:edit-comment,answer,comment');
    Route::delete('/questions/{question}/answers/{answer}/comments/{comment}', 'AnswerCommentsController@remove')->middleware('can:delete-comment,answer,comment');
});
