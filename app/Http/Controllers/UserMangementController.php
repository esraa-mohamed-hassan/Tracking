<?php

namespace App\Http\Controllers;

use App\Logs;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Auth;

class UserMangementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        try {
            $data = User::all();
            return view('user_mangemant')->with('data', $data);
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'User Management Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function AddUser(Request $request)
    {
        return view('add_user');
    }

    public function StoreUser(Request $request)
    {
        try {
            $validation = Validator::make(
                $request->all(),
                [
                    'name' => 'required|min:3|max:150|regex:/^[A-Za-z\-\s]+$/',
                    'email' => 'email|required|unique:users',
                    'password' => 'required|string|min:7|regex:/^(?=.*?[a-z])(?=.*?[0-9]).{7,}$/',
                ]
            );

            if ($validation->fails()) {
                return redirect('add_user')->withErrors($validation)->withInput();
            }

            $name = $request->input('name');
            $email = $request->input('email');
            $password = Hash::make($request->input('password'));
            $user = new User();
            $user->name = $name;
            $user->email = $email;
            $user->password = $password;
            $user->role = 'user';
            $res = $user->save();
            if ($res) {
                return redirect('/user_management')->with('success', 'New User added successfully');;
            }
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'StoreUser Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function EditUser(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $data = User::find($user_id);
            return view('edit_user')->with('data', $data);
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'EditUser Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function UpdateUser(Request $request)
    {
        try {
            $user_id = $request->input('user_id');
            $name = $request->input('name');
            $email = $request->input('email');
            $password = $request->input('password');

            $validation = Validator::make(
                $request->all(),
                [
                    'name' => 'required|min:3|max:150|regex:/^[A-Za-z\-\s]+$/',
                    'email' => 'email|required|unique:users',
                ]
            );

            if ($validation->fails()) {
                if (array_key_exists("email", $validation->errors()->messages())) {
                    $get_user = User::where('email', $email)->first();
                    if ($get_user->id != $user_id) {
                        return redirect('edit_user?user_id=' . $user_id)->withErrors($validation)->withInput();
                    }
                } else {
                    return redirect('edit_user?user_id=' . $user_id)->withErrors($validation)->withInput();
                }
            }


            if (!empty($password)) {
                $data = [
                    'name' => $name,
                    'email' => $email,
                    'password' =>  Hash::make($password),
                ];
            } else {
                $data = [
                    'name' => $name,
                    'email' => $email,
                ];
            }

            $res = User::Where('id', $user_id)->update($data);
            if ($res) {
                return redirect('/user_management')->with('success', 'User updated successfully');
            }
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdateUser Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function destroy($user_id)
    {
        try {
            User::where('id', $user_id)->firstorfail()->delete();
            return response()->json(array('success' => true));
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'destroy User Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function profile()
    {
        try {
            $user_id = Auth::id();
            $data = User::find($user_id);
            return view('user_profile')->with('data', $data);
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'EditUser Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }

    public function UpdateUserProfile(Request $request)
    {
        try {
            $user_id = Auth::id();
            $name = $request->input('name');
            $password = $request->input('password');

            $validation = Validator::make(
                $request->all(),
                [
                    'name' => 'required|min:3|max:150|regex:/^[A-Za-z\-\s]+$/',
                ]
            );

            if ($validation->fails()) {
                return redirect('edit_user?user_id=' . $user_id)->withErrors($validation)->withInput();
            }


            if (!empty($password)) {
                $data = [
                    'name' => $name,
                    'password' =>  Hash::make($password),
                ];
            } else {
                $data = [
                    'name' => $name,
                ];
            }

            $res = User::Where('id', $user_id)->update($data);
            if ($res) {
                $data = User::find($user_id);
                return redirect('profile')->with('success', 'Your Profile updated successfully');
            }
        } catch (\Exception $e) {
            $log = new Logs();
            $log->logdata(__LINE__, __FILE__, __DIR__, __FUNCTION__, __CLASS__, __TRAIT__, __METHOD__, __NAMESPACE__, 'UpdateUser Error', var_export($e->getMessage(), TRUE), 'Error');
            $return = [
                'status' => 'error',
                'message' => $e->getMessage(),
            ];
            return $return;
        }
    }
}