<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserLoginRequest;
use App\Http\Requests\UserRegisterRequest;
use App\Http\Requests\UserUpdateRequest;
use Illuminate\Http\Exceptions\HttpResponseException;


class UserController extends Controller
{
    public function register(UserRegisterRequest $request) : JsonResponse
    {
        $data = $request->validated();

        if(User::where('username', $data['username'])->count() == 1){
            throw new HttpResponseException(response([
                'errors' => ['username' => 'Username already registered.']
            ], 400));
        }
        $user = new User();
        $user->username = $data['username'];
        $user->name = $data['name'];
        $user->email = $data['email']; // ✅ Tambahkan ini
        $user->password = Hash::make($data['password']); // ✅ Hash password
        $user->save();


        return (new UserResource($user))->response()->setStatusCode(201);
    }

    public function login(UserLoginRequest $request) : JsonResponse
    {
        $data = $request->validated();

        $user = User::where('username', $data['username'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            throw new HttpResponseException(response([
                'errors' => ['username' => 'Invalid username or password.']
            ], 401));
        }

        // Generate token (this is just an example, use a proper method in production)
        $user->token = Str::uuid()->toString();
        $user->save();

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function get(Request $request)
    {
        $user = Auth::user();
        return new UserResource($user);
    }

    public function update(UserUpdateRequest $request)
    {
        $data = $request->validated();
        $user = Auth::user();

        if (isset($data['name'])) {
            $user->name = $data['name'];
        }

        if (isset($data['password'])) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return (new UserResource($user))->response()->setStatusCode(200);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        $user->token = null; // Invalidate the token
        $user->save();

        return response()->json(['data' => true]);
    }
}
