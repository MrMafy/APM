<?php

namespace App\Http\Controllers;

use App\Models\Group;
use App\Models\RegReestrKP;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Projects;
use App\Models\Equipment;
use App\Models\Expenses;
use App\Models\AdditionalExpense;
use App\Models\Note;
use App\Models\Total;
use App\Models\Markup;
use App\Models\contacts;
use App\Models\Risks;
use App\Models\BasicInfo;
use App\Models\BasicReference;
use App\Models\workGroup;
use App\Models\Change;
use App\Models\CalcRisk;
use App\Models\baseRisks;
use App\Models\ProjectManager;

use App\Models\RegEOB;
use App\Models\RegNHRS;
use App\Models\RegOther;
use App\Models\RegSInteg;
use App\Models\User;
use App\Models\UserGroup;

use PhpOffice\PhpWord\TemplateProcessor;

use Illuminate\Support\Facades\DB;
use Illuminate\Pagination\Paginator;

class ProjectController extends Controller
{
    // Отображение списка всех проектов на странице карты проекта
//    public function allData(Request $request)
//    {
//        $user = $request->user();
//        $projects = Projects::all();
//
////        if ($user->role === 'admin') {
////            $projects = Projects::all();
////        } elseif ($user->role === 'proj_manager') {
////            if ($user->group_num === 'Группа 1') {
////                $projects = Projects::where('projManager', $user->name)->get();
////            } elseif ($user->group_num === 'Группа 2') {
////                $projects = Projects::where('projManager', $user->name)->get();
////            } elseif ($user->group_num === 'Группа 3') {
////                $projects = Projects::where('projManager', $user->name)->get();
////            } elseif ($user->group_num === 'Группа 4') {
////                $projects = Projects::where('projManager', $user->name)->get();
////            }
////
////        } elseif ($user->role === 'responsible') {
////            if ($user->group_num === 'Группа 1') {
////                $projects = Projects::where('projNumSuf', $user->group_num)->get();
////            } elseif ($user->group_num === 'Группа 2') {
////                $projects = Projects::where('projNumSuf', $user->group_num)->get();
////            } elseif ($user->group_num === 'Группа 3') {
////                $projects = Projects::where('projNumSuf', $user->group_num)->get();
////            } elseif ($user->group_num === 'Группа 4') {
////                $projects = Projects::where('projNumSuf', $user->group_num)->get();
////            }
////
////        }
//        return view('all-maps', ['data' => $projects, 'user' => $user]);
//
//    }

    public function allData(Request $request)
    {
        $user = $request->user();
        $projects = Projects::all();
        $groups = Group::all();
        $projectManagers = ProjectManager::all();

        return view('all-maps', [
            'data' => $projects,
            'user' => $user,
            'groups' => $groups,
            'projectManagers' => $projectManagers
        ]);
    }

    public function filterProjects(Request $request)
    {
        $group = $request->input('group');
        $manager = $request->input('manager');
        $sortOrder = $request->input('sortOrder', 'asc');

        $query = Projects::query();

        if ($group) {
            $query->where('projNumSuf', $group);
        }

        if ($manager) {
            $query->where('projManager', $manager);
        }

        if ($sortOrder) {
            $query->orderBy('date_application', $sortOrder);
        }

        $projects = $query->get();

        return view('all-maps', [
            'data' => $projects,
            'user' => $request->user(),
            'groups' => Group::all(),
            'projectManagers' => ProjectManager::all()
        ]);
    }



    // Метод для получения ID проекта по vnNum
    public function getProjectIdByVnNum($vnNum)
    {
        $project = Projects::where('projNum', $vnNum)->first(); // Используем Eloquent для поиска проекта по projNum
        return $project ? $project->id : null; // Возвращаем ID проекта, если найден, иначе null
    }
    public function getAllProjects()
    {
        $projects = Projects::select('id', 'projNum')->get();
        return response()->json($projects);
    }


    // Отображение одного проекта и связанных данных (отображает данные по id) на странице карты проекта
    public function showOneMessage($id, $tab = null)
    {
        $project = Projects::with('equipment', 'expenses', 'totals', 'contacts', 'risks', 'workGroup', 'basicReference', 'basicInfo', 'notes')->find($id);
        $notes = $project->notes()->paginate(3);
        $user = auth()->user();
        $users = User::all(); // Получить всех пользователей
        $groups = UserGroup::all();
        if (!$project) {
            abort(404, 'Project not found');
        }

//        $notes = $project->notes;
        $baseRisks = baseRisks::all();

        $defaultTab = 'calculation';

        if ($tab === null) {
            $tab = $defaultTab;
        }

        if (view()->exists("tables.{$tab}-projectMap")) {
            return view('project-map', compact('baseRisks', 'project', 'tab', 'user', 'users', 'groups'));
        } else {
            abort(404, 'Tab not found');
        }
    }

    public function updateNote(Request $request, $id)
    {
        $project = Projects::find($id);

        if (!$project) {
            abort(404, 'Project not found');
        }

        $project->proj_note = $request->input('value');
        $project->save();

        return response()->json(['message' => 'Note updated successfully']);
    }



    // удаление карты проекта (НЕАКТУАЛЬНО )
    public function deleteMessage($id)
    {
        Projects::find($id)->delete();
        return redirect()->route('project-maps')->with('success', 'сообщение было удалено');
    }


    //метод для сохранения новой записи в ДНЕВНИК
    public function store(Request $request, Projects $project)
    {
        $note = new Note;
        $note->comment = $request->comment;
        $note->date = now(); // указываем текущую дату
        $note->project_num = $project->projNum;
        $note->save();
        return redirect()->route('project-data-one', ['id' => $project->id, 'tab' => '#notes'])->with('success', 'Project data successfully updated');
    }

    //метод для удаления записи из дневника
    public function destroy(Projects $project, Note $note)
    {
        if ($project->projNum === $note->project_num) {
            $note->delete();
        }
        return redirect()->route('project-data-one', ['id' => $project->id, 'tab' => '#notes'])->with('success', 'Project data successfully updated');
    }

    public function edit(Projects $project, Note $note)
    {
        return back();
    }

    public function update(Request $request, Projects $project, Note $note)
    {
        $note->update(['comment' => $request->comment]);
        $note->update(['date' => now()]);
        return redirect()->route('project-data-one', ['id' => $project->id, 'tab' => '#notes'])->with('success', 'Project data successfully updated');
    }

