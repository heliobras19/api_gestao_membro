<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $user = User::where('email', \request('email'))->first();
        if ($user && !$user->ativo) {
            return response()->json(["conta temporariamente suspensa"]);
        }
        $credentials = request(['email', 'password']);

        $token = auth()->attempt($credentials);
        if ($token === false) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        return $this->respondWithToken($token);
    }

    public function register(Request $request)
    {
        if (!auth()->user()->admin) {
            return response()->json(["msg" => "voce  não tem autorização para criar contas"], 403);
        }
        $request->validate([
            'name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
            'scope' => 'required',
            'abrangencia' => 'nullable',
            'admin' => 'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'scope' => $request->scope,
            'admin' => $request->admin,
            'abrangencia' => $request->abrangencia ?? 'nacional'
        ]);
        return response()->json($user);
    }

    public function updateUser($user, Request $request)
    {
        $user = User::find($user);
        if ($request->password_old || $request->password_old != null) {
            if ($user->password == Hash::make($request->password_old)) {
                $request->merge([
                    "password" => Hash::make($request->new_password)
                ]); 
            }
            
        } elseif (isset($request->password) && ($request->password == null || $request->password == '')) {
           $request->replace($request->except('password'));
        }
        $user->update($request->all());
    }

    public function destaivarConta(User $user)
    {
        $user->ativo = false;
        $user->save();
        return response()->json(["conta desativada com sucesso"]);
    }

    public function ativarConta(User $user)
    {
        $user->ativo = true;
        $user->save();
        return response()->json(["conta ativada com sucesso"]);
    }

    public function listUser()
    {
        $user = User::query();
        if (\request()->get('abrangencia')) {
            $user->where('abrangencia', \request()->get('abrangencia'));
        }
        return response()->json($user->get());
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    public function updateMe(Request $request) {
        $user = User::find(auth()->user()->id);
        $data = null;
        if (isset($request->senha) && $request->senha != null) {
            if (Hash::make($request->senha) == $user->password) {
                $data = [
                    "name" => $request->name,
                    "email" => $request->email,
                    "password" => Hash::make($request->new_password)
                ];
            } else {
                return response()->json(["msg" => "senha errada"], 403);
            }
        }
        $user->update($data ?? $request->all());
        return response()->json([$user]);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();
        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }
}
