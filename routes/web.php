<?php

use App\Http\Controllers\AcademicYearController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\ClassController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\HistoryController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SPPRateController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('auth.login');
});


Route::post('/login', [App\Http\Controllers\AuthController::class, 'login'])->name('login.process');
Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    Route::get('/student/payments/history', [HistoryController::class, 'index'])->name('payments.history');
    Route::get('/student/payments/history/{id}', [HistoryController::class, 'show'])->name('payments.history.show');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dasboard/admin', [DashboardController::class, 'admin'])->name('admin.dashboard');
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::post('/users/store', [UserController::class, 'store'])->name('users.store');
    Route::post('/users/update/{id}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/delete/{id}', [UserController::class, 'destroy'])->name('users.delete');

    Route::get('/academic-years', [AcademicYearController::class, 'index'])->name('academic-years.index');
    Route::post('/academic-years/store', [AcademicYearController::class, 'store'])->name('academic-years.store');
    Route::post('/academic-years/update/{id}', [AcademicYearController::class, 'update'])->name('academic-years.update');
    Route::delete('/academic-years/delete/{id}', [AcademicYearController::class, 'destroy'])->name('academic-years.delete');
    Route::post('/set-active/{id}', [AcademicYearController::class, 'setActive'])->name('academic-years.set-active');
    Route::patch('/academic-years/{id}/toggle', [AcademicYearController::class, 'toggleActive'])->name('academic-years.toggle');

    Route::get('spp-rates', [SPPRateController::class, 'index'])->name('spp.index');
    Route::post('spp-rates/store', [SPPRateController::class, 'store'])->name('spp.store');
    Route::post('spp-rates/update/{id}', [SPPRateController::class, 'update'])->name('spp.update');
    Route::delete('spp-rates/delete/{id}', [SPPRateController::class, 'destroy'])->name('spp.delete');

    Route::get('/classes', [ClassController::class, 'index'])->name('classes.index');
    Route::post('/store', [ClassController::class, 'store'])->name('classes.store');
    Route::post('/update/{id}', [ClassController::class, 'update'])->name('classes.update');
    Route::delete('/delete/{id}', [ClassController::class, 'destroy'])->name('classes.delete');
    Route::get('/classes/{id}/students', [ClassController::class, 'students'])->name('classes.students');

    Route::get('/students', [StudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [StudentController::class, 'create'])->name('students.create');
    Route::post('/students/store', [StudentController::class, 'store'])->name('students.store');
    Route::get('/students/edit/{id}', [StudentController::class, 'edit'])->name('students.edit');
    Route::put('students/update/{id}', [StudentController::class, 'update'])->name('students.update');
    Route::delete('/students/delete/{id}', [StudentController::class, 'destroy'])->name('students.destroy');
    Route::get('/students/detail/{id}', [StudentController::class, 'detail'])->name('students.detail');
    Route::post('/students/import', [StudentController::class, 'import'])->name('students.import');
    Route::get('/students/data', [StudentController::class, 'data'])->name('students.data');

    Route::get('/bills/students', [BillController::class, 'students'])->name('billing.students');
    Route::post('/bills/generate-all', [BillController::class, 'generateAllBills'])->name('bills.generateAll');
    Route::get('/billing/{studentId}', [BillController::class, 'index'])->name('billing.index');
    Route::get('/billing/show/{studentId}', [BillController::class, 'showDetail'])->name('admin.bills.show');

    Route::get('admin/payments', [PaymentController::class, 'adminIndex'])->name('admin.payments.index');
    Route::get('admin/payments/{id}', [PaymentController::class, 'adminShow'])->name('admin.payments.show');
    Route::post('admin/payments/{id}/approve', [PaymentController::class, 'approve'])->name('admin.payments.approve');
    Route::post('admin/payments/{id}/reject', [PaymentController::class, 'reject'])->name('admin.payments.reject');
});

Route::middleware(['auth', 'siswa'])->group(function () {
    // siswa role
    Route::get('/student/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');


    Route::get('student/payments', [PaymentController::class, 'studentIndex'])->name('student.payments.index');
    Route::get('student/payments/create', [PaymentController::class, 'create'])->name('student.payments.create');
    Route::post('student/payments/store', [PaymentController::class, 'store'])->name('student.payments.store');
    Route::get('student/payments/{id}', [PaymentController::class, 'studentShow'])->name('student.payments.show');
    // upload ulang bukti
    Route::post('/student/payments/{payment}/upload-proof', [PaymentController::class, 'uploadAdditionalProof'])->name('student.payments.upload-proof');
});