    // скачивание дневника
    public function exportNotesWord($id, $projNum)
    {
        // Загружаем данные из базы данных
        $project = Projects::find($id);

        // Проверяем, найден ли проект
        if (!$project) {
            return abort(404); // Вывести ошибку 404, если проект не найден
        }

        // Путь к существующему файлу Word
        $templatePath = storage_path("notes_template.docx");
        $templateProcessor = new TemplateProcessor($templatePath);

        // Получение данных из базы данных
        $notes = DB::table('notes')->where('project_num', $projNum)->get();

        $templateProcessor->cloneRow('date', count($notes));

        // Обход каждой строки данных и добавление значений в соответствующие ячейки
        foreach ($notes as $index => $note) {
            $templateProcessor->setValue('date#' . ($index + 1), $note->date);
            $templateProcessor->setValue('comment#' . ($index + 1), $note->comment);
        }

        $templateProcessor->setValue('projNum', $project->projNum);

        // Сохраняем измененный файл
        $newFilePath = storage_path("notes/дневник {$project->projNum}.docx");
        $templateProcessor->saveAs($newFilePath);


        // Возврат файла для загрузки
        return response()->download($newFilePath)->deleteFileAfterSend();
    }

    // переход на страницу СОЗДАНИЯ КАРТЫ ПРОЕКТА
    public function create()
    {
        $lastProject = Projects::latest('id')->first(); // Получаем последний проект (по наибольшему id)
        if ($lastProject) {
            $lastProjectNum = $lastProject->projNum;
            // Извлекаем номер из строки и увеличиваем его на 1
            $lastNumber = intval(substr($lastProjectNum, 0, strpos($lastProjectNum, '-')));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1; // Если нет проектов, начинаем с 1
        }
        $currentYear = date('y');
        $projectNum = $newNumber;

        $projectManagers = ProjectManager::all();
        $baseRisks = baseRisks::all();
        // Получение group_num пользователя
       // $currentUserGroupNum = Auth::user()->group_num;
        $currentUserGroupNum = Auth::user()->group_name;
        // Получение текущего пользователя
        $user = Auth::user();
        return view('add-map', compact('projectNum', 'currentYear', 'projectManagers', 'baseRisks', 'currentUserGroupNum', 'user'));
    }

    // ДОБАВЛЕНИЕ новой карты проекта
    public function storeNew(Request $request)
    {
        $group = $request->projNumSuf;
        $year = $request->projNumPre;
        $manualProjNum = $request->manualProjNum;

        if (!empty($manualProjNum)) {
            // Если номер введен вручную, используем его
            $projectNumber = $manualProjNum . ' ' . $group;;
        } else {
            // Если номер не введен вручную, формируем его автоматически
            $lastProjectInGroup = Projects::where('projNumSuf', $group)
                ->where('projNumPre', $year)
                ->orderBy('id', 'desc')
                ->first();
            $projectNumberInGroup = ($lastProjectInGroup) ? explode('-', $lastProjectInGroup->projNum)[0] + 1 : 1;
            $projectNumber = $projectNumberInGroup . '-' . $year . ' ' . $group;
        }


        // Создание записи проекта
        $project = new Projects;
        $project->projNumPre = $request->projNumPre;;
        $project->projNum = $projectNumber;
        $project->projNumSuf = $group;

        $project->projManager = $request->projManager;
        $project->proj_note = $request->proj_note;
        $project->objectName = $request->objectName;
        $project->endCustomer = $request->endCustomer;
        $project->contractor = $request->contractor;

        $project->date_application = $request->date_application;
        // $project->date_offer = $request->date_offer;

        $project->delivery = $request->has('delivery') ? 1 : 0;
        $project->pir = $request->has('pir') ? 1 : 0;
        $project->kd = $request->has('kd') ? 1 : 0;
        $project->production = $request->has('production') ? 1 : 0;
        $project->smr = $request->has('smr') ? 1 : 0;
        $project->pnr = $request->has('pnr') ? 1 : 0;
        $project->po = $request->has('po') ? 1 : 0;
        $project->cmr = $request->has('cmr') ? 1 : 0;

        $project->save();

        switch ($request->projNumSuf) {
            case 'Группа 1':
                $this->addToRegistrySinteg($project);
                break;
            case 'Группа 2':
                $this->addToRegistryEob($project);
                break;
            case 'Группа 3':
                $this->addToRegistryNhrs($project);
                break;
            case 'Группа 4':
                $this->addToRegistryOther($project);
                break;
            default:
                // Обработка, если тип не определен
                break;
        }

        // контакт лист
        if ($request->has('contacts')) {
            $data_contacts = array();
            foreach ($request->input('contacts') as $index => $contactsData) {
                $item = array(
                    'project_num' => $project->projNum,
                    'fio' => $contactsData['fio'],
                    'post' => $contactsData['post'],
                    'organization' => $contactsData['organization'],
                    'responsibility' => $contactsData['responsibility'],
                    'phone' => $contactsData['phone'],
                    'email' => $contactsData['email']
                );
                array_push($data_contacts, $item);
            }
            contacts::insert($data_contacts);
        }


       return redirect()->route('project-maps');
    }


    // Метод для получения данных по выбранному риску
    public function getRiskData(Request $request)
    {
        // Получаем выбранный риск из запроса
        $selectedRisk = $request->input('risk');

        $baseRisk = baseRisks::where('nameRisk', $selectedRisk)->first();

        if ($baseRisk) {
            // Возвращаем данные в формате JSON
            return response()->json([
                'reasonData' => json_decode($baseRisk->reasonRisk),
                'consequenceData' => json_decode($baseRisk->conseqRiskOnset),
                'counteringRiskData' => json_decode($baseRisk->counteringRisk),
                'riskManagMeasuresData' => json_decode($baseRisk->riskManagMeasures),
                'term' => $baseRisk->term,
            ]);
        } else {
            // Риск не найден
            return response()->json(['error' => 'Риск не найден'], 404);
        }
    }


    // редактирование карты проекта -> РАСЧЕТ (открыывает страницу редактирования по id записи)
    public function updateCalculation($id)
    {
        $project = Projects::find($id);
        $maxRiskId = CalcRisk::max('id');
        $projectManagers = ProjectManager::all();

        $RegReestrKP = RegReestrKP::all();
        // Получаем все дополнительные файлы для каждого объекта RegReestrKP
        $RegReestrKP->each(function ($regReestrKP) {
            $regReestrKP->additionalFiles = $regReestrKP->additionalFiles()->get();
        });

        if (!$project) {
            return response()->json(['error' => 'Project not found'], 404);
        }

        return view('project-map-update', [
            'project' => $project,
            'maxRiskId' => $maxRiskId,
            'projectManagers' => $projectManagers,
            'RegReestrKP' => $RegReestrKP
        ]);
    }

    // РЕДАКТИРОВАНИЕ данных для карты проекта -> РАСЧЕТ

