<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Notification;

class User extends Authenticatable
{
    use HasRoles;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'type',
        'avatar',
        'lang',
        'created_by',
        'job_title',
        'plan',
        'plan_expire_date',
        'is_active',
    ];

    public function creatorId()
    {
        if($this->type == 'Owner' || $this->type == 'Super Admin')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public $settings;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public $customField;

    public function ownerId()
    {
        if($this->type == 'Super Admin' || $this->type == 'Owner')
        {
            return $this->id;
        }
        else
        {
            return $this->created_by;
        }
    }

    public function priceFormat($price)
    {
        $settings = Utility::settings();

        return (($settings['site_currency_symbol_position'] == "pre") ? $settings['site_currency_symbol'] : '') . number_format($price, 2) . (($settings['site_currency_symbol_position'] == "post") ? $settings['site_currency_symbol'] : '');
    }

    public function dateFormat($date)
    {
        $settings = Utility::settings();

        return date($settings['site_date_format'], strtotime($date));
    }

    public function timeFormat($time)
    {
        $settings = Utility::settings();

        return date($settings['site_time_format'], strtotime($time));
    }

    public function invoiceNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["invoice_prefix"] . sprintf("%05d", $number);
    }

    public function contractNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["contract_prefix"] . sprintf("%05d", $number);
    }
    
    public function estimateNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["estimation_prefix"] . sprintf("%05d", $number);
    }

    public function mdfNumberFormat($number)
    {
        $settings = Utility::settings();

        return $settings["mdf_prefix"] . sprintf("%05d", $number);
    }

    public function deals()
    {
        return $this->belongsToMany('App\Models\Deal', 'user_deals', 'user_id', 'deal_id');
    }

    public function leads()
    {
        return $this->belongsToMany('App\Models\Lead', 'user_leads', 'user_id', 'lead_id');
    }

    public function clientDeals()
    {
        return $this->belongsToMany('App\Models\Deal', 'client_deals', 'client_id', 'deal_id');
    }

    public function clientEstimations()
    {
        return $this->hasMany('App\Models\Estimation', 'client_id', 'id');
    }

    public function clientContracts()
    {
        return $this->hasMany('App\Models\Contract', 'client_name', 'id');
    }

    public function getInvoiceCount($id)
    {
        $invoices = Invoice::select('invoices.*')->join('deals', 'invoices.deal_id', '=', 'deals.id')->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->where('client_deals.client_id', '=', $id)->where('invoices.created_by', '=', \Auth::user()->ownerId())->count();

        return $invoices;
    }

    public function clientPermission($dealId)
    {
        return ClientPermission::where('client_id', '=', $this->id)->where('deal_id', '=', $dealId)->first();
    }

    public function assignPlan($planID)
    {
        $plan = Plan::find($planID);
        if($plan)
        {
            $deals     = Deal::where('created_by', '=', $this->ownerId())->get();
            $dealCount = 0;
            foreach($deals as $deal)
            {
                $dealCount++;
                if($dealCount <= $plan->max_deals)
                {
                    $deal->is_active = 1;
                    $deal->save();
                }
                else
                {
                    $deal->is_active = 0;
                    $deal->save();
                }
            }

            $users     = User::where('type', '!=', 'Client')->where('created_by', '=', $this->ownerId())->get();
            $userCount = 0;
            foreach($users as $user)
            {
                $userCount++;
                if($userCount <= $plan->max_users)
                {
                    $user->is_active = 1;
                    $user->save();
                }
                else
                {
                    $user->is_active = 0;
                    $user->save();
                }
            }

            $clients     = User::where('type', '=', 'Client')->where('created_by', '=', $this->ownerId())->get();
            $clientCount = 0;
            foreach($clients as $client)
            {
                $clientCount++;
                if($clientCount <= $plan->max_clients)
                {
                    $client->is_active = 1;
                    $client->save();
                }
                else
                {
                    $client->is_active = 0;
                    $client->save();
                }
            }

            $this->plan = $plan->id;
            if($plan->duration == 'Month')
            {
                $this->plan_expire_date = Carbon::now()->addMonths(1)->isoFormat('YYYY-MM-DD');
            }
            elseif($plan->duration == 'Year')
            {
                $this->plan_expire_date = Carbon::now()->addYears(1)->isoFormat('YYYY-MM-DD');
            }
            else
            {
                $this->plan_expire_date = null;
            }

            $this->save();

            return ['is_success' => true];
        }
        else
        {
            return [
                'is_success' => false,
                'error' => 'Plan is deleted.',
            ];
        }
    }

    public function getUserCount()
    {
        return User::where('created_by', '=', \Auth::user()->id)->count();
    }

    public function getRoleCount()
    {
        return Role::where('created_by', '=', \Auth::user()->id)->count();
    }

    public function userDefaultData()
    {
        $id       = $this->id;
        $pipeline = Pipeline::create(
            [
                'name' => 'Sales',
                'created_by' => $id,
            ]
        );

        $stages = [
            'Initial Contact',
            'Qualification',
            'Meeting',
            'Proposal',
            'Close',
        ];
        foreach($stages as $stage)
        {
            Stage::create(
                [
                    'name' => $stage,
                    'pipeline_id' => $pipeline->id,
                    'created_by' => $id,
                ]
            );
        }

        // Default Lead Stages
        $lead_stages = [
            'Draft',
            'Sent',
            'Open',
            'Revised',
            'Declined',
            'Accepted',
        ];
        foreach($lead_stages as $lead_stage)
        {
            LeadStage::create(
                [
                    'name' => $lead_stage,
                    'pipeline_id' => $pipeline->id,
                    'created_by' => $id,
                ]
            );
        }
        // End Default Lead Stages

        // Label
        $labels = [
            'New Deal' => 'danger',
            'Idea' => 'warning',
            'Appointment' => 'primary',
        ];
        foreach($labels as $label => $color)
        {
            Label::create(
                [
                    'name' => $label,
                    'color' => $color,
                    'pipeline_id' => $pipeline->id,
                    'created_by' => $id,
                ]
            );
        }

        // Source
        $sources = [
            'Website',
            'Organic',
            'Call',
            'Social Media',
            'Email Campaign',
        ];
        foreach($sources as $source)
        {
            Source::create(
                [
                    'name' => $source,
                    'created_by' => $id,
                ]
            );
        }

        // Payment
        $payments = [
            'Cash',
            'Bank',
        ];
        foreach($payments as $payment)
        {
            Payment::create(
                [
                    'name' => $payment,
                    'created_by' => $id,
                ]
            );
        }

        // Expense Category
        $expenseCategories = [
            'Meeting',
            'Product',
            'Repair',
            'Travel',
        ];
        foreach($expenseCategories as $expenseCategory)
        {
            ExpenseCategory::create(
                [
                    'name' => $expenseCategory,
                    'description' => '',
                    'created_by' => $id,
                ]
            );
        }

        // Make Entry In User_Email_Template
        $allEmail = EmailTemplate::all();
        foreach($allEmail as $email)
        {
            UserEmailTemplate::create(
                [
                    'template_id' => $email->id,
                    'user_id' => $id,
                    'is_active' => 1,
                ]
            );
        }
    }

    // For Email template Module
    public function defaultEmail()
    {
        // Email Template
        $emailTemplate = [
            'New User',
            'Assign Deal',
            'Move Deal',
            'Create Task',
            'Assign Lead',
            'Move Lead',
            'Assign Estimation',
            'Contract',
        ];

        foreach($emailTemplate as $eTemp)
        {
            EmailTemplate::create(
                [
                    'name' => $eTemp,
                    'from' => env('APP_NAME'),
                    'created_by' => $this->id,
                ]
            );
        }

        $defaultTemplate = [
            'New User' => [
                'subject' => 'Login Detail',
                'lang' => [
                    'ar' => '<p>????????????&nbsp;<br>?????????? ???? ???? {app_name}.</p><p><b>???????????? ???????????????????? </b>: {email}<br><b>???????? ????????</b> : {password}</p><p>{app_url}</p><p>????????<br>{app_name}</p>',
                    'da' => '<p>Hej,&nbsp;<br>Velkommen til {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Adgangskode</b> : {password}</p><p>{app_url}</p><p>Tak,<br>{app_name}</p>',
                    'de' => '<p>Hallo,&nbsp;<br>Willkommen zu {app_name}.</p><p><b>Email </b>: {email}<br><b>Passwort</b> : {password}</p><p>{app_url}</p><p>Vielen Dank,<br>{app_name}</p>',
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                    'es' => '<p>Hola,&nbsp;<br>Bienvenido a {app_name}.</p><p><b>Correo electr??nico </b>: {email}<br><b>Contrase??a</b> : {password}</p><p>{app_url}</p><p>Gracias,<br>{app_name}</p>',
                    'fr' => '<p>Bonjour,&nbsp;<br>Bienvenue ?? {app_name}.</p><p><b>Email </b>: {email}<br><b>Mot de passe</b> : {password}</p><p>{app_url}</p><p>Merci,<br>{app_name}</p>',
                    'it' => '<p>Ciao,&nbsp;<br>Benvenuto a {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Parola d\'ordine</b> : {password}</p><p>{app_url}</p><p>Grazie,<br>{app_name}</p>',
                    'ja' => '<p>??????????????????&nbsp;<br>??????????????? {app_name}.</p><p><b>E????????? </b>: {email}<br><b>???????????????</b> : {password}</p><p>{app_url}</p><p>???????????????<br>{app_name}</p>',
                    'nl' => '<p>Hallo,&nbsp;<br>Welkom bij {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Wachtwoord</b> : {password}</p><p>{app_url}</p><p>Bedankt,<br>{app_name}</p>',
                    'pl' => '<p>Witaj,&nbsp;<br>Witamy w {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Has??o</b> : {password}</p><p>{app_url}</p><p>Dzi??ki,<br>{app_name}</p>',
                    'ru' => '<p>????????????,&nbsp;<br>?????????? ???????????????????? ?? {app_name}.</p><p><b>?????????????????????? ???????????? </b>: {email}<br><b>????????????</b> : {password}</p><p>{app_url}</p><p>??????????????,<br>{app_name}</p>',
                    'pt' => '<p>Ol??, &nbsp;<br>Bem-vindo a {app_name}.</p><p><b>Email </b>: {email}<br><b>Senha</b> : {password}</p><p>{app_url}</p><p>Obrigado,<br>{app_name}</p>',
                ],
            ],
            'Assign Deal' => [
                'subject' => 'New Deal Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????????? ???????? ?????????? ????.</span></p><p><span style="font-family: sans-serif;"><b>?????? ????????????</b> : {deal_name}<br><b>???? ???????????? ????????????</b> : {deal_pipeline}<br><b>?????????? ????????????</b> : {deal_stage}<br><b>???????? ????????????</b> : {deal_status}<br><b>?????? ????????????</b> : {deal_price}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Deal Navn</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Fase</b> : {deal_stage}<br><b>Deal status</b> : {deal_status}<br><b>Deal pris</b> : {deal_price}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Gesch??ftsname</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Deal Status</b> : {deal_status}<br><b>Ausgehandelter Preis</b> : {deal_price}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Deal Name</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Deal Status</b> : {deal_status}<br><b>Deal Price</b> : {deal_price}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal ha sido asignado a usted.</span></p><p><span style="font-family: sans-serif;"><b>Nombre del trato</b> : {deal_name}<br><b>Tuber??a de reparto</b> : {deal_pipeline}<br><b>Etapa de reparto</b> : {deal_stage}<br><b>Estado del acuerdo</b> : {deal_status}<br><b>Precio de oferta</b> : {deal_price}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Le New Deal vous a ??t?? attribu??.</span></p><p><span style="font-family: sans-serif;"><b>Nom de l\'accord</b> : {deal_name}<br><b>Pipeline de transactions</b> : {deal_pipeline}<br><b>??tape de l\'op??ration</b> : {deal_stage}<br><b>Statut de l\'accord</b> : {deal_status}<br><b>Prix ??????de l\'offre</b> : {deal_price}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal ?? stato assegnato a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome dell\'affare</b> : {deal_name}<br><b>Pipeline di offerte</b> : {deal_pipeline}<br><b>Stage Deal</b> : {deal_stage}<br><b>Stato dell\'affare</b> : {deal_status}<br><b>Prezzo dell\'offerta</b> : {deal_price}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">????????????????????????????????????????????????</span></p><p><span style="font-family: sans-serif;"><b>?????????</b> : {deal_name}<br><b>????????????????????????</b> : {deal_pipeline}<br><b>??????????????????</b> : {deal_stage}<br><b>????????????</b> : {deal_status}<br><b>????????????</b> : {deal_price}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Dealnaam</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Dealstatus</b> : {deal_status}<br><b>Deal prijs</b> : {deal_price}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowa oferta zosta??a Ci przypisana.</span></p><p><span style="font-family: sans-serif;"><b>Nazwa oferty</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Etap transakcji</b> : {deal_stage}<br><b>Status oferty</b> : {deal_status}<br><b>Cena oferty</b> : {deal_price}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?????????? ???????? ?????? ???????????????? ??????.</span></p><p><span style="font-family: sans-serif;"><b>???????????????? ????????????</b> : {deal_name}<br><b>?????????????????????? ????????????</b> : {deal_pipeline}<br><b>???????? ????????????</b> : {deal_stage}<br><b>???????????? ????????????</b> : {deal_status}<br><b>???????? ????????????</b> : {deal_price}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal foi Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Deal Name</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Est??gio Deal</b> : {deal_stage}<br><b>Status do Deal</b> : {deal_status}<br><b>Pre??o de Deal</b> : {deal_price}</span></p>',
                ],
            ],
            'Move Deal' => [
                'subject' => 'Deal has been Moved',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????? ???????? ???? {deal_old_stage} ??????&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">?????? ????????????</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">???? ???????????? ????????????</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">?????????? ????????????</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">???????? ????????????</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">?????? ????????????</span>&nbsp;: {deal_price}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">En aftale er flyttet fra {deal_old_stage} til&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Deal Navn</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Fase</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal pris</span>&nbsp;: {deal_price}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ein Deal wurde verschoben {deal_old_stage} zu&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Gesch??ftsname</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal Status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Ausgehandelter Preis</span>&nbsp;: {deal_price}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Deal has been move from {deal_old_stage} to&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Deal Name</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal Status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal Price</span>&nbsp;: {deal_price}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se ha movido un acuerdo de {deal_old_stage} a&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nombre del trato</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Tuber??a de reparto</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Etapa de reparto</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Estado del acuerdo</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Precio de oferta</span>&nbsp;: {deal_price}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un accord a ??t?? d??plac?? de {deal_old_stage} ??&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nom de l\'accord</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Pipeline de transactions</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">??tape de l\'op??ration</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Statut de l\'accord</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Prix ??????de l\'offre</span>&nbsp;: {deal_price}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un affare ?? stato spostato da {deal_old_stage} per&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nome dell\'affare</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Pipeline di offerte</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Stage Deal</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Stato dell\'affare</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Prezzo dell\'offerta</span>&nbsp;: {deal_price}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">????????? {deal_old_stage} ???&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">?????????</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">????????????????????????</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">??????????????????</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">????????????</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">????????????</span>&nbsp;: {deal_price}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Een deal is verplaatst van {deal_old_stage} naar&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Dealnaam</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Dealstatus</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal prijs</span>&nbsp;: {deal_price}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Umowa zosta??a przeniesiona {deal_old_stage} do&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nazwa oferty</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Etap transakcji</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Status oferty</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Cena oferty</span>&nbsp;: {deal_price}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???????????? ???????? ???????????????????? ???? {deal_old_stage} ??&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">???????????????? ????????????</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">?????????????????????? ????????????</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">???????? ????????????</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">???????????? ????????????</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">???????? ????????????</span>&nbsp;: {deal_price}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Um Deal tem sido move-se de {deal_old_stage} para &nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nome do Deal</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Est??gio Deal</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Status do Deal</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Pre??o Deal</span>&nbsp;: {deal_price}</span></p>',
                ],
            ],
            'Create Task' => [
                'subject' => 'New Task Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????????? ???????? ?????????? ????.</span></p><p><span style="font-family: sans-serif;"><b>?????? ????????????</b> : {task_name}<br><b>???????????? ????????????</b> : {task_priority}<br><b>???????? ????????????</b> : {task_status}<br><b>???????? ????????????</b> : {deal_name}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny opgave er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Opgavens navn</b> : {task_name}<br><b>Opgaveprioritet</b> : {task_priority}<br><b>Opgavestatus</b> : {task_status}<br><b>Opgave</b> : {deal_name}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neue Aufgabe wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Aufgabennname</b> : {task_name}<br><b>Aufgabenpriorit??t</b> : {task_priority}<br><b>Aufgabenstatus</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Task has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Task Name</b> : {task_name}<br><b>Task Priority</b> : {task_priority}<br><b>Task Status</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nueva tarea ha sido asignada a usted.</span></p><p><span style="font-family: sans-serif;"><b>Nombre de la tarea</b> : {task_name}<br><b>Prioridad de tarea</b> : {task_priority}<br><b>Estado de la tarea</b> : {task_status}<br><b>Reparto de tarea</b> : {deal_name}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Une nouvelle t??che vous a ??t?? assign??e.</span></p><p><span style="font-family: sans-serif;"><b>Nom de la t??che</b> : {task_name}<br><b>Priorit?? des t??ches</b> : {task_priority}<br><b>Statut de la t??che</b> : {task_status}<br><b>Deal Task</b> : {deal_name}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">La nuova attivit?? ?? stata assegnata a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome dell\'attivit??</b> : {task_name}<br><b>Priorit?? dell\'attivit??</b> : {task_priority}<br><b>Stato dell\'attivit??</b> : {task_status}<br><b>Affare</b> : {deal_name}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???????????????????????????????????????????????????</span></p><p><span style="font-family: sans-serif;"><b>????????????</b> : {task_name}<br><b>?????????????????????</b> : {task_priority}<br><b>???????????????????????????</b> : {task_status}<br><b>???????????????</b> : {deal_name}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe taak is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Opdrachtnaam</b> : {task_name}<br><b>Taakprioriteit</b> : {task_priority}<br><b>Taakstatus</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowe zadanie zosta??o Ci przypisane.</span></p><p><span style="font-family: sans-serif;"><b>Nazwa zadania</b> : {task_name}<br><b>Priorytet zadania</b> : {task_priority}<br><b>Status zadania</b> : {task_status}<br><b>Zadanie Deal</b> : {deal_name}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?????????? ???????????? ???????? ?????????????????? ??????.</span></p><p><span style="font-family: sans-serif;"><b>???????????????? ????????????</b> : {task_name}<br><b>?????????????????? ????????????</b> : {task_priority}<br><b>?????????????????? ????????????</b> : {task_status}<br><b>????????????</b> : {deal_name}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Tarefa foi Designar para voc??.</span></p><p><span style="font-family: sans-serif;"><b>Nome da Tarefa</b> : {task_name}<br><b>Prioridade Tarefa</b> : {task_priority}<br><b>Status da tarefa</b> : {task_status}<br><b>Deal de tarefas</b> : {deal_name}</span></p>',
                ],
            ],
            'Assign Lead' => [
                'subject' => 'New Lead Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????????? ???????? ???????? ????.</span></p><p><span style="font-family: sans-serif;"><b>?????? ???????????? ??????????????</b> : {lead_name}<br><b>???????????? ???????????????????? ??????????????</b> : {lead_email}<br><b>???? ???????????? ????????????</b> : {lead_pipeline}<br><b>?????????? ????????????</b> : {lead_stage}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny bly er blevet tildelt dig.</span></p><p><span style="font-family: sans-serif;"><b>Blynavn</b> : {lead_name}<br><b>Lead-e-mail</b> : {lead_email}<br><b>Blyr??rledning</b> : {lead_pipeline}<br><b>Lead scenen</b> : {lead_stage}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neuer Lead wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Lead Name</b> : {lead_name}<br><b>Lead-E-Mail</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Lead has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Lead Name</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se le ha asignado un nuevo plomo.</span></p><p><span style="font-family: sans-serif;"><b>Nombre principal</b> : {lead_name}<br><b>Correo electr??nico principal</b> : {lead_email}<br><b>Tuber??a de plomo</b> : {lead_pipeline}<br><b>Etapa de plomo</b> : {lead_stage}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un nouveau prospect vous a ??t?? attribu??.</span></p><p><span style="font-family: sans-serif;"><b>Nom du responsable</b> : {lead_name}<br><b>Courriel principal</b> : {lead_email}<br><b>Pipeline de plomb</b> : {lead_pipeline}<br><b>??tape principale</b> : {lead_stage}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Lead ?? stato assegnato a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome del lead</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Conduttura di piombo</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???????????????????????????????????????????????????</span></p><p><span style="font-family: sans-serif;"><b>????????????</b> : {lead_name}<br><b>??????????????????</b> : {lead_email}<br><b>???????????????????????????</b> : {lead_pipeline}<br><b>?????????????????????</b> : {lead_stage}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe lead is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Lead naam</b> : {lead_name}<br><b>E-mail leiden</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Hoofdfase</b> : {lead_stage}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowy potencjalny klient zosta?? do ciebie przypisany.</span></p><p><span style="font-family: sans-serif;"><b>Imi?? i nazwisko</b> : {lead_name}<br><b>G????wny adres e-mail</b> : {lead_email}<br><b>O????w ruroci??gu</b> : {lead_pipeline}<br><b>Etap prowadz??cy</b> : {lead_stage}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?????????? ?????? ?????? ???????????????? ??????.</span></p><p><span style="font-family: sans-serif;"><b>?????? ????????????</b> : {lead_name}<br><b>?????????????? Email</b> : {lead_email}<br><b>?????????????? ??????????????????????</b> : {lead_pipeline}<br><b>?????????????? ????????</b> : {lead_stage}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Lead foi Designar para voc??.</span></p><p><span style="font-family: sans-serif;"><b>Nome do Lead</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Est??gio de Lead</b> : {lead_stage}</span></p>',
                ],
            ],
            'Move Lead' => [
                'subject' => 'Lead has been Moved',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????? ???????????? ?????????????? ???? {lead_old_stage} ??????&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">?????? ???????????? ??????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">???????????? ???????????????????? ??????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">???? ???????????? ????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">?????????? ????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">En leder er flyttet fra {lead_old_stage} til&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Blynavn</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead-e-mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Blyr??rledning</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead scenen</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ein Lead wurde verschoben von {lead_old_stage} zu&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead Name</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead-E-Mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Lead has been move from {lead_old_stage} to&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead Name</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un plomo ha sido movido de {lead_old_stage} a&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nombre principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Correo electr??nico principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Tuber??a de plomo</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Etapa de plomo</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un lead a ??t?? d??plac?? de {lead_old_stage} ??&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nom du responsable</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Courriel principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Pipeline de plomb</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">??tape principale</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?? stato spostato un lead {lead_old_stage} per&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nome del lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Conduttura di piombo</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?????????????????????????????? {lead_old_stage} ???&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">??????????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">???????????????????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">?????????????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Er is een lead verplaatst van {lead_old_stage} naar&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead naam</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">E-mail leiden</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Hoofdfase</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Prowadzenie zosta??o przeniesione {lead_old_stage} do&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Imi?? i nazwisko</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">G????wny adres e-mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">O????w ruroci??gu</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Etap prowadz??cy</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???????????? ?????? ?????????????????? ???? {lead_old_stage} ??&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">?????? ????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">?????????????? Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">?????????????? ??????????????????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">?????????????? ????????</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Lead foi mover-se de {lead_old_stage} para &nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nome do Lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Est??gio de Lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                ],
            ],
            'Assign Estimation' => [
                'subject' => 'New Estimation Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">???? ?????????? ?????????? ???????? ????.</span></p><p><span style="font-family: sans-serif;"><b>???????? ??????????????</b>: {estimation_name}<br><b>???????? ??????????</b> : {estimation_client}<br><b>???????? ??????????????</b> : {estimation_status}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny estimering er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Estimations-id</b>: {estimation_name}<br><b>Estimeringsklient</b> : {estimation_client}<br><b>Estimeringsstatus</b> : {estimation_status}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neue Sch??tzung wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Sch??tz-Id</b>: {estimation_name}<br><b>Sch??tzungs-Client</b> : {estimation_client}<br><b>Sch??tzungsstatus</b> : {estimation_status}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Estimation has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Estimation Id</b>: {estimation_name}<br><b>Estimation Client</b> : {estimation_client}<br><b>Estimation Status</b> : {estimation_status}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se le ha asignado una nueva estimaci??n.</span></p><p><span style="font-family: sans-serif;"><b>ID de estimaci??n</b>: {estimation_name}<br><b>Cliente de Estimaci??n</b> : {estimation_client}<br><b>Estado de estimaci??n</b> : {estimation_status}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Une nouvelle estimation vous a ??t?? attribu??e.</span></p><p><span style="font-family: sans-serif;"><b>Identifiant d\'estimation</b>: {estimation_name}<br><b>Client d\'estimation</b> : {estimation_client}<br><b>Statut d\'estimation</b> : {estimation_status}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">La nuova stima ?? stata assegnata a te.</span></p><p><span style="font-family: sans-serif;"><b>ID stima</b>: {estimation_name}<br><b>Cliente di stima</b> : {estimation_client}<br><b>Stato della stima</b> : {estimation_status}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">??????????????????</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">??????????????????????????????????????????????????????</span></p><p><span style="font-family: sans-serif;"><b>????????????ID</b>: {estimation_name}<br><b>??????????????????????????????</b> : {estimation_client}<br><b>??????????????????</b> : {estimation_status}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe schatting is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Schattings-ID</b>: {estimation_name}<br><b>Schatting Client</b> : {estimation_client}<br><b>Schattingsstatus</b> : {estimation_status}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowe oszacowanie zosta??o Ci przypisane.</span></p><p><span style="font-family: sans-serif;"><b>Identyfikator szacunku</b>: {estimation_name}<br><b>Oszacowanie klienta</b> : {estimation_client}<br><b>Status oszacowania</b> : {estimation_status}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">????????????,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">?????????? ???????????? ???????? ?????????????????? ??????.</span></p><p><span style="font-family: sans-serif;"><b>?????????????????????????? ????????????</b>: {estimation_name}<br><b>???????????? ??????????????</b> : {estimation_client}<br><b>???????????? ????????????</b> : {estimation_status}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Ol??,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Estimation foi Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Estimation Id</b>: {estimation_name}<br><b>Estimation Client</b> : {estimation_client}<br><b>Status da Estimation</b> : {estimation_status}</span></p>',
                ],
            ],
            'Contract' => [
                'subject' => 'Contract',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;"><strong>?????????? </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong><strong>?????????? :</strong> </strong>{ contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>?????????? ??????????</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>?????????? ????????????????</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">?????????? ?????????? ??????. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Regards Regards ?? </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;"><strong>Hej </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Aftaleemne:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart-dato</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Slutdato</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">Ser frem til at h??re fra dig. </span></p>
                    <p><strong><span style="font-family: sans-serif;">K??rlig hilsen </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;"><strong><strong> </strong></strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Vertragssubjekt:</strong> {contract_subject} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>tart-Datum</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>: </strong>{contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Freuen Sie sich auf die von Ihnen zu h&ouml;renden Informationen. </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">-Regards, </span></span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;"><strong>Hi </strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Contract Subject:</strong> {contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>End Date</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Looking forward to hear from you. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Kind Regards, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;"><strong><strong>Hi </strong></strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong><strong>de contrato:</strong> </strong>{contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">S</span></span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Fecha de finalizaci&oacute;n</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Con ganas de escuchar de usted. </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">Regards de tipo, </span></span></strong></p>
                    <p><span style="font-family: sans-serif;">{contract_name}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;"><strong><strong> </strong></strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Objet du contrat:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>Date de d&eacute;but</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Date de fin</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">Vous avez h&acirc;te de vous entendre. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Kind Regards </span> </strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;"><strong>Ciao </strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Oggetto Contratto:</strong> {contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>Data tarte</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Data di fine</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Non vedo lora di sentire da te. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Kind indipendentemente, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;"><span style="font-family: sans-serif;"><strong>?????? </strong>{contract_client} </span></span></p>
                    <p><span style="font-family: sans-serif;"><strong>????????????:</strong> {????????????} </span></p>
                    <p><strong><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: </span></strong><span style="font-family: sans-serif;">{contract_start_date}</span><span style="font-family: sans-serif;"> </span></p>
                    <p><span style="font-family: sans-serif;"><strong>?????????</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">??????????????????????????????????????????????????? </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">???????????????????????? </span></span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;"><strong>Hi </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Contractonderwerp:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Einddatum</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">Ik kijk ernaar uit om van u te horen. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Soort Regards, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;"><strong>Hi </strong>{contract_client}</span></p>
                    <p><span style="font-family: sans-serif;"><strong>Temat umowy:</strong> {contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">S</span></span></strong><span style="font-family: sans-serif;"><strong>data tartu</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Data zako??czenia</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Nie mo??na si?? doczeka??, aby us??ysze?? od u??ytkownika. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Regaty typu, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;"><strong>???????????? </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>???????? ????????????????:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>???????? ??????????????</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>???????? ??????????????????</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">?? ?????????????????????? ???????????? ???????????????? ???? ??????. </span></p>
                    <p><strong><span style="font-family: sans-serif;">?????????? ????????, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;"><strong>Oi </strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Assunto do Contrato:</strong> {contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Data de t??rmino</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Olhando para a frente para ouvir de voc??. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Kind Considerar, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                ],
            ],
        ];

        $email = EmailTemplate::all();

        foreach($email as $e)
        {
            foreach($defaultTemplate[$e->name]['lang'] as $lang => $content)
            {
                EmailTemplateLang::create(
                    [
                        'parent_id' => $e->id,
                        'lang' => $lang,
                        'subject' => $defaultTemplate[$e->name]['subject'],
                        'content' => $content,
                        'from' => env('APP_NAME'),
                    ]
                );
            }
        }
    }

    // End Email template Module

    public function makeEmployeeRole()
    {
        $userRole        = Role::create(
            [
                'name' => 'Employee',
                'created_by' => $this->id,
            ]
        );
        $userPermissions = [
            'Manage Deals',
            'Create Deal',
            'Edit Deal',
            'Delete Deal',
            'Move Deal',
            'View Deal',
            'Manage Leads',
            'Create Lead',
            'Edit Lead',
            'Delete Lead',
            'View Lead',
            'Move Lead',
            'Manage Tasks',
            'Create Task',
            'Edit Task',
            'Delete Task',
            'View Task',
            'Manage Invoices',
            'Create Invoice',
            'Edit Invoice',
            'Delete Invoice',
            "View Invoice",
            'Manage Products',
            'Create Product',
            'Edit Product',
            'Delete Product',
            'Manage Expenses',
            'Create Expense',
            'Edit Expense',
            'Delete Expense',
            'Manage Taxes',
            'Create Tax',
            'Edit Tax',
            'Delete Tax',
            'Manage Invoice Payments',
            'Create Invoice Payment',
            'Invoice Add Product',
            'Invoice Delete Product',
            'Invoice Edit Product',
            'Convert Lead To Deal',
        ];
        foreach($userPermissions as $ap)
        {
            $permission = Permission::findByName($ap);
            $userRole->givePermissionTo($permission);
        }
    }

    public function notifications()
    {
        return Notification::where('user_id', '=', \Auth::user()->id)->where('is_read', '=', 0)->orderBy('id', 'desc')->get();
    }

    public function unread()
    {
        return Message::where('from', '=', $this->id)->where('is_read', '=', 0)->count();
    }

    public function mdfs()
    {
        return $this->hasMany('App\Models\Mdf', 'user_id', 'id');
    }
}
