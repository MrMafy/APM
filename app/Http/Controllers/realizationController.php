<?php

namespace App\Http\Controllers;

use App\Models\RegEOB;
use App\Models\RegNHRS;
use App\Models\RegOther;
use App\Models\RegSInteg;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use App\Models\Projects;
use App\Models\BasicReference;
use App\Models\BasicInfo;
use App\Models\workGroup;
use Illuminate\Support\Facades\Auth;


class realizationController extends Controller
{
    // переход на страницу создание реализации
    public function create()
    {
        $user = Auth::user();
        return view('add-realization', compact('user'));
    }

    // Отображение данных из расчета
    public function showDataCalculation($id)
    {
        $project = Projects::with('equipment', 'expenses', 'totals', 'markups', 'contacts', 'risks')->find($id);
        $user = Auth::user();
        return view('add-realization', compact('project', 'user'));
    }

    // сохранение данных
    public function storeNew($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        $num = $project->getOriginal('projNum');

        // базовая справка
        $BasicReference = new BasicReference;
            $BasicReference->project_num = $project->projNum;
            $BasicReference->projName = $request->projName;
            $BasicReference->projCustomer = $project->endCustomer;
            $BasicReference->startDate = $request->startDate;
            $BasicReference->endDate = $request->endDate;
            $BasicReference->projGoal = $request->projGoal;
            $BasicReference->projCurator = $request->projCurator;
            $BasicReference->projManager = $request->projManager;
            $BasicReference->linkPlan = $request->linkPlan;
            $BasicReference->payment = $request->payment;
            $BasicReference->save();

            $project->payment = $request->payment;
            $project->save();

        // доп информация
        $BasicInfo = new BasicInfo;
            $BasicInfo->project_num = $project->projNum;
            $BasicInfo->contractor = $request->contractor;
            $BasicInfo->contract_num = $request->contract_num;
            // $BasicInfo->price_plan = $request->price_plan;
            $BasicInfo->price_plan = $project->totals->first()->price;
            $BasicInfo->price_fact = $request->price_fact;
            $BasicInfo->contract_price = $request->contract_price;
            // расчет полей
            $contract_price = floatval($request->contract_price);
            $price_plan = floatval($request->price_plan);
            $price_fact = floatval($request->price_fact);
            // нахождения поля profit_plan(прибыль план) путем разницы стоим.контракта и себестоимость план
            $profit_plan = ($contract_price - $price_plan);
            // нахождения поля profit_fact(прибыль факт) путем разницы стоим.контракта и себестоимость факт
            $profit_fact = ($contract_price - $price_fact);
            // вывод расчитанных полей
            $BasicInfo->profit_plan = $profit_plan;
            $BasicInfo->profit_fact = $profit_fact;

            $BasicInfo->start_date = $request->start_date;
            $BasicInfo->end_date_plan = $request->end_date_plan;

            $BasicInfo->end_date_fact = $request->end_date_fact;
            $BasicInfo->complaint = $request->complaint;
            $BasicInfo->save();
        // Состав рабочей группы и ответственность
        $workGroup = new workGroup;
            $workGroup->project_num = $project->projNum;
            $workGroup->projCurator = $request->projCurator2;
            $workGroup->projDirector = $request->projDirector;
            $workGroup->techlid = $request->techlid;
            $workGroup->production = $request->production;
            $workGroup->supply = $request->supply;
            $workGroup->logistics = $request->logistics;
            $workGroup->save();


        switch ($project->projNumSuf) {
            case 'Группа 1':
                RegSInteg::where('vnNum', $num)->update(['proj_cost' => $request->price_fact, 'profit' => $profit_fact,
                    'date_start' => $request->start_date, 'date_end' => $request->end_date_fact, 'payment' => $request->payment]);
                break;
            case 'Группа 2':
                RegEOB::where('vnNum', $num)->update(['proj_cost' => $request->price_fact, 'profit' => $profit_fact,
                    'date_start' => $request->start_date, 'date_end' => $request->end_date_fact, 'payment' => $request->payment]);
                break;
            case 'Группа 3':
                RegNHRS::where('vnNum', $num)->update(['proj_cost' => $request->price_fact, 'profit' => $profit_fact,
                    'date_start' => $request->start_date, 'date_end' => $request->end_date_fact, 'payment' => $request->payment]);
                break;
            case 'Группа 4':
                RegOther::where('vnNum', $num)->update(['proj_cost' => $request->price_fact, 'profit' => $profit_fact,
                    'date_start' => $request->start_date, 'date_end' => $request->end_date_fact, 'payment' => $request->payment]);
                break;
            default:
                // Обработка, если тип не определен
                break;
        }

       return redirect()->route('project-data-one', $id)->with('success');
        }
}
