<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TicketCommentController;
use App\Http\Controllers\TicketCategoryController;
use App\Http\Controllers\TicketReportController;

use Illuminate\Support\Facades\Mail;





Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

//agent crud
Route::resource('agents', AgentController::class);

//Categories  crud routes
Route::resource('categories', TicketCategoryController::class);

//ticket crud routes
Route::get('/tickets', [TicketController::class, 'index'])->name('tickets.index');
Route::get('/tickets/create', [TicketController::class, 'create'])->name('tickets.create');
Route::post('/tickets', [TicketController::class, 'store'])->name('tickets.store');
Route::get('/tickets/{ticket}', [TicketController::class, 'show'])->name('tickets.show');
Route::get('/tickets/{ticket}/edit', [TicketController::class, 'edit'])->name('tickets.edit');
Route::put('/tickets/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
Route::delete('/tickets/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

//other routes for tickets
Route::put('/tickets/{ticket}/resolve', [TicketController::class, 'resolve'])->name('tickets.resolve');
Route::get('/tickets/{ticket}/close', [TicketController::class, 'showCloseForm'])->name('tickets.close.form');
Route::post('/tickets/{ticket}/close', [TicketController::class, 'close'])->name('tickets.close');
Route::put('/tickets/{ticket}/reopen', [TicketController::class, 'reopen'])->name('tickets.reopen');

//comment route
Route::post('/tickets/{ticket}/comments', [TicketCommentController::class, 'store'])->name('ticket_comments.store');

//mark as read routes
Route::get('/notifications/read-all', function () {Auth::user()->unreadNotifications->markAsRead(); return back();})->name('notifications.readAll');





Route::get('/reports', [TicketReportController::class, 'report'])->name('tickets.report');

Route::get('/tickets/report/pdf', [TicketController::class, 'reportPdf'])->name('tickets.report.pdf');


});
require __DIR__.'/auth.php';
