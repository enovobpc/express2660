<?php

namespace App\Http\Controllers\Admin\Exports;

use App\Models\UserAbsence;
use Auth, Excel, Setting, Date;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Models\User;

class OperatorsController extends \App\Http\Controllers\Admin\Controller
{

    /**
     * Sidebar active menu option
     *
     * @var string
     */
    protected $sidebarActiveOption = '';

    /**
     * Store last row of each iteration
     *
     * @var type
     */
    protected $lastRow = null;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['ability:' . config('permissions.role.admin') . ',operators']);
    }

    /**
     * Export simple file
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request)
    {
        $ids = $request->id;

        $header = [
            'codigo',
            'Nome',
            'Nome Completo',
            'Genero',
            'Data Nascimento',
            'Estado Civil',
            'Nacionalidade',
            'Nº Fiscal',
            'Nº Cartão Cidadão',
            'Nº Seg. Social',
            'Morada Residência',
            'Código Postal Residência',
            'Localidade Residência',
            'País Residência',
            'Telefone Pessoal',
            'Telemóvel Pessoal',
            'E-mail Pessoal',
            'Morada Fiscal',
            'Código Postal Fiscal',
            'Localidade Fiscal',
            'País Fiscal',
            'Nome Contacto SOS',
            'Grau parentesco SOS',
            'Telefone SOS',
            'Telemóvel SOS',
            'Cargo',
            'Telefone Empresa',
            'Telemóvel Empresa',
            'E-mail Empresa',
            'Grupos Trabalho',
            'Chefe/Responsável',
            'Salário Base',
            'Valor/Hora',
            'Subsidio Alimentação',
            'Isenção Horário',
            'Despesas',
            'Data de Admissão',
            'Observações Pessoais',
            'Observações Profissionais'
        ];

        try {
            $data = User::with('workgroups')
                ->filterSource()
                ->filterAgencies();

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter active
                $value = $request->active;
                if ($request->has('active')) {
                    $data = $data->where('active', $value);
                }

                //filter agency
                $value = $request->agency;
                if ($request->has('agency')) {
                    $data = $data->where('agency_id', $value);
                }
            }

            if (Auth::user()->isGuest()) {
                $data = $data->where('agency_id', '99999'); //hide data to gest agency role
            }

            $data = $data->get();

            Excel::create('Listagem de Colaboradores', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $user) {

                        $workgroups = null;
                        if (!$user->workgroups->isEmpty()) {
                            $workgroups = $user->workgroups->pluck('name')->toArray();
                            $workgroups = is_array($workgroups) ? implode(',', $workgroups) : '';
                        }

                        $rowData = [
                            $user->code,
                            $user->name,
                            $user->fullname,
                            $user->gender,
                            $user->birthdate,
                            $user->civil_status,
                            $user->nacionality,
                            $user->vat,
                            $user->id_card,
                            $user->ss_card,
                            $user->address,
                            $user->zip_code,
                            $user->city,
                            $user->country,
                            $user->personal_phone,
                            $user->personal_mobile,
                            $user->personal_email,
                            $user->fiscal_address,
                            $user->fiscal_zip_code,
                            $user->fiscal_city,
                            $user->fiscal_country,
                            $user->emergency_name,
                            $user->emergency_kinship,
                            $user->emergency_phone,
                            $user->emergency_mobile,
                            $user->professional_role,
                            $user->professional_phone,
                            $user->professional_mobile,
                            $user->professional_email,
                            $workgroups,
                            @$user->chief->name,
                            $user->salary_price,
                            $user->salary_value_hour,
                            $user->salary_food_allowance,
                            $user->salary_working_time_exemption,
                            $user->salary_expenses,
                            @$user->admission_date,
                            $user->about,
                            $user->salary_obs
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export customer recipients
     * @param Request $request
     * @param $customerId
     */
    public function holidaysBalance(Request $request)
    {

        $year = $request->get('year');
        $year = empty($year) ? date('Y') : $year;

        try {
            $ids = $request->id;

            $data = User::filterSource()
                ->with(['absences' => function ($q) use ($year) {
                    $q->where('is_holiday', 1);
                    $q->whereRaw('YEAR(start_date) = "' . $year . '"');
                }]);

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter active
                $value = $request->active;
                if ($request->has('active')) {
                    $data = $data->where('active', $value);
                }
            }

            $data = $data->get();

            $header = [
                'Código',
                'Nome',
                'Total Dias',
                'Dias Gozados',
                'Dias Agendados',
                'Dias Livres',
            ];


            Excel::create('Balanço Anual Férias ' . $year, function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $customer) {

                        $daysUsed = $daysScheduled = 0;
                        if ($customer->absences) {
                            $daysUsed = $customer->absences->filter(function ($item) {
                                return $item->start_date <= date('Y-m-d');
                            })->sum('duration');

                            $daysScheduled = $customer->absences->filter(function ($item) {
                                return $item->start_date > date('Y-m-d');
                            })->sum('duration');
                        }

                        $daysFree = Setting::get('rh_max_holidays') - ($daysUsed + $daysScheduled);

                        $rowData = [
                            $customer->code,
                            $customer->name,
                            Setting::get('rh_max_holidays'),
                            $daysUsed,
                            $daysScheduled,
                            $daysFree
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }

    /**
     * Export customer recipients
     * @param Request $request
     * @param $customerId
     */
    public function absences(Request $request)
    {

        $year = $request->get('year');
        $year = empty($year) ? date('Y') : $year;

        try {
            $ids = $request->id;

            $data = UserAbsence::with('type')
                ->join('users', function ($join) {
                    $join->on('users_absences.user_id', '=', 'users.id');
                })->whereNull('users.deleted_at')
                ->orderBy('user_id')
                ->orderBy('start_date', 'asc');

            if ($ids) {
                $data = $data->whereIn('id', $ids);
            } else {
                //filter user
                $value = $request->user;
                if ($request->has('user')) {
                    $data = $data->where('user_id', $value);
                }

                //filter active
                $value = $request->start_date;
                if ($request->has('start_date')) {

                    $startDate = $value;
                    $endDate   = $value;
                    if ($request->has('end_date')) {
                        $endDate = $request->end_date;
                    }

                    $data = $data->whereBetween('start_date', [$startDate, $endDate]);
                }
            }

            $data = $data->get([
                'users.code',
                'users.fullname',
                'users.name',
                'users_absences.*'
            ]);

            $header = [
                'Código',
                'Nome',
                'Tipo',
                'Estado',
                'Data Início',
                'Data Fim',
                'Duração',
                'Periodo',
                'Remunerada',
                'Sub. Alimentação',
                'Observações',
                'Anexo'
            ];


            Excel::create('Resumo de Férias e Faltas', function ($file) use ($data, $header) {

                $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                    $sheet->row(1, $header);
                    $sheet->row(1, function ($row) {
                        $row->setBackground('#ee7c00');
                        $row->setFontColor('#ffffff');
                    });

                    foreach ($data as $absence) {


                        $status = 'Aprovado';
                        if (!is_null($absence->end_date) && $absence->end_date->lt(Date::today())) {
                            $status = 'Concluído';
                        }

                        $filepath = '';
                        if ($absence->filepath) {
                            $filepath = asset($absence->filepath);
                        }

                        $rowData = [
                            $absence->code,
                            $absence->fullname ? $absence->fullname : $absence->name,
                            @$absence->type->name,
                            $status,
                            (!is_null($absence->start_date) && $absence->start_date->format('Y-m-d')) ? $absence->start_date->format('Y-m-d') : '',
                            (!is_null($absence->end_date) && $absence->end_date->format('Y-m-d')) ? $absence->end_date->format('Y-m-d') : '',
                            $absence->duration,
                            $absence->period == 'days' ? 'Dias' : 'Horas',
                            $absence->is_remunerated ? 'Sim' : 'Não',
                            $absence->is_meal_subsidy ? 'Sim' : 'Não',
                            $absence->obs,
                            $filepath
                        ];
                        $sheet->appendRow($rowData);
                    }
                });
            })->export('xls');
        } catch (\Exception $e) {
            return Redirect::back()->with('error', 'Erro interno ao gerar ficheiro Excel. ' . $e->getMessage());
        }
    }
}
