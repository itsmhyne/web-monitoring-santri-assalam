<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

    /** login */
    public function login(Request $request)
    {

        $validasi = Validator::make($request->all(), [
            'email' => 'required',
            'password' => 'required|min:6'
        ]);

        if (!$request->email) {
            return $this->error($validasi->errors()->first());
        }

        // ambil data user berdasarkan email yang dimasukkan
        $user = User::where('email', $request->email)->first();

        if ($user) {

            // cek apakah password sama sesuai dengan email yang dimasukkan
            if (\password_verify($request->password, $user->password)) {
                return $this->success($user);
            } else {
                return $this->error('Password Salah');
            }
        }
        return $this->error('User tidak ditemukan');
    }
    /** end login */

    /**register */
    public function register(Request $request)
    {
        $validasi = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users',
            'password' => 'required|min:6'
        ]);

        if ($validasi->fails()) {
            return $this->error($validasi->errors()->first());
        }

        // proses registrasi dan becrypt password
        $user = User::create(\array_merge($request->all(), [
            'password' => \bcrypt($request->password)
        ]));

        if ($user) {
            return $this->success($user, 'Selamat Datang' . $user->name);
        } else {
            return $this->error('Terjadi Kesalahan');
        }
    }
    /**end register */


    /** template notifikasi */
    public function error($message)
    {
        return response()->json([
            'code' => 400,
            'message' => $message
        ], 400);
    }

    public function success($data, $message = "success")
    {
        return response()->json([
            'code' => 200,
            'message' => $message,
            'data' => $data
        ], 200);
    }
    /** end template notifikasi */
}


/**
 * Copyright
 * Created By : M. Hamdan Yusuf
 * Github : MHYID
 * Location File : APP/Http/Controllers/Api/AuthController.php
 */
