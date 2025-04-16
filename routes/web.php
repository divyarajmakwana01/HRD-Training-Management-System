<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ParticipantsController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\Admin\ProgrammeController;
use App\Http\Controllers\Admin\CoordinatorController;
use App\Http\Controllers\admin\ParticipantsController as AdminParticipantsController;
use App\Http\Controllers\QuestionnaireProgrammeController;
use App\Http\Controllers\Admin\ProgrammeQuestionController;

// ==========================
// Public Routes
// ==========================


Route::prefix('admin')->group(function () {
    Route::get('/programme-questions', [ProgrammeQuestionController::class, 'index'])->name('admin.programme_questions.index');
    Route::post('/programme-questions/store', [ProgrammeQuestionController::class, 'store'])->name('admin.programme_questions.store');
    Route::post('/admin/programme-questions/toggle-status', [ProgrammeQuestionController::class, 'toggleStatus'])
        ->name('admin.programme_questions.toggle_status');

});

Route::prefix('admin')->group(function () {
    Route::get('/questionnaire_programme', [QuestionnaireProgrammeController::class, 'index'])->name('admin.questionnaire_programme.index');
    Route::post('/questionnaire_programme/fetch', [QuestionnaireProgrammeController::class, 'fetchQuestions'])->name('admin.questionnaire_programme.fetch');
    Route::post('/questionnaire_programme/toggle', [QuestionnaireProgrammeController::class, 'toggleQuestion'])->name('admin.questionnaire_programme.toggleQuestion');
    Route::post('/admin/questionnaire_programme/submit', [QuestionnaireProgrammeController::class, 'submitQuestionnaire'])
        ->name('admin.questionnaire_programme.submit');

});



Route::post('/admin/export-participants', [AdminController::class, 'exportParticipants'])->name('admin.export.participants');

Route::get('/', function () {
    return view('index');
})->name('index');

Route::prefix('participants')->group(function () {

    // User Registration
    Route::get('/create_account', [LoginController::class, 'showCreateAccountForm'])->name('participants.create_account');
    Route::post('/create_account', [LoginController::class, 'createAccount'])->name('participants.create_account.post');

    // Password Reset
    Route::get('/reset_password/{token}', [LoginController::class, 'showResetPasswordForm'])->name('participants.reset_password');
    Route::post('/reset_password', [LoginController::class, 'resetPassword'])->name('participants.reset_password.post');

    Route::get('/reset_password_request', [LoginController::class, 'showResetPasswordRequestForm'])->name('participants.reset_password_request');
    Route::post('/reset_password_request', [LoginController::class, 'sendResetPasswordEmail'])->name('participants.reset_password_request.post');

    // Login & Logout
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('participants.login');
    Route::post('/login', [LoginController::class, 'login'])->name('participants.login.post');
    Route::post('/logout', [LoginController::class, 'logout'])->name('participants.logout');

    // Dashboard & Profile
    Route::get('/dashboard', [LoginController::class, 'showDashboard'])->name('participants.dashboard');
    Route::get('/profile', [ParticipantsController::class, 'showProfile'])->name('participants.profile');
    Route::post('/profile/store', [ParticipantsController::class, 'storeProfile'])->name('participants.profile.store');

    // Programme Listing
    Route::get('/programme', [ParticipantsController::class, 'showProgramme'])->name('participants.programme');

    // Registration
    Route::get('/programmes', [ParticipantsController::class, 'showProgramme'])->name('programme.list');
    Route::post('/programme/register/{programme_id}', [ParticipantsController::class, 'registerForProgramme'])->name('programme.register');

    // Payment Routes
    Route::get('/payment/{programme_id}', [ParticipantsController::class, 'getPaymentDetails'])->name('participant.payment.view');
    Route::post('/payment/{programme_id}', [ParticipantsController::class, 'storePaymentDetails'])->name('participant.payment.store');

    // Transport Routes
    Route::get('/transport/{programme_id}', [ParticipantsController::class, 'getTransportDetails'])->name('participant.transport.view');
    Route::post('/transport/{programme_id}', [ParticipantsController::class, 'storeTransportDetails'])->name('participant.transport.store');

    // Dashboard
    Route::get('/dashboard', [ProgrammeController::class, 'getParticipantDashboard'])->name('participants.dashboard');

    // Fetch Programme Questions
    Route::get('/programme/{programme_id}/questions', [ParticipantsController::class, 'getProgrammeQuestions']);

    // 🆕 Submit Programme Responses
    Route::post('/programme/submitResponse', [ParticipantsController::class, 'submitResponse'])->name('programme.submitResponse');

});


    // Captcha storage for security purposes
    Route::post('/store-captcha', [LoginController::class, 'storeCaptcha'])->name('store.captcha');

    // ==========================
    // Admin Routes
    // ==========================

