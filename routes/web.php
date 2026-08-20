<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MilestoneController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TeamMemberController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\ZoneController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadStoreController;
use App\Http\Controllers\PropertyPublicController;
use Illuminate\Support\Facades\Route;

/* ---------- Público ---------- */
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/propiedad/{property:slug}', [PropertyPublicController::class, 'show'])->name('property.show');
Route::post('/lead', [LeadStoreController::class, 'store'])->name('lead.store');

/* ---------- Auth ---------- */
Route::middleware('guest')->group(function () {
    Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/admin/login', [AuthController::class, 'login'])->name('login.attempt');
});
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/* ---------- Panel Admin ---------- */
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('properties', PropertyController::class)->except('show');
    Route::post('properties/{property}/images', [PropertyController::class, 'uploadImages'])->name('properties.images.store');
    Route::patch('properties/{property}/featured', [PropertyController::class, 'toggleFeatured'])->name('properties.featured');
    Route::patch('images/{image}', [PropertyController::class, 'setCover'])->name('images.cover');
    Route::delete('images/{image}', [PropertyController::class, 'destroyImage'])->name('images.destroy');

    Route::resource('zones', ZoneController::class)->except('show');
    Route::resource('testimonials', TestimonialController::class)->except('show');
    Route::resource('team', TeamMemberController::class)->except('show');
    Route::resource('milestones', MilestoneController::class)->except('show');
    Route::resource('faqs', FaqController::class)->except('show');

    Route::get('leads', [LeadController::class, 'index'])->name('leads.index');
    Route::patch('leads/{lead}/read', [LeadController::class, 'toggleRead'])->name('leads.read');
    Route::delete('leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy');

    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});