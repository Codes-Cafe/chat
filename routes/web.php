<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\TeacherController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

//admin routes
Route::post('/admin/save', [AdminController::class, 'registerFucntion'])->name('admin.checkregister');
Route::post('/admin/check', [AdminController::class, 'loginFunction'])->name('admin.checklogin');
Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');
Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');
Route::get('/admin/register', [AdminController::class, 'register'])->name('admin.register');
Route::get('/admin/chats', [AdminController::class, 'chats'])->name('admin.admin_chat');
Route::get('/admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.admin_dashboard');

//user routes
Route::post('/user/save', [UserController::class, 'registerFucntion'])->name('user.checkregister');
Route::post('/user/check', [UserController::class, 'loginFunction'])->name('user.checklogin');
Route::get('/user/logout', [UserController::class, 'logout'])->name('user.logout');
Route::get('/user/login', [UserController::class, 'login'])->name('user.login');
Route::get('/user/register', [UserController::class, 'register'])->name('user.register');
Route::get('/user/chats/{teacherId}', [UserController::class, 'chats'])->name('user.user_chat');
Route::get('/user/dashboard', [UserController::class, 'dashboard'])->name('user.user_dashboard');

Route::post('/user/fetch-teacher-messages', [ChatController::class, 'fetchOfTeacherMessages'])
    ->name('fetch.teacher.messages');
Route::post('/user/send-message-to-teacher', [ChatController::class, 'sendMessageFromUserToTeacher'])
    ->name('send.message.to.teacher');


//teacher routes
Route::post('/teacher/save', [TeacherController::class, 'registerFucntion'])->name('teacher.checkregister');
Route::post('/teacher/check', [TeacherController::class, 'loginFunction'])->name('teacher.checklogin');
Route::get('/teacher/logout', [TeacherController::class, 'logout'])->name('teacher.logout');
Route::get('/teacher/login', [TeacherController::class, 'login'])->name('teacher.login');
Route::get('/teacher/register', [TeacherController::class, 'register'])->name('teacher.register');
Route::get('/teacher/chats/{userId}', [TeacherController::class, 'chats'])->name('teacher.teacher_chat');
Route::get('/teacher/dashboard', [TeacherController::class, 'dashboard'])->name('teacher.teacher_dashboard');

Route::get('/teacher/fetch-teacher-chat-list', [ChatController::class, 'fetchTeacherChatList'])
    ->name('teacher.fetch-teacher-chat-list');

Route::post('/teacher/fetch-user-messages', [ChatController::class, 'fetchOfUserMessages'])
    ->name('fetch.user.messages');

Route::post('/teacher/send-message-to-user', [ChatController::class, 'sendMessageFromTeacherToUser'])
    ->name('send.message.to.user');
