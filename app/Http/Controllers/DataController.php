<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegSInteg;
use App\Models\RegEOB;
use App\Models\RegNHRS;
use App\Models\RegOther;
use App\Models\UserGroup;
use App\Models\Group;

class DataController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
//        $RegSInteg = [];
//        $RegEOB = [];
//        $RegNHRS = [];
//        $RegOther = [];
        $UserGroup = UserGroup::all();
        $Group = Group::all();
        $RegSInteg = RegSInteg::all();
        $RegEOB = RegEOB::all();
        $RegNHRS = RegNHRS::all();
        $RegOther = RegOther::all();

        // Проверка роли пользователя и его группы
//        if ($user->role === 'admin') {
//            $RegSInteg = RegSInteg::all();
//            $RegEOB = RegEOB::all();
//            $RegNHRS = RegNHRS::all();
//            $RegOther = RegOther::all();
//        } elseif ($user->role === 'proj_manager') {
//            if ($user->group_num === 'Группа 1') {
//                $RegSInteg = RegSInteg::where('projectManager', $user->name)->get();
//            } elseif ($user->group_num === 'Группа 2') {
//                $RegEOB = RegEOB::where('projectManager', $user->name)->get();
//            } elseif ($user->group_num === 'Группа 3') {
//                $RegNHRS = RegNHRS::where('projectManager', $user->name)->get();
//            } elseif ($user->group_num === 'Группа 4') {
//                $RegOther = RegOther::where('projectManager', $user->name)->get();
//            }
//        } elseif ($user->role === 'responsible') {
//            if ($user->group_num === 'Группа 1') {
//                $RegSInteg = RegSInteg::all();
//            } elseif ($user->group_num === 'Группа 2') {
//                $RegEOB = RegEOB::all();
//            } elseif ($user->group_num === 'Группа 3') {
//                $RegNHRS = RegNHRS::all();
//            } elseif ($user->group_num === 'Группа 4') {
//                $RegOther = RegOther::all();
//            }
//        }

        return view('home', compact('UserGroup','Group', 'RegSInteg', 'RegEOB', 'RegNHRS', 'RegOther'))->with('user', $user);
    }

    public function getData_group_1(Request $request)
    {
        $user = $request->user();
        $RegSInteg = [];

        if ($user->role === 'proj_manager') {
            $RegSInteg = RegSInteg::where('projectManager', $user->name)->get();
        } else {
            $RegSInteg = RegSInteg::all();
        }

        // Возвращаем данные в формате JSON
        return response()->json($RegSInteg);
    }

    public function getData_group_2(Request $request)
    {
        $user = $request->user();
        $RegEOB = [];

        if ($user->role === 'proj_manager') {
            $RegEOB = RegEOB::where('projectManager', $user->name)->get();
        } else {
            $RegEOB = RegEOB::all();
        }

        // Возвращаем данные в формате JSON
        return response()->json($RegEOB);
    }

    public function getData_group_3(Request $request)
    {
        $user = $request->user();
        $RegNHRS = [];

        if ($user->role === 'proj_manager') {
            $RegNHRS = RegNHRS::where('projectManager', $user->name)->get();
        } else {
            $RegNHRS = RegNHRS::all();
        }

        // Возвращаем данные в формате JSON
        return response()->json($RegNHRS);
    }

    public function getData_group_4(Request $request)
    {
        $user = $request->user();
        $RegOther = [];

        if ($user->role === 'proj_manager') {
            $RegOther = RegOther::where('projectManager', $user->name)->get();
        } else {
            $RegOther = RegOther::all();
        }

        // Возвращаем данные в формате JSON
        return response()->json($RegOther);
    }

}