Route::prefix('admin')->group(function () {
    // Admin Authentication
    Route::get('/login', [AdminController::class, 'showLoginForm'])->name('admin.login');
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.post');
    Route::get('/dashboard', [AdminController::class, 'index'])->name('admin.dashboard');
    Route::post('/logout', [AdminController::class, 'logout'])->name('admin.logout');

    // Programme Management (CRUD operations)
    Route::prefix('programme')->group(function () {
        Route::get('/', [ProgrammeController::class, 'index'])->name('admin.programme');
        Route::post('/store', [ProgrammeController::class, 'store'])->name('admin.programme.store');
        Route::get('/{id}/edit', [ProgrammeController::class, 'edit'])->name('admin.programme.edit');
        Route::put('/{id}/update', [ProgrammeController::class, 'update'])->name('admin.programme.update');
        Route::delete('/{id}', [ProgrammeController::class, 'destroy'])->name('admin.programme.destroy');
        Route::patch('/{id}/toggle-status', [ProgrammeController::class, 'toggleStatus'])->name('programme.toggleStatus');
        Route::post('/{programme}/add-coordinator', [ProgrammeController::class, 'addCoordinator'])
            ->name('admin.programme.addCoordinator');

        // Admin Side to Remove Coordinator from Programme 
        Route::delete('admin/programme_coordinators/{programme_id}/{coordinator_id}', [ProgrammeController::class, 'destroyCoordinatorsProgramme'])
            ->name('admin.programme_coordinators.destroy');


    });

    // Coordinator Management (Admin Side)
    Route::prefix('coordinators')->group(function () {
        Route::get('/', [CoordinatorController::class, 'index'])->name('admin.coordinators.index');
        Route::get('/create', [CoordinatorController::class, 'create'])->name('admin.coordinators.create');
        Route::post('/store', [CoordinatorController::class, 'store'])->name('admin.coordinators.store');
        Route::get('/{id}', [CoordinatorController::class, 'show'])->name('admin.coordinators.show');
        Route::get('/{id}/edit', [CoordinatorController::class, 'edit'])->name('admin.coordinators.edit');
        Route::put('/{id}', action: [CoordinatorController::class, 'updateCoordinators'])->name('admin.coordinators.update');
        Route::delete('/{id}', [CoordinatorController::class, 'destroy'])->name('admin.coordinators.destroy');


        // Admin Side to Check Assigned Programmes for a Specific Coordinator Programme List 
        Route::get('admin/coordinators/{id}/programmes', [CoordinatorController::class, 'showAssignedProgrammes'])->name('admin.coordinators.programmes');

    });

    //create participant
    Route::get('/create_participant', [AdminParticipantsController::class, 'index'])->name('admin.create_participant');
    Route::post('/create_participant', [AdminParticipantsController::class, 'store'])->name('admin.create_participant.store');
    Route::put('/create_participant/{id}', [AdminParticipantsController::class, 'update'])->name('admin.create_participant.update');



});

    Route::post('/admin/coordinators', [CoordinatorController::class, 'register'])->name('admin.coordinators.register');

    // ==========================
    // Coordinator Routes (Self-Management)
    // ==========================

Route::prefix('coordinators')->group(function () {
    // Login & Logout
    Route::get('/login', [CoordinatorController::class, 'showLoginForm'])->name('coordinators.login');
    Route::post('/login', [CoordinatorController::class, 'login'])->name('coordinator.login.post');
    Route::post('/logout', [CoordinatorController::class, 'logout'])->name('coordinators.logout');

    //Dashboard & Programmes
    Route::get('/dashboard', [CoordinatorController::class, 'Dashboard'])->name('coordinators.dashboard');
    Route::get('/programmes', [CoordinatorController::class, 'showProgrammes'])->name('coordinators.programmes');



    // Profile Management
    Route::get('/profile', [CoordinatorController::class, 'profile'])->name('coordinators.profile');
    Route::put('/update/{id}', [CoordinatorController::class, 'update'])->name('coordinator.update');
    Route::post('/store', [CoordinatorController::class, 'store'])->name('coordinator.store');

    // Password Reset Flow
    Route::post('/send-reset-link', [CoordinatorController::class, 'sendResetLink'])->name('coordinator.send-reset-link');
    Route::get('/reset-password/{token}', [CoordinatorController::class, 'showResetPasswordForm'])->name('coordinator.show-reset-password');
    Route::post('/reset-password/{token}', [CoordinatorController::class, 'setPassword'])->name('coordinator.reset-password');
    Route::post('/reset-password-request', [CoordinatorController::class, 'resetPasswordRequest'])
        ->name('coordinator.reset_password_request');
});

// ==========================
// Programme Registration, Payment, Transport, and PDF
// ==========================

// Display available programmes
Route::get('/programmes', [ParticipantsController::class, 'showProgramme'])->name('programme.list');

// Register or update participant details & registration
Route::post('/programme/register/{programme_id}', [ParticipantsController::class, 'registerForProgramme'])->name('programme.register');


Route::get('/generate-pdf/{programme_id}/{action?}', [ParticipantsController::class, 'generatePDF'])
    ->name('generate.pdf');


// ==========================
// Registration Route
// ==========================

Route::get('/admin/participants', [AdminController::class, 'showParticipants'])->name('admin.participants');
Route::get('/coordinators/participants', [CoordinatorController::class, 'fetchRegisteredParticipants'])
    ->name('coordinators.participants');

// Participants Management
Route::get('/admin/participants', [AdminController::class, 'showParticipants'])->name('admin.participants');

Route::post('/admin/verify-response/{id}', [AdminParticipantsController::class, 'verifyResponse'])->name('admin.verifyResponse');
