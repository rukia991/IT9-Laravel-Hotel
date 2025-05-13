<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FacilityController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\NotificationsController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\RoomController;
use App\Http\Controllers\RoomStatusController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\TransactionRoomReservationController;
use App\Http\Controllers\TypeController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\BookingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Routes for Super Admin
Route::group(['middleware' => ['auth', 'checkRole:Super']], function () {
    Route::resource('user', UserController::class);
    Route::get('/rooms', [RoomController::class, 'show'])->name('admin.rooms.show');
});

// Routes for Super Admin and Admin
Route::group(['middleware' => ['auth', 'checkRole:Super,Admin']], function () {
    Route::post('/room/{room}/image/upload', [ImageController::class, 'store'])->name('image.store');
    Route::delete('/image/{image}', [ImageController::class, 'destroy'])->name('image.destroy');

    Route::name('transaction.reservation.')->group(function () {
        Route::get('/createIdentity', [TransactionRoomReservationController::class, 'createIdentity'])->name('createIdentity');
        Route::get('/pickFromCustomer', [TransactionRoomReservationController::class, 'pickFromCustomer'])->name('pickFromCustomer');
        Route::post('/storeCustomer', [TransactionRoomReservationController::class, 'storeCustomer'])->name('storeCustomer');
        Route::get('/{customer}/viewCountPerson', [TransactionRoomReservationController::class, 'viewCountPerson'])->name('viewCountPerson');
        Route::get('/{customer}/chooseRoom', [TransactionRoomReservationController::class, 'chooseRoom'])->name('chooseRoom');
        Route::get('/{customer}/{room}/{from}/{to}/confirmation', [TransactionRoomReservationController::class, 'confirmation'])->name('confirmation');
        Route::post('/{customer}/{room}/payDownPayment', [TransactionRoomReservationController::class, 'payDownPayment'])->name('payDownPayment');
    });

    Route::resource('customer', CustomerController::class);
    Route::resource('type', TypeController::class);

    Route::resource('room', RoomController::class);
    Route::get('/room/{room}', [RoomController::class, 'show'])->name('room.details');

    Route::resource('roomstatus', RoomStatusController::class);
    Route::resource('transaction', TransactionController::class);
    Route::resource('facility', FacilityController::class);

    Route::get('/payment', [PaymentController::class, 'index'])->name('payment.index');
    Route::get('/payment/{payment}/invoice', [PaymentController::class, 'invoice'])->name('payment.invoice');

    Route::get('/transaction/{transaction}/payment/create', [PaymentController::class, 'create'])->name('transaction.payment.create');
    Route::post('/transaction/{transaction}/payment/store', [PaymentController::class, 'store'])->name('transaction.payment.store');

    Route::get('/get-dialy-guest-chart-data', [ChartController::class, 'dailyGuestPerMonth']);
    Route::get('/get-dialy-guest/{year}/{month}/{day}', [ChartController::class, 'dailyGuest'])->name('chart.dailyGuest');
});

// Routes for Super Admin, Admin, and Customer
Route::group(['middleware' => ['auth', 'checkRole:Super,Admin,Customer']], function () {
    Route::get('/activity-log', [ActivityController::class, 'index'])->name('activity-log.index');
    Route::get('/activity-log/all', [ActivityController::class, 'all'])->name('activity-log.all');
    Route::resource('user', UserController::class)->only(['show']);
    Route::get('/customer/reservations', [CustomerController::class, 'reservedRooms'])->name('customer.reservations');

    Route::view('/notification', 'notification.index')->name('notification.index');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/mark-all-as-read', [NotificationsController::class, 'markAllAsRead'])->name('notification.markAllAsRead');

    Route::get('/notification-to/{id}', [NotificationsController::class, 'routeTo'])->name('notification.routeTo');

    Route::post('/booking', [BookingController::class, 'store'])->name('booking.store');
});

// Routes for Receptionist
Route::group(['middleware' => ['auth', 'checkRole:Receptionist']], function () {
    // Dashboard
    Route::get('/receptionist', function () {
        return view('receptionist.index');
    })->name('receptionist.index');

    // Reservations
    Route::get('/receptionist/reservations', [TransactionController::class, 'receptionistReservations'])
        ->name('receptionist.reservations');
    Route::get('/receptionist/reservations/{transaction}/approve', [TransactionController::class, 'approveReservation'])
        ->name('receptionist.reservations.approve');
    Route::get('/receptionist/reservations/{transaction}/reject', [TransactionController::class, 'rejectReservation'])
        ->name('receptionist.reservations.reject');
    Route::get('/receptionist/reservations/{transaction}', [TransactionController::class, 'reservationDetails'])
        ->name('receptionist.reservation-details');

    // Rooms
    Route::get('/receptionist/rooms', [RoomController::class, 'receptionistRooms'])
        ->name('receptionist.rooms');
    Route::get('/receptionist/new-reservation', [TransactionRoomReservationController::class, 'createReservation'])
        ->name('receptionist.new-reservation');

    // Payments
    Route::get('/receptionist/payments', [PaymentController::class, 'receptionistPayments'])
        ->name('receptionist.payments');
    Route::get('/receptionist/process-payment/{transaction}', [PaymentController::class, 'processPayment'])
        ->name('receptionist.process-payment');

    // Check-in/out
    Route::get('/receptionist/check-in', [TransactionController::class, 'checkInList'])
        ->name('receptionist.check-in');
});

// Routes for Manager
Route::group(['middleware' => ['auth', 'checkRole:Manager']], function () {
    Route::get('/manager', function () {
        return view('manager.index'); // Correct path to the manager view
    })->name('manager.index');
});

// Login routes
Route::view('/login', 'auth.login')->name('login.index');
Route::post('/login', [AuthController::class, 'login'])->name('login');

// For Customers and Guest Access
Route::get('/customer', [RoomController::class, 'index'])->name('customer.index');
Route::get('/customer/room/{room}', [RoomController::class, 'show'])->name('customer.room.show');

// Forgot Password routes
Route::group(['middleware' => 'guest'], function () {
    Route::get('/forgot-password', fn() => view('auth.passwords.email'))->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword'])->name('password.email');

    // Reset Password routes
    Route::get('/reset-password/{token}', fn(string $token) => view('auth.reset-password', ['token' => $token]))
        ->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

// Register routes
Route::group(['middleware' => 'guest'], function () {
    Route::view('/register', 'auth.register')->name('register.index');
    Route::post('/register', [AuthController::class, 'register'])->name('register');
});

// Home route
Route::get('/', [HomeController::class, 'index'])->name('home');