    public function updateCalculationSubmit($id, Request $req)
    {
        // --------------РАСЧЕТ----------------//
        $user = $req->user();

        // Обновление общая информация по проекту
        $project = Projects::find($id);
        $old_projNum = Projects::find($id)->projNum;

        switch ($project->projNumSuf) {
            case 'Группа 1':
                $this->updateRegistrySinteg($project, $req);
                break;
            case 'Группа 2':
                $this->updateRegistryEob($project, $req);
                break;
            case 'Группа 3':
                $this->updateRegistryNhrs($project, $req);
                break;
            case 'Группа 4':
                $this->updateRegistryOther($project, $req);
                break;
        }

        if ($user->role === 'admin') {
            $project->projNum = $req->input('projNum');

            $project->projNumPre =$req->input('projNumPre');
        }

      //ВЫТАСКИВАНИЕ ГОДА ИЗ НОМЕРА ПРОЕКТА (projNum)
        // Пример строки проекта
        $projNum = $req->input('projNum');
        // Разбиение строки по пробелу
        $parts = explode(' ', $projNum);
        // Получение первого элемента после разделения по пробелу
        $numberPart = $parts[0];
        // Далее, разбиваем эту часть по дефису
        $numberParts = explode('-', $numberPart);
        // Получение второго элемента после разделения по дефису
        $desiredValue = $numberParts[1];

        $project->projNumPre =$desiredValue;
        // $project->projNum = $req->input('projNum');
        $project->proj_note = $req->input('proj_note');
        $project->projManager = $req->input('projManager');
        $project->objectName = $req->input('objectName');
        $project->endCustomer = $req->input('endCustomer');
        $project->contractor = $req->input('contractor');
        $project->date_application = $req->input('date_application');
        $project->date_offer = $req->input('date_offer');

        $project->delivery = $req->has('delivery') ? 1 : 0;
        $project->pir = $req->has('pir') ? 1 : 0;
        $project->kd = $req->has('kd') ? 1 : 0;
        $project->production = $req->has('production') ? 1 : 0;
        $project->smr = $req->has('smr') ? 1 : 0;
        $project->pnr = $req->has('pnr') ? 1 : 0;
        $project->po = $req->has('po') ? 1 : 0;
        $project->cmr = $req->has('cmr') ? 1 : 0;

        $project->save();


        // Обновление оборудования
        if ($req->has('equipment')) {
            $totalPrice = 0;
            foreach ($req->input('equipment') as $equipmentData) {
                // Проверяем наличие идентификатора оборудования
                if (!empty($equipmentData['id'])) {
                    // Если есть идентификатор, ищем соответствующую запись в базе данных и обновляем её
                    $equipment = Equipment::find($equipmentData['id']);
                } else {
                    // Если идентификатор отсутствует, создаем новую запись
                    $equipment = new Equipment();
                }

                // Устанавливаем значение поля project_num
                $equipment->project_num = $project->projNum;

                $count = intval($equipmentData['count']);
                $priceUnit = floatval($equipmentData['priceUnit']);
                $price = $count * $priceUnit; // Расчёт стоимости

                // Заполнение или обновление данных оборудования
                $equipment->fill([
                    'nameTMC' => $equipmentData['nameTMC'],
                    'manufacture' => $equipmentData['manufacture'],
                    'unit' => $equipmentData['unit'],
                    'count' => $equipmentData['count'],
                    'priceUnit' => $equipmentData['priceUnit'],
                    'price' => $price, // запись в бд расчитанной стоимости
                ]);
                $totalPrice += $price;
                $equipment->save();
            }
        }

        // Прочие расходы.
        // Получаем запись расходов, где project_num равен старому значению
        $expenses = Expenses::where('project_num', $old_projNum)->firstOrFail();
        $total = 0;
        // Обработка основных расходов
        foreach ($req->input('expense') as $index => $expenseData) {
            foreach ($expenseData as $key => $value) {
                if ($key !== '_token') { // Пропускаем токен CSRF
                    $total += floatval($value);
                    // Обновляем значение в модели Expenses
                    $expenses->{$key} = $value;
                }
            }
        }
        // Обновление или создание записей о дополнительных расходах
        if ($req->has('additional_expenses')) {
            foreach ($req->input('additional_expenses') as $id => $additionalExpenseData) {
                $additionalExpense = AdditionalExpense::find($id);
                if ($additionalExpense) {
                    // Редактирование существующей записи
                    $additionalExpense->cost = $additionalExpenseData['cost'];
                    $additionalExpense->save();
                    $total += floatval($additionalExpenseData['cost']); // Добавляем стоимость к общей сумме
                } else {
                    // Добавление новой записи
                    $newAdditionalExpense = new AdditionalExpense;
                    $newAdditionalExpense->expense_id = $expenses->id; // Устанавливаем связь с основным расходом
                    $newAdditionalExpense->cost = $additionalExpenseData['cost'];
                    $newAdditionalExpense->save();
                    $total += floatval($additionalExpenseData['cost']); // Добавляем стоимость к общей сумме
                }
            }
        }
        // Обновляем project_num на новое значение
        $expenses->project_num = $project->projNum;
        // Сохранение общей стоимости расходов
        $expenses->total = $total;
        $expenses->save();


        //КСГ
//        $totals = Total::where('project_num', $project->projNum)->first();
        $totals = Total::where('project_num', $old_projNum)->firstOrFail();
        if ($totals) {
            $kdDays = floatval($req->kdDays);
            $equipmentDays = floatval($req->equipmentDays);
            $productionDays = floatval($req->productionDays);
            $shipmentDays = floatval($req->shipmentDays);

            $periodDays = $kdDays + $equipmentDays + $productionDays + $shipmentDays; // Расчет итого
            // нахождения поля price(себестоимость) путем сложения поля всего из таблицы оборудования и всего из проч.расх.
            $priceTotals = ($totalPrice + $total);
            $totals->fill([
                'periodDays' => $periodDays,
                'price' => $priceTotals,
                'kdDays' => $kdDays,
                'equipmentDays' => $equipmentDays,
                'productionDays' => $productionDays,
                'shipmentDays' => $shipmentDays,
            ]);
            $totals->project_num = $project->projNum;
            $totals->save();
        }

        //уровень наценки
        if ($req->has('markup')) {
            Markup::where('project_num', $project->projNum)->delete();
            $data_markups = [];
            foreach ($req->input('markup') as $index => $markupsData) {
                $item = [
                    'project_num' => $project->projNum,
                    'date' => $markupsData['date'],
                    'percentage' => $markupsData['percentage'],
                    'priceSubTkp' => $markupsData['priceSubTkp'],
                    'agreedFio' => $markupsData['agreedFio'],
                ];
                array_push($data_markups, $item);
            }
            Markup::insert($data_markups);
        }

        // контакт лист
        if ($req->has('contact')) {
            Contacts::where('project_num', $old_projNum)->delete();
            $data_contacts = [];
            foreach ($req->input('contact') as $index => $contactsData) {
                $item = [
                    'project_num' => $project->projNum,
                    'fio' => $contactsData['fio'],
                    'post' => $contactsData['post'],
                    'organization' => $contactsData['organization'],
                    'responsibility' => $contactsData['responsibility'],
                    'phone' => $contactsData['phone'],
                    'email' => $contactsData['email'],
                ];
                array_push($data_contacts, $item);
            }
            Contacts::insert($data_contacts);
        }
        // риски
        if ($req->has('risk')) {
            // Удаляем существующие риски проекта
            CalcRisk::where('project_num', $old_projNum)->delete();

            $data_risks = [];
            foreach ($req->input('risk') as $index => $riskData) {
                $item = [
                    'project_num' => $project->projNum,
                    'calcRisk_name' => $riskData['riskName'],
                ];
                array_push($data_risks, $item);
            }
            // Вставляем новые данные о рисках
            CalcRisk::insert($data_risks);
        }

        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation']);
    }

