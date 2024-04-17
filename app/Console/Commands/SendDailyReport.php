<?php

namespace App\Console\Commands;

use App\Models\Agency;
use App\Models\Customer;
use App\Models\FleetGest\Incidence;
use App\Models\Provider;
use App\Models\Shipment;
use App\Models\IncidenceType;
use Illuminate\Console\Command;
use Excel;
use Mail, App;

class SendDailyReport extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shipment:dailyReport';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notify shipments status to providers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $this->info("Notify shipments status\n");

        $this->sendToProviders();
        $this->sendToCustomers();

        return;
    }

    /**
     * Send email to providers
     */
    public function sendToProviders()
    {

        $providers = Provider::filterSource()
            ->where('daily_report', 1)
            ->get();

        foreach ($providers as $provider) {
            $this->info("Forncedor: " . trim($provider->name) . " (" . trim($provider->daily_report_email) . ")\n");
            $bindings = [
                'id',
                'date',
                'tracking_code',
                'sender_name',
                'sender_zip_code',
                'sender_country',
                'recipient_name',
                'recipient_zip_code',
                'recipient_country',
                'status_id',
                'volumes',
                'weight',
                'cost_price',
            ];

            $data = Shipment::filterAgencies()
                ->with('status')
                ->with(['history' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }])
                ->where('provider_id', $provider->id)
                ->whereHas('history', function ($q) {
                    $q->whereRaw('DATE(created_at) = "' . date('Y-m-d') . '"');
                })
                ->get($bindings);

            $locale = $provider->locale;

            if ($provider->id == '129') {
                $reportFile = $this->viaDirectaExcel($data, $locale);
            } elseif ($provider->id == '2') {
                $reportFile = $this->seabourneExcel($data, $locale);
            } else {
                $reportFile = $this->defaultExcel($data, $locale);
            }

            // Send notification by email
            if (!empty($provider->daily_report_email)) {
                $emails = validateNotificationEmails($provider->daily_report_email);

                if (!empty($emails['error'])) {
                    $this->info("Error. Não foi possível enviar o e-mail para " . implode(',', $emails['error']));
                }

                if (!empty($emails['valid'])) {
                    $locale = $provider->locale;

                    $subject = $locale == 'pt' ? 'Report Diário' : 'Daily Report';

                    try {
                        Mail::send('emails.daily_report', compact('locale', 'shipment'), function ($message) use ($emails, $reportFile, $subject) {
                            $message->to($emails['valid'])
                                ->subject($subject);
                            $message->attach($reportFile);
                        });
                    } catch (\Exception $e) {
                        $this->info("Error. Não foi possível enviar o e-mail para " . implode(',', $emails['valid']));
                    }
                }
            }
        }
    }

    /**
     * Send email to providers
     */
    public function sendToCustomers()
    {

        $agencies = Agency::filterSource()->pluck('id')->toArray();

        $customers = Customer::whereIn('agency_id', $agencies)
            ->where('daily_report', 1)
            ->get();


        foreach ($customers as $customer) {
            $this->info("Cliente: " . trim($customer->name) . " (" . trim($customer->daily_report_email) . ")\n");
            $bindings = [
                'id',
                'date',
                'tracking_code',
                'sender_name',
                'sender_zip_code',
                'sender_country',
                'recipient_name',
                'delivery_date',
                'recipient_zip_code',
                'recipient_country',
                'status_id',
                'service_id',
                'volumes',
                'weight',
                'provider_tracking_code',
                'reference'
            ];

            $locale = $customer->locale;

            $data = Shipment::filterAgencies()
                ->with(['status', 'service'])
                ->with(['history' => function ($q) {
                    $q->orderBy('created_at', 'desc');
                }])
                ->where('customer_id', $customer->id)
                ->whereHas('history', function ($q) {
                    $q->whereRaw('DATE(created_at) = "' . date('Y-m-d') . '"');
                })
                ->get($bindings);

            if (!$data->isEmpty()) {
                if (!$data->isEmpty()) {
                    if ($customer->id == '6997') {
                        $reportFile = $this->viaDirectaExcel($data, $locale);
                    } elseif ($customer->id == '7073' || $customer->id == '7081' || $customer->id == '7092') {
                        $reportFile = $this->seabourneExcel($data, $locale);
                    } else {
                        $reportFile = $this->defaultExcel($data, $locale);
                    }

                    // Send notification by email
                    if (!empty($customer->daily_report_email)) {
                        $emails = validateNotificationEmails($customer->daily_report_email);

                        if (!empty($emails['error'])) {
                            $this->info("Error. Não foi possível enviar o e-mail para " . implode(',', $emails['error']));
                        }

                        if (!empty($emails['valid'])) {
                            $locale = $customer->locale;

                            $subject = $locale == 'pt' ? 'Report Diário' : 'Daily Report';

                            try {
                                Mail::send('emails.daily_report', compact('locale', 'shipment'), function ($message) use ($emails, $reportFile, $subject) {
                                    $message->to($emails['valid'])
                                        ->subject($subject);
                                    $message->attach($reportFile);
                                });
                            } catch (\Exception $e) {
                                $this->info("Error. Não foi possível enviar o e-mail para " . implode(',', $emails['valid']));
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     *
     * @param $data
     */
    public function seabourneExcel($data, $locale = 'pt')
    {

        $header = [
            'AWB',
            'Date',
            'Time',
            'Status Code',
            'Signature'
        ];


        return Excel::create('Update PODs - [' . date('Y-m-d') . ']', function ($file) use ($data, $header) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach ($data as $shipment) {
                    $rowData = [
                        $shipment->provider_tracking_code,
                        $shipment->history->first()->created_at->format('Ymd'),
                        $shipment->history->first()->created_at->format('H:i'),
                        $shipment->status_id == 5 ? 'OK' : 'KO',
                        $shipment->history->first()->receiver,
                    ];
                    $sheet->appendRow($rowData);
                }
            });
        })->store("xlsx", false, true)['full'];
    }

    /**
     * Via Directa summary
     * @param $data
     */
    public function viaDirectaExcel($data, $locale = 'pt')
    {

        $header = [
            'Guia',
            'Número de envio',
            'Data',
            'Destinatário',
            'Estado',
            'Data/Hora Entrega',
            'Receptor',
            'Incidência',
            'Peso Bruto VD (Kg)',
            'Peso Bruto  (Kg)',
            'Peso Taxável (Kg)'
        ];


        return Excel::create('Update PODs - [' . date('Y-m-d') . ']', function ($file) use ($data, $header, $locale) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header, $locale) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                foreach ($data as $shipment) {

                    $incidences = '';

                    foreach ($shipment->history as $item) {
                        if ($item->status_id == 9) {
                            if ($locale == 'pt') {
                                $incidences .= $item->created_at . ' ' . @$item->incidence->name . '<br/>';
                            } else {
                                $incidences .= $item->created_at . ' ' . @$item->incidence->name_en . '<br/>';
                            }
                        }
                    }

                    $rowData = [
                        $shipment->referece,
                        $shipment->provider_tracking_code,
                        $shipment->date,
                        $shipment->recipient_name,
                        $locale == 'pt' ? $shipment->status->name : $shipment->status->{'name_' . $locale},
                        $shipment->history->first()->created_at,
                        $shipment->history->first()->receiver,
                        br2nl($incidences),
                        $shipment->weight,
                        $shipment->volumetric_weight,
                        $shipment->volumetric_weight > $shipment->weight ? $shipment->volumetric_weight : $shipment->weight,
                    ];
                    $sheet->appendRow($rowData);
                }
            });
        })->store("xlsx", false, true)['full'];
    }

    public function defaultExcel($data, $locale = 'pt')
    {

        if ($locale == 'pt') {
            $header = [
                'Data',
                'AWB',
                'Referencia',
                'Serviço',
                'Expedidor',
                'Consignatário',
                'Código Postal',
                'Estado/Status',
                'Data Estado',
                'Obs Estado',
                'Motivo Incidência',
                'Data Agendada Entrega',
                'Recebido Por',
                'URL',
                //'Obs',
                //'Valor'
            ];
        } else {
            $header = [
                'Date',
                'AWB',
                'Reference',
                'Service',
                'Shipper',
                'Cnee',
                'Zip-Code',
                'Status',
                'Date Status',
                'Remarks state',
                'Incidence Reason',
                'Scheduled Delivery Date',
                'Received By',
                'URL'
                //'Remarks',
                //'Price'
            ];
        }



        return Excel::create('Update PODs - [' . date('Y-m-d') . ']', function ($file) use ($data, $header, $locale) {

            $file->sheet('Listagem', function ($sheet) use ($data, $header, $locale) {

                $sheet->row(1, $header);
                $sheet->row(1, function ($row) {
                    $row->setBackground('#ee7c00');
                    $row->setFontColor('#ffffff');
                });

                if ($locale == 'pt') {
                    foreach ($data as $shipment) {
                        $lastHistory = $shipment->lastHistory;
                        $rowData = [
                            $shipment->date,
                            $shipment->provider_tracking_code,
                            $shipment->reference,
                            $shipment->service->name ?? '',
                            $shipment->sender_name,
                            $shipment->recipient_name,
                            $shipment->recipient_zip_code,
                            $locale == 'pt' ? $shipment->status->name : $shipment->status->{'name_' . $locale},
                            $lastHistory->created_at ?? '',
                            $lastHistory->obs ?? '',
                            $lastHistory->incidence->name ?? 'N/A',
                            $shipment->delivery_date ?? 'N/A',
                            $lastHistory->receiver ?? '',
                            App::make('url')->to('/') . '/pt/tracking?tracking=' . $shipment->tracking_code,
                            //$shipment->obs,
                            //$shipment->cost_price
                        ];
                        $sheet->appendRow($rowData);
                    }
                } else {
                    foreach ($data as $shipment) {
                        $lastHistory = $shipment->lastHistory;
                        $rowData = [
                            $shipment->date,
                            $shipment->provider_tracking_code,
                            $shipment->reference,
                            $shipment->service->name ?? '',
                            $shipment->sender_name,
                            $shipment->recipient_name,
                            $shipment->recipient_zip_code,
                            $locale == 'pt' ? $shipment->status->name : $shipment->status->{'name_' . $locale},
                            $lastHistory->created_at,
                            $lastHistory->obs ?? '',
                            $lastHistory->incidence->name_en ?? 'N/A',
                            $shipment->delivery_date ?? 'N/A',
                            $lastHistory->receiver ?? '',
                            App::make('url')->to('/') . '/pt/tracking?tracking=' . $shipment->tracking_code,
                            //$shipment->obs,
                            //$shipment->cost_price
                        ];
                        $sheet->appendRow($rowData);
                    }
                }
            });
        })->store("xlsx", false, true)['full'];
    }
}
