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

Route::middleware('jwt.auth')->group(function() {
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
    Route::post('/questions', 'QuestionsController@create');
    Route::patch('/questions', 'QuestionsController@update');
    Route::delete('/questions', 'QuestionsController@remove');
    Route::post('/questions/{question}/vote', 'QuestionsController@addVote');
    Route::delete('/questions/{question}/vote', 'QuestionsController@clearVote');
    
    Route::get('/questions/{question}/answers', 'AnswersController@listAll');
    Route::get('/questions/{question}/answers/{answer}', 'AnswersController@view');
    Route::post('/questions/{question}/answers', 'AnswersController@create');
    Route::patch('/questions/{question}/answers', 'AnswersController@update');
    Route::delete('/questions/{question}/answers', 'AnswersController@remove');
    Route::patch('/questions/{question}/answers/{answer}/solved', 'AnswersController@solved');
    Route::post('/questions/{question}/answers/{answer}/vote', 'AnswersController@addVote');
    Route::delete('/questions/{question}/answers/{answer}/vote', 'AnswersController@clearVote');
    
    // Question comments
    Route::get('/questions/{question}/comments', 'QuestionCommentsController@listAll');
    Route::get('/questions/{question}/comments/{question_comment}', 'QuestionCommentsController@view');
    Route::post('/questions/{question}/comments', 'QuestionCommentsController@create');
    Route::patch('/questions/{question}/comments', 'QuestionCommentsController@update');
    Route::delete('/questions/{question}/comments', 'QuestionCommentsController@remove');
    
    // Answer comments
    Route::get('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@listAll');
    Route::get('/questions/{question}/answers/{answer}/comments/{answer_comment}', 'AnswerCommentsController@view');
    Route::post('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@create');
    Route::patch('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@update');
    Route::delete('/questions/{question}/answers/{answer}/comments', 'AnswerCommentsController@remove');
});
