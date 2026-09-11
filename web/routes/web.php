<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ProjectMemberController;
use App\Http\Controllers\SubtaskController;
use App\Http\Controllers\TaskController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function (): void {
    Route::get('/registro', [RegisterController::class, 'create'])->name('register');
    Route::post('/registro', [RegisterController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/acceder', [LoginController::class, 'create'])->name('login');
    Route::post('/acceder', [LoginController::class, 'store'])->middleware('throttle:5,1');

    Route::get('/contrasena/olvidada', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('/contrasena/olvidada', [PasswordResetLinkController::class, 'store'])->middleware('throttle:3,1')->name('password.email');
    Route::get('/contrasena/restablecer/{token}', [ResetPasswordController::class, 'create'])->name('password.reset');
    Route::post('/contrasena/restablecer', [ResetPasswordController::class, 'store'])->name('password.update');
});

Route::middleware('auth')->group(function (): void {
    Route::get('/', [ProjectController::class, 'index'])->name('dashboard');
    Route::get('/cuenta', [AccountController::class, 'edit'])->name('account.edit');
    Route::put('/cuenta/contrasena', [AccountController::class, 'updatePassword'])->middleware('throttle:5,1')->name('account.password.update');
    Route::get('/proyectos/nuevo', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/proyectos', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/proyectos/{project}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/proyectos/{project}/editar', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::put('/proyectos/{project}', [ProjectController::class, 'update'])->name('projects.update');
    Route::post('/proyectos/{project}/archivar', [ProjectController::class, 'archive'])->name('projects.archive');
    Route::post('/proyectos/{project}/restaurar', [ProjectController::class, 'restore'])->name('projects.restore');
    Route::get('/proyectos/{project}/miembros/nuevo', [ProjectMemberController::class, 'create'])->name('project-members.create');
    Route::post('/proyectos/{project}/miembros', [ProjectMemberController::class, 'store'])->name('project-members.store');
    Route::put('/proyectos/{project}/miembros/{user}', [ProjectMemberController::class, 'update'])->name('project-members.update');
    Route::delete('/proyectos/{project}/miembros/{user}', [ProjectMemberController::class, 'destroy'])->name('project-members.destroy');
    Route::get('/proyectos/{project}/tareas/nueva', [TaskController::class, 'create'])->name('tasks.create');
    Route::post('/proyectos/{project}/tareas', [TaskController::class, 'store'])->name('tasks.store');
    Route::get('/proyectos/{project}/tareas/{task}/editar', [TaskController::class, 'edit'])->name('tasks.edit');
    Route::put('/proyectos/{project}/tareas/{task}', [TaskController::class, 'update'])->name('tasks.update');
    Route::patch('/proyectos/{project}/tareas/{task}/estado', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::patch('/proyectos/{project}/tareas/{task}/completada', [TaskController::class, 'updateCompletion'])->name('tasks.completion');
    Route::patch('/proyectos/{project}/tareas/{task}/subtareas/{subtask}', [SubtaskController::class, 'update'])->name('subtasks.update');
    Route::delete('/proyectos/{project}/tareas/{task}', [TaskController::class, 'destroy'])->name('tasks.destroy');
    Route::get('/proyectos/{project}/papelera/tareas', [TaskController::class, 'trash'])->name('tasks.trash');
    Route::post('/proyectos/{project}/papelera/tareas/{task}/restaurar', [TaskController::class, 'restore'])->name('tasks.restore');
    Route::get('/proyectos/{project}/calendario', [CalendarController::class, 'index'])->name('calendar.index');
    Route::get('/proyectos/{project}/calendario/datos', [CalendarController::class, 'feed'])->name('calendar.feed');
    Route::get('/proyectos/{project}/eventos/nuevo', [EventController::class, 'create'])->name('events.create');
    Route::post('/proyectos/{project}/eventos', [EventController::class, 'store'])->name('events.store');
    Route::get('/proyectos/{project}/eventos/{event}/editar', [EventController::class, 'edit'])->name('events.edit');
    Route::put('/proyectos/{project}/eventos/{event}', [EventController::class, 'update'])->name('events.update');
    Route::delete('/proyectos/{project}/eventos/{event}', [EventController::class, 'destroy'])->name('events.destroy');
    Route::get('/proyectos/{project}/papelera/eventos', [EventController::class, 'trash'])->name('events.trash');
    Route::post('/proyectos/{project}/papelera/eventos/{event}/restaurar', [EventController::class, 'restore'])->name('events.restore');
    Route::post('/salir', [LoginController::class, 'destroy'])->name('logout');
});