    // ------------------- УДАЛЕНИЕ СТРОК ИЗ ТАБЛИЦЫ РАСЧЕТ ВО ВРЕМЯ РЕДАКТИРОВАНИЯ -----------------------------------
    public function deleteRow($table, $id)
    {
        $project = Projects::find($id);
        $model = null;

        switch ($table) {
            case 'equipment':
                $model = Equipment::find($id);
                break;
            case 'markups':
                $model = Markup::find($id);
                break;
            case 'contacts':
                $model = Contacts::find($id);
                break;
            case 'risks':
                $model = CalcRisk::find($id);
                break;
            case 'additional_expenses':
                $expense = AdditionalExpense::find($id);
                if ($expense) {
                    $expenseCost = $expense->cost;
                    $expense->delete();

                    // Пересчитываем параметр $total
                    $total = 0;
                    $expenses = Expenses::where('project_num', $project->projNum)->firstOrFail();
                    foreach ($expenses->additionalExpenses as $additionalExpense) {
                        $total += $additionalExpense->cost;
                    }
                    $expenses->total = $total;
                    $expenses->save();

                    return response()->json(['success' => true, 'expenseCost' => $expenseCost]);
                }
                break;
            default:
                return response()->json(['success' => false, 'message' => 'Неизвестная таблица.']);
        }

        if ($model) {
            $model->delete();
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false, 'message' => 'Запись не найдена.']);
        }
    }


    // редактирование карты проекта -> РЕАЛИЗАЦИЯ (открыывает страницу редактирования по id записи)
    public function updateRealization($id)
    {
        $project = new Projects;
        $user = Auth::user();
        return view('update-realization', ['project' => $project->find($id), 'user']);
    }

    // РЕДАКТИРОВАНИЕ данных для карты проекта -> РЕАЛИЗАЦИЯ
    public function updateRealizationSubmit($id, Request $req)
    {
        $project = Projects::find($id);
        $num = $project->getOriginal('projNum');

        // --------------РЕАЛИЗАЦИЯ----------------//
        // базовая справка
        $BasicReference = BasicReference::where('project_num', $project->projNum)->first();
        $BasicReference->projName = $req->input('projName');
        // $BasicReference->projCustomer = $project->endCustomer = $req->input('endCustomer');
        $BasicReference->projCustomer = $project->endCustomer;
        $BasicReference->startDate = $req->input('startDate');
        $BasicReference->endDate = $req->input('endDate');
        $BasicReference->projGoal = $req->input('projGoal');
        $BasicReference->projCurator = $req->input('projCurator');
        $BasicReference->projManager = $req->input('projManager');
        $BasicReference->linkPlan = $req->input('linkPlan');
        $BasicReference->payment = $req->input('payment');
        $BasicReference->save();

        $project->payment = $req->input('payment');
        $project->save();

//        $FromKSGperiodDays =  updateCalculationSubmit::$periodDays;
//        $FromKSGperiodDays -> save();
//        $FromKSGpriceTotals =  updateCalculationSubmit::$priceTotals;
//        $FromKSGpriceTotals -> save();

        // доп информация
        $BasicInfo = BasicInfo::where('project_num', $project->projNum)->first();
        $BasicInfo->contractor = $req->contractor;
        $BasicInfo->contract_num = $req->contract_num;
        // $BasicInfo->price_plan = $request->price_plan;
        $BasicInfo->price_plan = $project->totals->first()->price;
        $BasicInfo->price_fact = $req->price_fact;
        $BasicInfo->contract_price = $req->contract_price;
        // расчет полей
        $contract_price = floatval($req->contract_price);
        $price_plan = floatval($req->price_plan);
        $price_fact = floatval($req->price_fact);
        // нахождения поля profit_plan(прибыль план) путем разницы стоим.контракта и себестоимость план
        $profit_plan = ($contract_price - $price_plan);
        // нахождения поля profit_fact(прибыль факт) путем разницы стоим.контракта и себестоимость факт
        $profit_fact = ($contract_price - $price_fact);
        // вывод расчитанных полей
        $BasicInfo->profit_plan = $profit_plan;
        $BasicInfo->profit_fact = $profit_fact;

        $BasicInfo->start_date = $req->start_date;
        $BasicInfo->end_date_plan = $req->end_date_plan;

        $BasicInfo->end_date_fact = $req->end_date_fact;
        $BasicInfo->complaint = $req->complaint;
        $BasicInfo->save();

        // Состав рабочей группы и ответственность
        $workGroup = workGroup::where('project_num', $project->projNum)->first();
        $workGroup->projCurator = $req->projCurator2;
        $workGroup->projDirector = $req->projDirector;
        $workGroup->techlid = $req->techlid;
        $workGroup->production = $req->production;
        $workGroup->supply = $req->supply;
        $workGroup->logistics = $req->logistics;
        $workGroup->save();

        switch ($project->projNumSuf) {
            case 'Группа 1':
                RegSInteg::where('vnNum', $num)->update(['proj_cost' => $req->price_fact, 'profit' => $profit_fact,
                    'date_start' => $req->start_date, 'date_end' => $req->end_date_fact, 'payment' => $req->payment]);
                break;
            case 'Группа 2':
                RegEOB::where('vnNum', $num)->update(['proj_cost' => $req->price_fact, 'profit' => $profit_fact,
                    'date_start' => $req->start_date, 'date_end' => $req->end_date_fact, 'payment' => $req->payment]);
                break;
            case 'Группа 3':
                RegNHRS::where('vnNum', $num)->update(['proj_cost' => $req->price_fact, 'profit' => $profit_fact,
                    'date_start' => $req->start_date, 'date_end' => $req->end_date_fact, 'payment' => $req->payment]);
                break;
            case 'Группа 4':
                RegOther::where('vnNum', $num)->update(['proj_cost' => $req->price_fact, 'profit' => $profit_fact,
                    'date_start' => $req->start_date, 'date_end' => $req->end_date_fact, 'payment' => $req->payment]);
                break;
            default:
                // Обработка, если тип не определен
                break;
        }

        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#realization'])->with('success', 'Project data successfully updated');
    }

    // редактирование карты проекта -> ИЗМЕНЕНИЯ (открывает страницу редактирования по id записи)
    public function updateChanges($id)
    {
        $project = Projects::find($id);
        return view('update-changes', ['project' => $project]);
    }

    public function updateChangesSubmit($id, Request $req)
    {
        $project = Projects::find($id);

        // Ensure the request has the 'changes' key and it's an array
        if ($req->has('changes') && is_array($req->input('changes'))) {
            foreach ($req->input('changes') as $index => $ChangesData) {
                $change = Change::updateOrCreate(
                    [
                        'project_num' => $project->projNum,
                        'contract_num' => $ChangesData['contract_num'],
                        'contractor' => $ChangesData['contractor'],
                        'id' => $ChangesData['id'],
                    ],
                    [
                        'id' => $ChangesData['id'],
                        'change' => $ChangesData['change'],
                        'impact' => $ChangesData['impact'],
                        'stage' => $ChangesData['stage'],
                        'corrective' => $ChangesData['corrective'],
                        'responsible' => $ChangesData['responsible'],
                    ]
                );
            }
        }

        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#changes'])->with('success', 'Project data successfully updated');
    }


    public function search(Request $request)
    {
        $user = $request->user();
        $search_text = $request->input('search');
        $data = null;

        if ($user->role === 'admin') {
            $data = Projects::where('projManager', 'LIKE', '%' . $search_text . '%')
                ->orWhere('projNum', 'LIKE', '%' . $search_text . '%')
                ->orWhere('objectName', 'LIKE', '%' . $search_text . '%')
                ->get();
        } elseif ($user->role === 'proj_manager') {
            $data = Projects::where('projManager', $user->name)
                ->where(function ($query) use ($search_text) {
                    $query->where('projManager', 'LIKE', '%' . $search_text . '%')
                        ->orWhere('projNum', 'LIKE', '%' . $search_text . '%')
                        ->orWhere('objectName', 'LIKE', '%' . $search_text . '%');
                })
                ->get();
        } elseif ($user->role === 'responsible') {
            $data = Projects::where('projNumSuf', $user->group_num)
                ->where(function ($query) use ($search_text) {
                    $query->where('projManager', 'LIKE', '%' . $search_text . '%')
                        ->orWhere('projNum', 'LIKE', '%' . $search_text . '%')
                        ->orWhere('objectName', 'LIKE', '%' . $search_text . '%');
                })
                ->get();
        }

        if ($data->isEmpty()) {
            // Выводим текст, если результаты поиска пусты
            return view('search', ['noResults' => true]);
        }

        return view('search', compact('data', 'search_text'));
    }



    // --------------- ДОБАВЛЕНИЕ В РЕЕСТР -----------------------------
    // группа 2
    private function addToRegistryEob($project)
    {
//        Log::info('Adding to registry EOB 2:');
//        Log::info($project->toArray());

        RegEob::create([
            'vnNum' => $project->projNum,
            'purchaseName' => $project->proj_note,
            'delivery' => $project->supply,
            'pir' => $project->pir,
            'kd' => $project->kd,
            'prod' => $project->production,
            'shmr' => $project->smr,
            'pnr' => $project->pnr,
            'po' => $project->po,
            'smr' => $project->cmr,
            'purchaseOrg' => $project->contractor,
            'endUser' => $project->endCustomer,
            'object' => $project->objectName,
            'receiptDate' => $project->date_application,
            'submissionDate' => $project->date_offer,
            'projectManager' => $project->projManager,
            'payment' => $project->payment,
        ]);
    }

    // группа 1
    private function addToRegistrySinteg($project)
    {
//        Log::info('Adding to registry SInteg 1:');
//        Log::info($project->toArray());


        RegSInteg::create([
            'vnNum' => $project->projNum,
            'purchaseName' => $project->proj_note,
            'delivery' => $project->supply,
            'pir' => $project->pir,
            'kd' => $project->kd,
            'prod' => $project->production,
            'shmr' => $project->smr,
            'pnr' => $project->pnr,
            'po' => $project->po,
            'smr' => $project->cmr,
            'purchaseOrg' => $project->contractor,
            'endUser' => $project->endCustomer,
            'object' => $project->objectName,
            'receiptDate' => $project->date_application,
            'submissionDate' => $project->date_offer,
            'projectManager' => $project->projManager,
            'payment' => $project->payment,
        ]);
    }

    // группа 3
    private function addToRegistryNhrs($project)
    {
//        Log::info('Adding to registry NHRS 3:');
//        Log::info($project->toArray());

        RegNHRS::create([
            'vnNum' => $project->projNum,
            'purchaseName' => $project->proj_note,
            'delivery' => $project->supply,
            'pir' => $project->pir,
            'kd' => $project->kd,
            'prod' => $project->production,
            'shmr' => $project->smr,
            'pnr' => $project->pnr,
            'po' => $project->po,
            'smr' => $project->cmr,
            'purchaseOrg' => $project->contractor,
            'endUser' => $project->endCustomer,
            'object' => $project->objectName,
            'receiptDate' => $project->date_application,
            'submissionDate' => $project->date_offer,
            'projectManager' => $project->projManager,
            'payment' => $project->payment,
        ]);
    }

    // группа 4
    private function addToRegistryOther($project)
    {
//        Log::info('Adding to registry Other 4:');
//        Log::info($project->toArray());

        RegOther::create([
            'vnNum' => $project->projNum,
            'purchaseName' => $project->proj_note,
            'delivery' => $project->supply,
            'pir' => $project->pir,
            'kd' => $project->kd,
            'prod' => $project->production,
            'shmr' => $project->smr,
            'pnr' => $project->pnr,
            'po' => $project->po,
            'smr' => $project->cmr,
            'purchaseOrg' => $project->contractor,
            'endUser' => $project->endCustomer,
            'object' => $project->objectName,
            'receiptDate' => $project->date_application,
            'submissionDate' => $project->date_offer,
            'projectManager' => $project->projManager,
            'payment' => $project->payment,
        ]);
    }

    // --------------- ИЗМЕНЕНИЯ В РЕЕСТРЕ ИЗ КАРТЫ ПРОЕКТА ----------------------------
    // группа 1
    private function updateRegistrySinteg($project, Request $req)
    {
        $registry = RegSInteg::where('vnNum', $project->projNum)->first();

        $user = $req->user();
        if ($user->role === 'admin')
            $project->projNum = $req->input('projNum');

        if ($registry) {
            $registry->update([
                'vnNum' => $project->projNum,
                'purchaseName' => $project->proj_note,
                'delivery' => $project->delivery,
                'pir' => $project->pir,
                'kd' => $project->kd,
                'prod' => $project->production,
                'shmr' => $project->smr,
                'pnr' => $project->pnr,
                'po' => $project->po,
                'smr' => $project->cmr,
                'purchaseOrg' => $project->contractor,
                'endUser' => $project->endCustomer,
                'object' => $project->objectName,
                'receiptDate' => $project->date_application,
                'submissionDate' => $project->date_offer,
                'projectManager' => $project->projManager,
                'payment' => $project->payment,
            ]);
        }
    }

    // группа 2
    private function updateRegistryEob($project, Request $req)
    {
        $registry = RegEob::where('vnNum', $project->projNum)->first();

        $user = $req->user();
        if ($user->role === 'admin')
            $project->projNum = $req->input('projNum');

        if ($registry) {
            $registry->update([
                'vnNum' => $project->projNum,
                'purchaseName' => $project->proj_note,
                'delivery' => $project->delivery,
                'pir' => $project->pir,
                'kd' => $project->kd,
                'prod' => $project->production,
                'shmr' => $project->smr,
                'pnr' => $project->pnr,
                'po' => $project->po,
                'smr' => $project->cmr,
                'purchaseOrg' => $project->contractor,
                'endUser' => $project->endCustomer,
                'object' => $project->objectName,
                'receiptDate' => $project->date_application,
                'submissionDate' => $project->date_offer,
                'projectManager' => $project->projManager,
                'payment' => $project->payment,
            ]);
        }
    }

    // группа 3
    private function updateRegistryNhrs($project, Request $req)
    {
        $registry = RegNHRS::where('vnNum', $project->projNum)->first();

        $user = $req->user();
        if ($user->role === 'admin')
            $project->projNum = $req->input('projNum');

        if ($registry) {
            $registry->update([
                'vnNum' => $project->projNum,
                'purchaseName' => $project->proj_note,
                'delivery' => $project->delivery,
                'pir' => $project->pir,
                'kd' => $project->kd,
                'prod' => $project->production,
                'shmr' => $project->smr,
                'pnr' => $project->pnr,
                'po' => $project->po,
                'smr' => $project->cmr,
                'purchaseOrg' => $project->contractor,
                'endUser' => $project->endCustomer,
                'object' => $project->objectName,
                'receiptDate' => $project->date_application,
                'submissionDate' => $project->date_offer,
                'projectManager' => $project->projManager,
                'payment' => $project->payment,
            ]);
        }
    }

    // группа 4
    private function updateRegistryOther($project, Request $req)
    {
        $registry = RegOther::where('vnNum', $project->projNum)->first();

        $user = $req->user();
        if ($user->role === 'admin')
            $project->projNum = $req->input('projNum');

        if ($registry) {
            $registry->update([
                'vnNum' => $project->projNum,
                'purchaseName' => $project->proj_note,
                'delivery' => $project->delivery,
                'pir' => $project->pir,
                'kd' => $project->kd,
                'prod' => $project->production,
                'shmr' => $project->smr,
                'pnr' => $project->pnr,
                'po' => $project->po,
                'smr' => $project->cmr,
                'purchaseOrg' => $project->contractor,
                'endUser' => $project->endCustomer,
                'object' => $project->objectName,
                'receiptDate' => $project->date_application,
                'submissionDate' => $project->date_offer,
                'projectManager' => $project->projManager,
                'payment' => $project->payment,
            ]);
        }
    }

    // --------------- ВЫВОД СПИСКА РУКОВОДИТЕЛЕЙ ПРОЕКТА ----------------------------
    public function getManagers($group)
    {
        try {
            $managers = DB::table('project_managers')->where('groupNum', $group)->get();
            return response()->json($managers);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    // --------------- ДОБАВЛЕНИЕ ОБОРУДОВАНИЯ ----------------------------
    public function addEquipment($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        if ($request->has('equipment')) {
            $data_equipment = array();
            $totalPrice = 0;
            foreach ($request->input('equipment') as $index => $equipmentData) {
                // нахождения поля price(стоимость) путем умножения кол-ва на цену за ед. (count*priceUnit)
                $count = intval($equipmentData['count']);
                $priceUnit = floatval($equipmentData['priceUnit']);
                $price = $count * $priceUnit; // Расчёт стоимости

                $item = array(
                    'project_num' => $project->projNum,
                    'nameTMC' => $equipmentData['nameTMC'],
                    'manufacture' => $equipmentData['manufacture'],
                    'unit' => $equipmentData['unit'],
                    'count' => $equipmentData['count'],
                    'priceUnit' => $equipmentData['priceUnit'],
                    'price' => $price, //запись в бд расчитанной стоимости
                );
                array_push($data_equipment, $item);
                $totalPrice += $price;
            }
            Equipment::insert($data_equipment);
        }
        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
    }

    // --------------- ДОБАВЛЕНИЕ ПРОЧИХ РАСХОДОВ  ----------------------------
    public function addExpenses($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        // прочие расходы
        $expenses = new Expenses;
        // нахождения поля total(всего) путем сложения значений всех полей
        $commandir = floatval($request->commandir);
        $rd = floatval($request->rd);
        $shmr = floatval($request->shmr);
        $pnr = floatval($request->pnr);
        $cert = floatval($request->cert);
        $delivery = floatval($request->delivery);
        $rastam = floatval($request->rastam);
        $ppo = floatval($request->ppo);
        $guarantee = floatval($request->guarantee);
        $check = floatval($request->check);
        $total = $commandir + $rd + $shmr + $pnr + $cert + $delivery + $rastam + $ppo + $guarantee + $check; // Расчёт всего
        $expenses->project_num = $project->projNum;
        $expenses->commandir = $request->commandir;
        $expenses->rd = $request->rd;
        $expenses->shmr = $request->shmr;
        $expenses->pnr = $request->pnr;
        $expenses->cert = $request->cert;
        $expenses->delivery = $request->delivery;
        $expenses->rastam = $request->rastam;
        $expenses->ppo = $request->ppo;
        $expenses->guarantee = $request->guarantee;
        $expenses->check = $request->check;

        $expenses->total = $total;
        $expenses->save();

        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
    }

    // --------------- ДОБАВЛЕНИЕ ИТОГО ----------------------------
    public function addTotals($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        // таблица прочие расходы
        $expensesData = Expenses::where('project_num', $project->projNum)->first();
        $equipmentData = Equipment::where('project_num', $project->projNum)->first();

        // Check if expenses data is available
        if ($expensesData) {
            // Access individual expense fields
            $commandir = $expensesData->commandir;
            $rd = $expensesData->rd;
            $shmr = $expensesData->shmr;
            $pnr = $expensesData->pnr;
            $cert = $expensesData->cert;
            $delivery = $expensesData->delivery;
            $rastam = $expensesData->rastam;
            $ppo = $expensesData->ppo;
            $guarantee = $expensesData->guarantee;
            $check = $expensesData->check;

            $totalPrice = $equipmentData->price;

            // Calculate total from expense fields
            $total = $commandir + $rd + $shmr + $pnr + $cert + $delivery + $rastam + $ppo + $guarantee + $check;

            // Calculate priceTotals
            $priceTotals = $totalPrice + $total;

            // Continue with the rest of your code
            $totals = new Total;
            $totals->project_num = $project->projNum;
            $totals->kdDays = $request->kdDays;
            $totals->equipmentDays = $request->equipmentDays;
            $totals->productionDays = $request->productionDays;
            $totals->shipmentDays = $request->shipmentDays;
            $totals->periodDays = $totals->kdDays + $totals->equipmentDays + $totals->productionDays + $totals->shipmentDays;
            $totals->price = $priceTotals;
            $totals->save();

            return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
        } else {
            // Handle the case when expenses data is not found
            return redirect()->back()->with('error', 'Expenses data not found for the project');
        }
    }

    // --------------- ДОБАВЛЕНИЕ УРОВНЯ НАЦЕНКИ ----------------------------
    public function addMarkups($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        if ($request->has('markups')) {
            $data_markups = array();
            foreach ($request->input('markups') as $index => $markupsData) {
                $item = array(
                    'project_num' => $project->projNum,
                    'date' => $markupsData['date'],
                    'percentage' => $markupsData['percentage'],
                    'priceSubTkp' => $markupsData['priceSubTkp'],
                    'agreedFio' => $markupsData['agreedFio']
                );
                array_push($data_markups, $item);
            }
            Markup::insert($data_markups);
        }
        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
    }

    // --------------- ДОБАВЛЕНИЕ РИСКИ ----------------------------
    public function addRisks($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);
        if ($request->has('risks')) {
            $data_risks = array();
            foreach ($request->input('risks') as $index => $risksData) {
                $item = array(
                    'project_num' => $project->projNum,
                    'calcRisk_name' => $risksData['riskName']
                );
                array_push($data_risks, $item);
            }
            CalcRisk::insert($data_risks);
        }

        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
    }

    // ------------------------ ПЕРЕНАЗНАЧЕНИЕ ПРОЕКТА ---------------------------
    public function projectRedirect ($id, Request $request){
        $user = Auth::user();
        $users = User::with('groups')->get(); // Получаем всех пользователей с их группами
        $groups = $user->groups()->get(); // Получаем все группы пользователя

        $project = Projects::find($id);
        // Получаем старое значение projectManager
        $num = $project->getOriginal('projNum');
        // Обновление руководителя проекта
        $project->projManager = $request->input('users');

        switch ($project->projNumSuf) {
            case 'Группа 1':
                RegSInteg::where('vnNum', $num)->update(['projectManager' => $request->input('users')]);
                break;
            case 'Группа 2':
                RegEOB::where('vnNum', $num)->update(['projectManager' => $request->input('users')]);
                break;
            case 'Группа 3':
                RegNHRS::where('vnNum', $num)->update(['projectManager' => $request->input('users')]);
                break;
            case 'Группа 4':
                RegOther::where('vnNum', $num)->update(['projectManager' => $request->input('users')]);
                break;
            default:
                // Обработка, если тип не определен
                break;
        }

        BasicReference::where('project_num', $num)->update(['projManager' => $request->input('users')]);
        Change::where('project_num', $num)->update(['responsible' => $request->input('users')]);
        RegReestrKP::where('project_num', $num)->update(['sender' => $request->input('users')]);

        $project->save();


//        return view('project-redirect', compact('groups', 'users', 'project'));
        return redirect()->back()->with('success', 'Руководитель проекта успешно обновлен!');
    }





    // ------------------------ ПРОДОЛЖЕНИЕ ЗАПОЛНЕНИЯ КАРТЫ ПРОЕКТА -------------
    public function projectСontinue($id, Request $request)
    {
        // поиск связанной карты проекта
        $project = Projects::find($id);

// оборудование
        if ($request->has('equipment')) {
            foreach ($request->input('equipment') as $index => $equipmentData) {
                // нахождения поля price(стоимость) путем умножения кол-ва на цену за ед. (count*priceUnit)
                $count = intval($equipmentData['count']);
                $priceUnit = floatval($equipmentData['priceUnit']);
                $price = $count * $priceUnit; // Расчёт стоимости

                // Создаем новую запись в таблице Equipment с указанием project_num
                Equipment::create([
                    'project_num' => $project->projNum,
                    'nameTMC' => $equipmentData['nameTMC'],
                    'manufacture' => $equipmentData['manufacture'],
                    'unit' => $equipmentData['unit'],
                    'count' => $equipmentData['count'],
                    'priceUnit' => $equipmentData['priceUnit'],
                    'price' => $price,
                    'equipment_file' => null, // Поскольку это создание новой записи, обнуляем значение файла
                    'equipment_fileName' => null,
                ]);
            }
        }

// Обработка загрузки файлов
        if ($request->hasFile('equipment_file')) {
            $files = $request->file('equipment_file');

            foreach ($files as $index => $file) {
                // Генерация уникального имени для файла
                $fileName = $file->getClientOriginalName();
                $filePath = $file->storeAs('equipment_files', $fileName);

                // Создаем или обновляем записи в таблице Equipment с добавлением файла
                Equipment::updateOrCreate(
                    ['project_num' => $project->projNum],
                    [
                        'equipment_file' => $filePath,
                        'equipment_fileName' => $fileName,
                    ]
                );
            }
        }

        // прочие расходы
        $expenses = new Expenses;
        // нахождения поля total(всего) путем сложения значений всех полей
        $commandir = floatval($request->commandir);
        $rd = floatval($request->rd);
        $shmr = floatval($request->shmr);
        $pnr = floatval($request->pnr);
        $cert = floatval($request->cert);
        $delivery = floatval($request->delivery);
        $rastam = floatval($request->rastam);
        $ppo = floatval($request->ppo);
        $guarantee = floatval($request->guarantee);
        $check = floatval($request->check);

        // $total =  $commandir + $rd + $shmr + $pnr + $cert + $delivery + $rastam + $ppo + $guarantee + $check; // Расчёт всего
        $expenses->project_num = $project->projNum;
        $expenses->commandir = $request->commandir;
        $expenses->rd = $request->rd;
        $expenses->shmr = $request->shmr;
        $expenses->pnr = $request->pnr;
        $expenses->cert = $request->cert;
        $expenses->delivery = $request->delivery;
        $expenses->rastam = $request->rastam;
        $expenses->ppo = $request->ppo;
        $expenses->guarantee = $request->guarantee;
        $expenses->check = $request->check;

        // Получаем все дополнительные расходы из запроса и просуммируем их
        $additionalExpensesTotal = 0;
        if ($request->has('additional_expenses')) {
            foreach ($request->additional_expenses as $additionalExpense) {
                $additionalExpensesTotal += floatval($additionalExpense);
            }
        }
        // Подсчитываем общую сумму расходов, включая дополнительные расходы
        $total = $commandir + $rd + $shmr + $pnr + $cert + $delivery + $rastam + $ppo + $guarantee + $check + $additionalExpensesTotal;
        // Присваиваем общую сумму полю total в модели Expenses
        $expenses->total = $total;
        // Сохраняем основные расходы
        $expenses->save();

        // Теперь обрабатываем дополнительные расходы
        if ($request->has('additional_expenses')) {
            foreach ($request->additional_expenses as $additionalExpense) {
                $additional = new AdditionalExpense;
                $additional->expense_id = $expenses->id; // Привязываем к основному расходу
                $additional->cost = $additionalExpense;
                $additional->save();
            }
        }


        // итого
        $totals = new Total;
        // нахождения поля periodDays(итого срок) путем сложения значений всех полей
        $kdDays = floatval($request->kdDays);
        $equipmentDays = floatval($request->equipmentDays);
        $productionDays = floatval($request->productionDays);
        $shipmentDays = floatval($request->shipmentDays);
        $periodDays = $kdDays + $equipmentDays + $productionDays + $shipmentDays; // Расчет итого
        // нахождения поля price(себестоимость) путем сложения поля всего из таблицы оборудования и всего из проч.расх.
//        $priceTotals = ($totalPrice + $total);
        $priceTotals = ($price + $total);
        $totals->project_num = $project->projNum;
        $totals->kdDays = $request->kdDays;
        $totals->equipmentDays = $request->equipmentDays;
        $totals->productionDays = $request->productionDays;
        $totals->shipmentDays = $request->shipmentDays;

        $totals->periodDays = $periodDays;
        $totals->price = $priceTotals;
        $totals->save();
        // уровень наценки
//        if ($request->has('markups')) {
//            $data_markups = array();
//            foreach ($request->input('markups') as $index => $markupsData) {
//                $item = array(
//                    'project_num' => $project->projNum,
//                    'date' => $markupsData['date'],
//                    'percentage' => $markupsData['percentage'],
//                    'priceSubTkp' => $markupsData['priceSubTkp'],
//                    'agreedFio' => $markupsData['agreedFio']
//                );
//                array_push($data_markups, $item);
//            }
//            Markup::insert($data_markups);
//        }
        // риски
        if ($request->has('risks')) {
            $data_risks = array();
            foreach ($request->input('risks') as $index => $risksData) {
                $item = array(
                    'project_num' => $project->projNum,
                    'calcRisk_name' => $risksData['riskName']
                );
                array_push($data_risks, $item);
            }
            CalcRisk::insert($data_risks);
        }
        return redirect()->route('project-data-one', ['id' => $id, 'tab' => '#calculation'])->with('success', 'Project data successfully added');
    }
    public function downloadEquipmentFile($id)
    {
        $equipment = Equipment::findOrFail($id);
        $filePath = storage_path('app/equipment_files/' . $equipment->equipment_fileName);
        return response()->download($filePath, $equipment->equipment_fileName);
    }

    // ------------------------ КОПИЯ КАРТЫ ПРОЕКТА -------------
    public function copyProject($id)
    {
        // Найти оригинальный проект
        $originalProject = Projects::findOrFail($id);
        // Определить группу и год оригинального проекта
        $group = $originalProject->projNumSuf;
        $year = $originalProject->projNumPre;
        // Посчитать количество копий в группе
        $lastCopyNumber = Projects::where('projNumSuf', $group)
            ->where('projNumPre', $year)
            ->count();

        $copyNumber = $lastCopyNumber + 1;
        // Создать новый проект
        $newProject = new Projects;
        $newProject->projNumPre = $originalProject->projNumPre;
        $newProject->projNum = $copyNumber-1 . '-' . $originalProject->projNum;
        $newProject->projNumSuf = $group;
        $newProject->projManager = $originalProject->projManager;
        $newProject->objectName = $originalProject->objectName;
        $newProject->endCustomer = $originalProject->endCustomer;
        $newProject->contractor = $originalProject->contractor;
        $newProject->date_application = $originalProject->date_application;

        // Копировать флаги работ
        $newProject->delivery = $originalProject->delivery;
        $newProject->pir = $originalProject->pir;
        $newProject->kd = $originalProject->kd;
        $newProject->production = $originalProject->production;
        $newProject->smr = $originalProject->smr;
        $newProject->pnr = $originalProject->pnr;
        $newProject->po = $originalProject->po;
        $newProject->cmr = $originalProject->cmr;

        $newProject->save();

        // Добавить новый проект в соответствующий реестр
        switch ($originalProject->projNumSuf) {
            case 'Группа 1':
                $this->addToRegistrySinteg($newProject);
                break;
            case 'Группа 2':
                $this->addToRegistryEob($newProject);
                break;
            case 'Группа 3':
                $this->addToRegistryNhrs($newProject);
                break;
            case 'Группа 4':
                $this->addToRegistryOther($newProject);
                break;
            default:
                // Обработка, если тип не определен
                break;
        }
        // Копировать контакты оригинального проекта
        foreach ($originalProject->contacts as $contact) {
            $newContact = new Contacts;
            $newContact->project_num = $newProject->projNum;
            $newContact->fio = $contact->fio;
            $newContact->post = $contact->post;
            $newContact->organization = $contact->organization;
            $newContact->responsibility = $contact->responsibility;
            $newContact->phone = $contact->phone;
            $newContact->email = $contact->email;
            $newContact->save();
        }

        return redirect()->route('project-maps');
    }


}
