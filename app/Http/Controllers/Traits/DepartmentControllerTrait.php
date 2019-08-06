<?php

namespace App\Http\Controllers\Traits;

// Модели
use App\User;
use App\Department;
use App\Staffer;
use App\Employee;
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

        $department->email = $company->email;

        $department->company_id = $company->id;
            $department->save();
            Log::info('Сохраняем первый филиал');

        add_phones($request, $department);

            return $department;

        }

        public function createDirector($company, $department, $user, $request = null)
        {

            // Подготовка: -------------------------------------------------------------------------------------

            // Получаем данные для авторизованного пользователя
            // $user_auth = $request->user();

            // Скрываем бога
            // $user_auth_id = hideGod($user_auth);
            // $company_auth_id = $user_auth->company_id;

            $staffer = new Staffer;
            $staffer->user_id = $user->id;
            $staffer->position_id = 1; // Директор
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
            $employee->save();
            Log::info('Сохраняем должность. Устраиваем юзера.');

            $position = $user->staff->first()->position;
            $position_id = $position->id;

            // Прописываем роли из должности для юзера
            setRolesFromPosition($position, $department, $user);
            
            return $employee;
        }
    }