<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DiscussionGroupController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('dashboard');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'create'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'createRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'storeRegister'])->name('register.store');
});

Route::post('/logout', [AuthController::class, 'destroy'])->middleware('auth')->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile/settings', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/settings', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/{user}/avatar', [ProfileController::class, 'avatar'])->name('profile.avatar');
    Route::get('/profile/{user}', [ProfileController::class, 'show'])->name('profile.show');

    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::post('/reports', [ReportController::class, 'store'])->name('reports.store');
    Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');
    Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');

    Route::get('/groups', [DiscussionGroupController::class, 'index'])->name('groups.index');
    Route::post('/groups', [DiscussionGroupController::class, 'store'])->name('groups.store');
    Route::get('/groups/{group}', [DiscussionGroupController::class, 'show'])->name('groups.show');
    Route::put('/groups/{group}', [DiscussionGroupController::class, 'update'])->name('groups.update');
    Route::get('/groups/{group}/image', [DiscussionGroupController::class, 'image'])->name('groups.image');
    Route::post('/groups/{group}/join', [DiscussionGroupController::class, 'join'])->name('groups.join');
    Route::post('/groups/{group}/leave', [DiscussionGroupController::class, 'leave'])->name('groups.leave');
    Route::post('/groups/{group}/members', [DiscussionGroupController::class, 'updateMembers'])->name('groups.members.update');

    Route::post('/groups/{group}/messages', [MessageController::class, 'store'])->name('messages.store');
    Route::get('/groups/{group}/messages', [MessageController::class, 'index'])->name('messages.index');
    Route::post('/groups/{group}/typing', [MessageController::class, 'typing'])->name('messages.typing');
    Route::get('/groups/{group}/typing', [MessageController::class, 'typingStatus'])->name('messages.typing.status');
    Route::post('/messages/{message}/reactions', [MessageController::class, 'react'])->name('messages.react');
    Route::delete('/messages/{message}', [MessageController::class, 'destroy'])->name('messages.destroy');
    Route::post('/messages/{message}/restore', [MessageController::class, 'restore'])->name('messages.restore');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [NotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllRead'])->name('notifications.readAll');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});
