<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Notice;
use App\Models\User;
use App\Models\UserCard;
use App\Models\FleetGest\Vehicle;
use Illuminate\Console\Command;
use Jenssegers\Date\Date;

class CheckValidities extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'validities:check {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run check user validities';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $this->info("Run check user validities\n");

        try {

            if (empty($this->argument('date'))) {
                $date = new Date();
            } else {
                $date = Date::parse($this->argument('date'));
            }

            $agencies = Agency::where('source', config('app.source'))->pluck('id')->toArray();

            try {
                /**
                 * ALERTAS COLABORADORES
                 */
                if(hasModule('human_resources')) {

                    //obtem a lista de utilizadores que vão receber o alerta
                    $recipients = \App\Models\User::where(function ($q) use ($agencies) {
                        $q->whereNull('agencies');
                        $q->orWhere(function ($q) use ($agencies) {
                            foreach ($agencies as $agency) {
                                $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                            }
                        });
                    })
                    ->whereHas('roles.perms', function ($query) {
                        $query->whereIn('name', ['users_cards', 'users_absences', 'users_profissional_info']);
                    })
                    ->get();

                    //obtem a lista de notiricações de todos os colaboradores
                    $params = [];
                    $params['return_totals'] = 1;
                    $params['start_date'] = '1970-01-01 00:00:00';
                    $users = User::getNotifications(null, $params);

                    $expireds = $users['expireds'];
                    $warnings = $users['warnings'];
                    $users    = $users['users'];

                    if ($users && !$users->isEmpty()) {

                        //prepara o alerta HTML no programa
                        $title    = '<i class="fas fa-car"></i> Cartões ou Certificados a Expirar';
                        $subtitle = 'Existem <b>' . $users->count() . '</b> colaboradores que necessitam atenção.<br/>';

                        if($warnings) {
                            $subtitle.= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-calendar-alt"></i> <b>' . $warnings . '</b> validades expiram nos próximos dias.<br/>';
                        }

                        if($expireds) {
                            $subtitle.= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-exclamation-triangle"></i> <b>' . $expireds . '</b> validades expiradas.';
                        }

                        $content = '<p>';
                        foreach ($users as $user) {

                            $content.= '<h4 style="margin-top: 0; font-weight: bold">' . $user->code .' - ' . strtoupper($user->fullname) .'</h4>';
                            $content.= '<table class="table table-condensed">';
                            $content.= '<tr>';
                            $content.= '<th style="background: #dddddd; text-align: left;">Notificação</th>
                                        <th style="background: #dddddd; text-align: left; width: 100px;">Data Limite</th>
                                        <th style="background: #dddddd; text-align: left; width: 100px">Dias Rest.</th>';
                            $content.= '</tr>';
                            $content.= '<tr>';
                            foreach($user->notifications as $notification) {

                                $date     = @$notification['date'] ? $notification['date']->format('Y-m-d') : '';
                                $daysleft = @$notification['days_left'] ? $notification['days_left'].' dias' : '';

                                $color = '#222';
                                if($notification['status'] == 'expired') {
                                    $color = 'red';
                                }

                                $content.= '<tr>
                                    <td style="border-bottom: 1px solid #dddddd; color: '. $color .'">'. $notification["title"] .'</td>
                                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $date .'</td>
                                    <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $daysleft .'</td>
                                </tr>';
                            }
                            $content.= '</tr>';
                            $content.= '</table>';

                        }
                        $content.= '</p>';
                        $content.= '<a href="'.route('admin.users.index').'" class="btn btn-sm btn-default">Aceder à gestão de colaboradores</a>';

                        //emite o alerta no software
                        $notice = new Notice();
                        $notice->title      = $title;
                        $notice->summary    = $subtitle;
                        $notice->content    = $content;
                        $notice->date       = date('Y-m-d');
                        $notice->sources    = [config('app.source')];
                        $notice->published  = 1;
                        $notice->auto       = 1;
                        $notice->save();

                        $recipientsIds = $recipients->pluck('id')->toArray();
                        $notice->users()->sync($recipientsIds);

                        foreach ($recipients as $recipient) {
                            $recipient->count_notices = $recipient->count_notices + 1;
                            $recipient->save();
                        }

                        //envia por e-mail
                        $params = [];
                        $params['expireds'] = 1;
                        $params['start_date'] = '1970-01-01 00:00:00';
                        $emails = implode(';', $recipients->pluck('email')->toArray());
                        User::sendNotificationsEmail(null, $params, $emails);
                    }
                }
            } catch(\Exception $e) {
                //dd($e->getMessage(). ' file '.$e->getFile(). ' line '. $e->getLine());
            }



            if(hasModule('fleet')) {

                $recipients = \App\Models\User::where(function ($q) use ($agencies) {
                        $q->whereNull('agencies');
                        $q->orWhere(function ($q) use ($agencies) {
                            foreach ($agencies as $agency) {
                                $q->orWhere('agencies', 'like', '%"' . $agency . '"%');
                            }
                        });
                    })
                    ->whereHas('roles.perms', function ($query) {
                        $query->whereIn('name', ['fleet_vehicles']);
                    })
                    ->get();

                $params = [];
                $params['return_totals'] = 1;
                $params['start_date'] = '1970-01-01 00:00:00';
                $vehicles = Vehicle::getNotifications(null, $params);

                $expireds = $vehicles['expireds'];
                $warnings = $vehicles['warnings'];
                $vehicles = $vehicles['vehicles'];

                if (!$vehicles->isEmpty()) {

                    $title = '<i class="fas fa-car"></i> Manutenções agendadas da frota';

                    $subtitle = 'Existem <b>' . $vehicles->count() . '</b> viaturas que necessitam atenção nos próximos dias.<br/>';

                    if($warnings) {
                        $subtitle.= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-calendar-alt"></i> <b>' . $warnings . '</b> assistencias agendadas ou validades a expirar.<br/>';
                    }

                    if($expireds) {
                        $subtitle.= '&nbsp;&nbsp;&nbsp;&nbsp;<i class="fas fa-fw fa-exclamation-triangle"></i> <b>' . $expireds . '</b> assistências ou lembretes expirados que necessitam revisão.';
                    }


                    $content = '<p>';
                    foreach ($vehicles as $vehicle) {

                        $content.= '<h4 style="margin-top: 0; font-weight: bold">Viatura ' . $vehicle->name .'</h4>';
                        $content.= '<table class="table table-condensed">';
                        $content.= '<tr>';
                        $content.= '<th style="background: #dddddd; text-align: left;">Notificação</th>
                                    <th style="background: #dddddd; text-align: left; width: 100px;">Data Limite</th>
                                    <th style="background: #dddddd; text-align: left; width: 80px">Km Limite</th>
                                    <th style="background: #dddddd; text-align: left; width: 100px">Dias Rest.</th>
                                    <th style="background: #dddddd; text-align: left; width: 100px">Km Rest.</th>';
                        $content.= '</tr>';
                        $content.= '<tr>';
                        foreach($vehicle->notifications as $notification) {

                            $date = @$notification['date'] ? $notification['date']->format('Y-m-d') : '';
                            $km = @$notification['km'] ? money($notification['km'], '', 0) : '';
                            $daysleft = @$notification['days_left'] ? $notification['days_left'].' dias' : '';
                            $kmleft = @$notification['km_left'] ? money($notification['km_left'], '', 0).'km' : '';

                            $color = '#222';
                            if($notification['status'] == 'expired') {
                                $color = 'red';
                            }

                            $content.= '<tr>
                                <td style="border-bottom: 1px solid #dddddd; color: '. $color .'">'. $notification["title"] .'</td>
                                <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $date .'</td>
                                <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $km .'</td>
                                <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $daysleft .'</td>
                                <td style="border-bottom: 1px solid #dddddd; text-align: center; color: '. $color .'">'. $kmleft .'</td>
                            </tr>';
                        }
                        $content.= '</tr>';
                        $content.= '</table>';

                    }
                    $content.= '</p>';
                    $content.= '<a href="'.route('admin.fleet.vehicles.index').'" class="btn btn-sm btn-default">Aceder ao módulo de frota</a>';

                    $notice = new Notice();
                    $notice->title = $title;
                    $notice->summary = $subtitle;
                    $notice->content = $content;
                    $notice->date = date('Y-m-d');
                    $notice->sources = [config('app.source')];
                    $notice->published = 1;
                    $notice->auto = 1;
                    $notice->save();

                    $recipientsIds = $recipients->pluck('id')->toArray();
                    $notice->users()->sync($recipientsIds);

                    foreach ($recipients as $recipient) {
                        $recipient->count_notices = $recipient->count_notices + 1;
                        $recipient->save();
                    }

                    //send by email
                    $params = [];
                    $params['expireds'] = 1;
                    $params['start_date'] = '1970-01-01 00:00:00';
                    $emails = implode(';', $recipients->pluck('email')->toArray());
                    Vehicle::sendNotificationsEmail(null, $params, $emails);
                }
            }


            $this->info("Run check user validities. | Date: " . $date);
            return;
        } catch (\Exception $e) {
            $this->info($e->getMessage().' FILE: '. $e->getFile(). ' ON LINE '. $e->getLine());
            return;
        }
    }
}
