<?php

namespace App\Http\Controllers\Traits;

// Модели
use App\Position;
use App\User;
use App\Company;
use App\Department;
use App\Staffer;
use App\Employee;
use DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

trait DepartmentControllerTrait
{

    public function createFirstDepartment($company, $request = null)
    {

            // Подготовка: -------------------------------------------------------------------------------------

            // Получаем данные для авторизованного пользователя
            // $user_auth = $request->user();

            // Скрываем бога
            // $user_auth_id = hideGod($user_auth);
            // $company_auth_id = $user_auth->company_id;

        $department = new Department;
        $department->name = 'Филиал в городе ' . $company->location->city->name;
        $department->company_id = $company->id;
        $department->location_id = $company->location_id;
            $department->author_id = 1; // Робот
        $department->display = true;
        $department->email = $company->email;

        $department->company_id = $company->id;
            $department->save();
            Log::info('Сохраняем первый филиал');

        add_phones($request, $department);

            return $department;

        }

        public function createDirector($company, $department, $user)
        {
            Log::info('Компания id: ' . $company->id . ';');
            Log::info('Отдел id: ' . $department->id . ';');
            Log::info('Пользователь id: ' . $user->id . ';');

            // dd(Company::where('id', $company->id)->get());

            // Создаем должность директора и валим ей права
            $position = Position::create([
                [
                    'name' => 'Директор',
                    'page_id' => 12,
                    'direction' => true,
                    'company_id' => $company->id,
                    'system' => false,
                    'author_id' => 1,
                    'sector_id' => null,
                ],
            ]);

            DB::table('position_role')->insert([
                [
                    'position_id' => $position->id,
                    'role_id' => 2
                ],
            ]);

            Log::info('В трейт создания директора пришла компания: ' . $company->name . ' с ID: ' . $company->id);
            $staffer = new Staffer;
            $staffer->user_id = $user->id;
            $staffer->position_id = $position->id;
            $staffer->department_id = $department->id;
            $staffer->filial_id = $department->id;
            $staffer->company_id = $company->id;
            $staffer->author_id = 1; // Робот
            $staffer->save();
            Log::info('Сохраняем штатную единицу');

            $employee = new Employee;
            $employee->company_id = $company->id;
            $employee->staffer_id = $staffer->id;
            $employee->user_id = $user->id;
            $employee->employment_date = Carbon::today()->format('Y-m-d');
            $employee->author_id = 1; // Робот

            Log::info($employee);

            $employee->save();
            $employee = Employee::where('id', $employee->id)->first();

            Log::info($employee);

            Log::info('Сохраняем сотрудника:');
            Log::info('Устроен в компанию с id: ' . $employee->company_id . ';');
            Log::info('Устроен на вакансию с id: ' . $employee->staffer_id . ';');
            Log::info('Его пользовательский id: ' . $employee->user_id . ';');

            Log::info('Сохраняем должность. Устраиваем юзера в штат компании с ID: ' . $company->id);

            $position = $user->staff->first()->position;
            $position_id = $position->id;

            // Прописываем роли из должности для юзера
            setRolesFromPosition($position, $department, $user);

            return $employee;
        }
    }
