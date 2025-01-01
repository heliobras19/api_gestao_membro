<?php

namespace App\Http\Controllers;

use App\Models\User;
use Exception;
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
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        return $this->respondWithToken($token);
    }

    public function password_verify (Request $request) {
        $user = auth()->user();
        $login =auth()->attempt(["email" => auth()->user()->email, "password" => $request->password]);
         if ( $login === false ) {
              return response()->json(["success" => false]);
         }
        return  response()->json(["success" => true]);
    }

    public function onlyUser ($id) {
        try{
             return User::find($id);
        } catch(Exception $err) {
            return response()->json($err->getMessage(), 500);
        }
       
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
            'abragencia' => 'nullable',
            'admin' => 'required'
        ]);
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'scope' => $request->scope,
            'admin' => $request->admin,
            'abragencia' => $request->abrangencia ?? 'NACIONAL'
        ]);
        return response()->json($user);
    }

    public function updateUser($user, Request $request)
    {
        if (!auth()->user()->admin) {
             return response()->json("voce não possui autorização para essa ação", 403);
        }
        try {
            $user = User::find($user);
            if ($request->password_old || $request->password_old != null) {
                if ($user->password == Hash::make($request->password_old)) {
                    $request->merge([
                        "password" => Hash::make($request->new_password)
                    ]); 
                }
                
            } elseif (isset($request->password) && ($request->password == null || $request->password == '')) {
            $request->replace($request->except('password'));
            } elseif ($request->passowrd_force_set) {
                $request->merge([
                        "password" => Hash::make($request->passowrd_force_set)
                ]); 
            }

            if ($request->has('abragencia')) {
                $validAbragenciaValues = ['MUNICIPAL', 'PROVINCIAL', 'NACIONAL'];
                if (!in_array($request->abragencia, $validAbragenciaValues)) {
                    return response()->json(['message' => 'Invalid abragencia value'], 400);
                }
            }
            $filteredData = array_filter($request->all(), function($value) {
                return $value !== null && $value !== '';
            });
            $user->update($filteredData);
            return response()->json([$user]);
        } catch (Exception $th) {
            return response()->json($th->getMessage());
        }
        
    }

    public function desativarConta(User $user)
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
        if (!auth()->user()->admin) {
            return response()->json(["msg" => "voce  não tem autorização para ver contas"], 403);
        }
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
