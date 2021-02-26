<?php

namespace App\Http\Controllers;

use App\Http\Requests\System\ShiftStoreRequest;
use App\Outlet;
use App\Shift;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    protected $entityAlias;
    protected $entityDependence;

    /**
     * ShiftController constructor.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->entityAlias = 'shifts';
        $this->entityDependence = true;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function index()
    {
        // Подключение политики
        $this->authorize(getmethod(__FUNCTION__), Shift::class);

        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $shifts = Shift::with([
            'filial',
            'outlet'
        ])
            ->moderatorLimit($answer)
            ->companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
//            ->filter()
//            ->orderBy('moderation', 'desc')
            ->oldest('created_at')
            ->paginate(30);
//        dd($subscribers);

        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.shifts.index', compact('shifts', 'pageInfo'));
    }

    public function shift(Request $request)
    {
        if ($request->has('date') && $request->has('outlet_id')) {

            $date = Carbon::createFromFormat('d.m.Y', $request->date);
            $outletId = $request->outlet_id;

//            if ($date->format('d.m.Y') == today()->format('d.m.Y')) {
//
//            }

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

            $shift = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', $date)
                ->where('outlet_id', $outletId)
                ->first();
//        dd($shift);

            if (empty($shift)) {
                $shift = Shift::make();
            }
        } else {

            $date = today();
            $outletId = $request->get('outlet_id', session('access.user_info.outlets')[0]->id);

            // Получаем из сессии необходимые данные (Функция находиться в Helpers)
            $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

            $openShift = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', '<', $date)
                ->where('outlet_id', $outletId)
                ->where('is_opened', true)
                ->first();
//        dd($openShift);

            if ($openShift) {
                return redirect()->route('shift', ['date' => $openShift->date->format('d.m.Y'), 'outlet_id' => $openShift->outlet_id]);
            } else {
                $shift = Shift::
//            moderatorLimit($answer)
//                ->
                companiesLimit($answer)
                    ->authors($answer)
                    ->systemItem($answer)
                    ->whereDate('date', $date)
                    ->where('outlet_id', $outletId)
                    ->first();

                if (empty($shift)) {
                    $shift = Shift::make();
                }
            }
//        dd($shift);

        }


        // Инфо о странице
        $pageInfo = pageInfo($this->entityAlias);

        return view('system.pages.shifts.shift.shift', compact('shift', 'pageInfo'));
    }

    /**
     * Открытие смены под торговой точкой
     *
     * @param ShiftStoreRequest $request
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function open(ShiftStoreRequest $request): \Illuminate\Http\RedirectResponse
    {
        // Подключение политики
        $this->authorize(getmethod('store'), Shift::class);

        $data = $request->validated();

        $data['filial_id'] = Outlet::where('id', $request->outlet_id)
            ->value('filial_id');

        $date = today();

        $data['date'] = $date->format('Y-m-d');

        $data['opened_at'] = $date->format('Y-m-d') . ' ' . now()->format('H:i:s');

        $previousShift = Shift::whereDate('date', '<', $date)
            ->where('outlet_id', $request->outlet_id)
            ->latest()
            ->first();

        $data['balance_open'] = $previousShift ? $previousShift->balance_close : 0;
        $data['balance_close'] = $previousShift ? $previousShift->balance_close : 0;

        $data['is_opened'] = true;

        $shift = Shift::create($data);

        if ($shift) {

            $filialShift = $this->openFilialShift($shift);

            return redirect()->route('shift', ['date' => $shift->date->format('d.m.Y'), 'outlet_id' => $shift->outlet_id]);
        } else {
            abort(403, __('errors.store'));
        }
    }

    /**
     * Открытие / обновление смены на филиал
     *
     * @param $shift
     * @return mixed
     */
    public function openFilialShift($shift)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $openFilialShift = Shift::
//            moderatorLimit($answer)
//                ->
        companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereDate('date', $shift->date)
            ->where('filial_id', $shift->filial_id)
            ->whereNull('outlet_id')
            ->where('is_opened', true)
            ->first();
//        dd($openFilialShift);

        if ($openFilialShift) {

            $openShifts = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', $shift->date)
                ->where('filial_id', $shift->filial_id)
                ->whereNotNull('outlet_id')
                ->where('is_opened', true)
                ->get([
                    'balance_open',
                    'cash',
                    'electronically',
                    'balance_close'
                ]);

            $openFilialShift->update([
                'cash' => $openShifts->sum('cash'),
                'electronically' => $openShifts->sum('electronically'),
                'balance_close' => $openFilialShift->balance_open + $openShifts->sum('cash')
            ]);

        } else {
            $previousFilialShiftBalanceCLose = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', '<', $shift->date)
                ->where('filial_id', $shift->filial_id)
                ->whereNull('outlet_id')
                ->where('is_opened', false)
                ->latest()
                ->value('balance_close');
//            dd($previousFilialShiftBalanceCLose);

            $data = [
                'date' => $shift->date,
                'opened_at' => $shift->opened_at,

                'balance_open' => $previousFilialShiftBalanceCLose ?? 0,
                'balance_close' => $previousFilialShiftBalanceCLose ?? 0,
                'is_opened' => true,

                'filial_id' => $shift->filial_id,
            ];

            $openFilialShift = Shift::create($data);
        }

        $companyShift = $this->openCompanyShift($shift);

        return $openFilialShift;
    }

    /**
     * Открытие / обновление смены на компанию
     *
     * @param $shift
     * @return mixed
     */
    public function openCompanyShift($shift)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $openCompanyShift = Shift::
//            moderatorLimit($answer)
//                ->
        companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereDate('date', $shift->date)
            ->whereNull('outlet_id')
            ->whereNull('filial_id')
            ->where('is_opened', true)
            ->first();
//        dd($openCompanyShift);

        if ($openCompanyShift) {

            $openFilialsShifts = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', $shift->date)
                ->whereNull('outlet_id')
                ->whereNotNull('filial_id')
                ->where('is_opened', true)
                ->get([
                    'cash',
                    'electronically',
                    'balance_close'
                ]);

            $openCompanyShift->update([
                'cash' => $openFilialsShifts->sum('cash'),
                'electronically' => $openFilialsShifts->sum('electronically'),
                'balance_close' => $openCompanyShift->balance_open + $openFilialsShifts->sum('cash')
            ]);

        } else {
            $previousCompanyShiftBalanceCLose = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', '<', $shift->date)
                ->whereNull('filial_id')
                ->whereNull('outlet_id')
                ->where('is_opened', false)
                ->latest()
                ->value('balance_close');
//            dd($previousCompanyShiftBalanceCLose);

            $data = [
                'date' => $shift->date,
                'opened_at' => $shift->opened_at,

                'balance_open' => $previousCompanyShiftBalanceCLose ?? 0,
                'balance_close' => $previousCompanyShiftBalanceCLose ?? 0,
                'is_opened' => true,
            ];

            $openCompanyShift = Shift::create($data);
        }

        return $openCompanyShift;
    }

    /**
     * Закрытие смены под торговой точкой
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function close($id): \Illuminate\Http\RedirectResponse
    {
        $shift = Shift::find($id);

        // Подключение политики
        $this->authorize(getmethod('update'), $shift);

        $shift->update([
            'closed_at' => now(),
            'is_opened' => false,
        ]);

        if ($shift) {
            $this->closeFilialShift($shift);

            return redirect()->route('shift');
        } else {
            abort(403, __('errors.store'));
        }
    }

    /**
     * Закрытие смены на филиал
     *
     * @param $shift
     */
    public function closeFilialShift($shift)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $countOpenShifts = Shift::
//            moderatorLimit($answer)
//                ->
        companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereDate('date', $shift->date)
            ->where('filial_id', $shift->filial_id)
            ->whereNotNull('outlet_id')
            ->where('is_opened', true)
            ->count();

        if ($countOpenShifts == 0) {

            $openFilialShift = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', $shift->date)
                ->where('filial_id', $shift->filial_id)
                ->whereNull('outlet_id')
                ->where('is_opened', true)
                ->update([
                    'closed_at' => $shift->closed_at,
                    'is_opened' => false,
                ]);
        }

        $this->closeCompanyShift($shift);
    }

    /**
     * Закрытие смены на компанию
     *
     * @param $shift
     */
    public function closeCompanyShift($shift)
    {
        // Получаем из сессии необходимые данные (Функция находиться в Helpers)
        $answer = operator_right($this->entityAlias, $this->entityDependence, getmethod(__FUNCTION__));

        $countOpenFilialsShifts = Shift::
//            moderatorLimit($answer)
//                ->
        companiesLimit($answer)
            ->authors($answer)
            ->systemItem($answer)
            ->whereDate('date', $shift->date)
            ->whereNotNull('filial_id')
            ->whereNull('outlet_id')
            ->where('is_opened', true)
            ->count();

        if ($countOpenFilialsShifts == 0) {

            $openFCompanyShift = Shift::
//            moderatorLimit($answer)
//                ->
            companiesLimit($answer)
                ->authors($answer)
                ->systemItem($answer)
                ->whereDate('date', $shift->date)
                ->whereNull('filial_id')
                ->whereNull('outlet_id')
                ->where('is_opened', true)
                ->update([
                    'closed_at' => $shift->closed_at,
                    'is_opened' => false,
                ]);
        }
    }
}
