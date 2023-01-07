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
                    'ar' => '<p>مرحبا،&nbsp;<br>مرحبا بك في {app_name}.</p><p><b>البريد الإلكتروني </b>: {email}<br><b>كلمه السر</b> : {password}</p><p>{app_url}</p><p>شكر،<br>{app_name}</p>',
                    'da' => '<p>Hej,&nbsp;<br>Velkommen til {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Adgangskode</b> : {password}</p><p>{app_url}</p><p>Tak,<br>{app_name}</p>',
                    'de' => '<p>Hallo,&nbsp;<br>Willkommen zu {app_name}.</p><p><b>Email </b>: {email}<br><b>Passwort</b> : {password}</p><p>{app_url}</p><p>Vielen Dank,<br>{app_name}</p>',
                    'en' => '<p>Hello,&nbsp;<br>Welcome to {app_name}.</p><p><b>Email </b>: {email}<br><b>Password</b> : {password}</p><p>{app_url}</p><p>Thanks,<br>{app_name}</p>',
                    'es' => '<p>Hola,&nbsp;<br>Bienvenido a {app_name}.</p><p><b>Correo electrónico </b>: {email}<br><b>Contraseña</b> : {password}</p><p>{app_url}</p><p>Gracias,<br>{app_name}</p>',
                    'fr' => '<p>Bonjour,&nbsp;<br>Bienvenue à {app_name}.</p><p><b>Email </b>: {email}<br><b>Mot de passe</b> : {password}</p><p>{app_url}</p><p>Merci,<br>{app_name}</p>',
                    'it' => '<p>Ciao,&nbsp;<br>Benvenuto a {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Parola d\'ordine</b> : {password}</p><p>{app_url}</p><p>Grazie,<br>{app_name}</p>',
                    'ja' => '<p>こんにちは、&nbsp;<br>へようこそ {app_name}.</p><p><b>Eメール </b>: {email}<br><b>パスワード</b> : {password}</p><p>{app_url}</p><p>おかげで、<br>{app_name}</p>',
                    'nl' => '<p>Hallo,&nbsp;<br>Welkom bij {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Wachtwoord</b> : {password}</p><p>{app_url}</p><p>Bedankt,<br>{app_name}</p>',
                    'pl' => '<p>Witaj,&nbsp;<br>Witamy w {app_name}.</p><p><b>E-mail </b>: {email}<br><b>Hasło</b> : {password}</p><p>{app_url}</p><p>Dzięki,<br>{app_name}</p>',
                    'ru' => '<p>Привет,&nbsp;<br>Добро пожаловать в {app_name}.</p><p><b>Электронное письмо </b>: {email}<br><b>пароль</b> : {password}</p><p>{app_url}</p><p>Спасибо,<br>{app_name}</p>',
                    'pt' => '<p>Olá, &nbsp;<br>Bem-vindo a {app_name}.</p><p><b>Email </b>: {email}<br><b>Senha</b> : {password}</p><p>{app_url}</p><p>Obrigado,<br>{app_name}</p>',
                ],
            ],
            'Assign Deal' => [
                'subject' => 'New Deal Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم تعيين صفقة جديدة لك.</span></p><p><span style="font-family: sans-serif;"><b>اسم الصفقة</b> : {deal_name}<br><b>خط أنابيب الصفقة</b> : {deal_pipeline}<br><b>مرحلة الصفقة</b> : {deal_stage}<br><b>حالة الصفقة</b> : {deal_status}<br><b>سعر الصفقة</b> : {deal_price}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Deal Navn</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Fase</b> : {deal_stage}<br><b>Deal status</b> : {deal_status}<br><b>Deal pris</b> : {deal_price}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Geschäftsname</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Deal Status</b> : {deal_status}<br><b>Ausgehandelter Preis</b> : {deal_price}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Deal Name</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Deal Status</b> : {deal_status}<br><b>Deal Price</b> : {deal_price}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal ha sido asignado a usted.</span></p><p><span style="font-family: sans-serif;"><b>Nombre del trato</b> : {deal_name}<br><b>Tubería de reparto</b> : {deal_pipeline}<br><b>Etapa de reparto</b> : {deal_stage}<br><b>Estado del acuerdo</b> : {deal_status}<br><b>Precio de oferta</b> : {deal_price}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Le New Deal vous a été attribué.</span></p><p><span style="font-family: sans-serif;"><b>Nom de l\'accord</b> : {deal_name}<br><b>Pipeline de transactions</b> : {deal_pipeline}<br><b>Étape de l\'opération</b> : {deal_stage}<br><b>Statut de l\'accord</b> : {deal_status}<br><b>Prix ​​de l\'offre</b> : {deal_price}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal è stato assegnato a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome dell\'affare</b> : {deal_name}<br><b>Pipeline di offerte</b> : {deal_pipeline}<br><b>Stage Deal</b> : {deal_stage}<br><b>Stato dell\'affare</b> : {deal_status}<br><b>Prezzo dell\'offerta</b> : {deal_price}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">新しい取引が割り当てられました。</span></p><p><span style="font-family: sans-serif;"><b>取引名</b> : {deal_name}<br><b>取引パイプライン</b> : {deal_pipeline}<br><b>取引ステージ</b> : {deal_stage}<br><b>取引状況</b> : {deal_status}<br><b>取引価格</b> : {deal_price}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Dealnaam</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Deal Stage</b> : {deal_stage}<br><b>Dealstatus</b> : {deal_status}<br><b>Deal prijs</b> : {deal_price}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowa oferta została Ci przypisana.</span></p><p><span style="font-family: sans-serif;"><b>Nazwa oferty</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Etap transakcji</b> : {deal_stage}<br><b>Status oferty</b> : {deal_status}<br><b>Cena oferty</b> : {deal_price}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Новый курс был назначен вам.</span></p><p><span style="font-family: sans-serif;"><b>Название сделки</b> : {deal_name}<br><b>Трубопровод сделки</b> : {deal_pipeline}<br><b>Этап сделки</b> : {deal_stage}<br><b>Статус сделки</b> : {deal_status}<br><b>Цена сделки</b> : {deal_price}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Deal foi Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Deal Name</b> : {deal_name}<br><b>Deal Pipeline</b> : {deal_pipeline}<br><b>Estágio Deal</b> : {deal_stage}<br><b>Status do Deal</b> : {deal_status}<br><b>Preço de Deal</b> : {deal_price}</span></p>',
                ],
            ],
            'Move Deal' => [
                'subject' => 'Deal has been Moved',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم نقل صفقة من {deal_old_stage} إلى&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">اسم الصفقة</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">خط أنابيب الصفقة</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">مرحلة الصفقة</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">حالة الصفقة</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">سعر الصفقة</span>&nbsp;: {deal_price}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">En aftale er flyttet fra {deal_old_stage} til&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Deal Navn</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Fase</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal pris</span>&nbsp;: {deal_price}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ein Deal wurde verschoben {deal_old_stage} zu&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Geschäftsname</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal Status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Ausgehandelter Preis</span>&nbsp;: {deal_price}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Deal has been move from {deal_old_stage} to&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Deal Name</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Deal Status</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal Price</span>&nbsp;: {deal_price}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se ha movido un acuerdo de {deal_old_stage} a&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nombre del trato</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Tubería de reparto</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Etapa de reparto</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Estado del acuerdo</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Precio de oferta</span>&nbsp;: {deal_price}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un accord a été déplacé de {deal_old_stage} à&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nom de l\'accord</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Pipeline de transactions</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Étape de l\'opération</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Statut de l\'accord</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Prix ​​de l\'offre</span>&nbsp;: {deal_price}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un affare è stato spostato da {deal_old_stage} per&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nome dell\'affare</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Pipeline di offerte</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Stage Deal</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Stato dell\'affare</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Prezzo dell\'offerta</span>&nbsp;: {deal_price}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">取引は {deal_old_stage} に&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">取引名</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">取引パイプライン</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">取引ステージ</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">取引状況</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">取引価格</span>&nbsp;: {deal_price}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Een deal is verplaatst van {deal_old_stage} naar&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Dealnaam</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Deal Stage</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Dealstatus</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Deal prijs</span>&nbsp;: {deal_price}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Umowa została przeniesiona {deal_old_stage} do&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nazwa oferty</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Etap transakcji</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Status oferty</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Cena oferty</span>&nbsp;: {deal_price}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Сделка была перемещена из {deal_old_stage} в&nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Название сделки</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Трубопровод сделки</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Этап сделки</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Статус сделки</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Цена сделки</span>&nbsp;: {deal_price}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Um Deal tem sido move-se de {deal_old_stage} para &nbsp; {deal_new_stage}.</span></p><p><span style="font-family: sans-serif;"><span style="font-weight: bolder;">Nome do Deal</span>&nbsp;: {deal_name}<br><span style="font-weight: bolder;">Deal Pipeline</span>&nbsp;: {deal_pipeline}<br><span style="font-weight: bolder;">Estágio Deal</span>&nbsp;: {deal_stage}<br><span style="font-weight: bolder;">Status do Deal</span>&nbsp;: {deal_status}<br><span style="font-weight: bolder;">Preço Deal</span>&nbsp;: {deal_price}</span></p>',
                ],
            ],
            'Create Task' => [
                'subject' => 'New Task Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم تعيين مهمة جديدة لك.</span></p><p><span style="font-family: sans-serif;"><b>اسم المهمة</b> : {task_name}<br><b>أولوية المهمة</b> : {task_priority}<br><b>حالة المهمة</b> : {task_status}<br><b>صفقة المهمة</b> : {deal_name}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny opgave er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Opgavens navn</b> : {task_name}<br><b>Opgaveprioritet</b> : {task_priority}<br><b>Opgavestatus</b> : {task_status}<br><b>Opgave</b> : {deal_name}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neue Aufgabe wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Aufgabennname</b> : {task_name}<br><b>Aufgabenpriorität</b> : {task_priority}<br><b>Aufgabenstatus</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Task has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Task Name</b> : {task_name}<br><b>Task Priority</b> : {task_priority}<br><b>Task Status</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nueva tarea ha sido asignada a usted.</span></p><p><span style="font-family: sans-serif;"><b>Nombre de la tarea</b> : {task_name}<br><b>Prioridad de tarea</b> : {task_priority}<br><b>Estado de la tarea</b> : {task_status}<br><b>Reparto de tarea</b> : {deal_name}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Une nouvelle tâche vous a été assignée.</span></p><p><span style="font-family: sans-serif;"><b>Nom de la tâche</b> : {task_name}<br><b>Priorité des tâches</b> : {task_priority}<br><b>Statut de la tâche</b> : {task_status}<br><b>Deal Task</b> : {deal_name}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">La nuova attività è stata assegnata a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome dell\'attività</b> : {task_name}<br><b>Priorità dell\'attività</b> : {task_priority}<br><b>Stato dell\'attività</b> : {task_status}<br><b>Affare</b> : {deal_name}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">新しいタスクが割り当てられました。</span></p><p><span style="font-family: sans-serif;"><b>タスク名</b> : {task_name}<br><b>タスクの優先度</b> : {task_priority}<br><b>タスクのステータス</b> : {task_status}<br><b>タスク取引</b> : {deal_name}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe taak is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Opdrachtnaam</b> : {task_name}<br><b>Taakprioriteit</b> : {task_priority}<br><b>Taakstatus</b> : {task_status}<br><b>Task Deal</b> : {deal_name}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowe zadanie zostało Ci przypisane.</span></p><p><span style="font-family: sans-serif;"><b>Nazwa zadania</b> : {task_name}<br><b>Priorytet zadania</b> : {task_priority}<br><b>Status zadania</b> : {task_status}<br><b>Zadanie Deal</b> : {deal_name}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Новая задача была назначена вам.</span></p><p><span style="font-family: sans-serif;"><b>Название задачи</b> : {task_name}<br><b>Приоритет задачи</b> : {task_priority}<br><b>Состояние задачи</b> : {task_status}<br><b>Задача</b> : {deal_name}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Tarefa foi Designar para você.</span></p><p><span style="font-family: sans-serif;"><b>Nome da Tarefa</b> : {task_name}<br><b>Prioridade Tarefa</b> : {task_priority}<br><b>Status da tarefa</b> : {task_status}<br><b>Deal de tarefas</b> : {deal_name}</span></p>',
                ],
            ],
            'Assign Lead' => [
                'subject' => 'New Lead Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم تعيين عميل جديد لك.</span></p><p><span style="font-family: sans-serif;"><b>اسم العميل المحتمل</b> : {lead_name}<br><b>البريد الإلكتروني الرئيسي</b> : {lead_email}<br><b>خط أنابيب الرصاص</b> : {lead_pipeline}<br><b>مرحلة الرصاص</b> : {lead_stage}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny bly er blevet tildelt dig.</span></p><p><span style="font-family: sans-serif;"><b>Blynavn</b> : {lead_name}<br><b>Lead-e-mail</b> : {lead_email}<br><b>Blyrørledning</b> : {lead_pipeline}<br><b>Lead scenen</b> : {lead_stage}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neuer Lead wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Lead Name</b> : {lead_name}<br><b>Lead-E-Mail</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Lead has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Lead Name</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se le ha asignado un nuevo plomo.</span></p><p><span style="font-family: sans-serif;"><b>Nombre principal</b> : {lead_name}<br><b>Correo electrónico principal</b> : {lead_email}<br><b>Tubería de plomo</b> : {lead_pipeline}<br><b>Etapa de plomo</b> : {lead_stage}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un nouveau prospect vous a été attribué.</span></p><p><span style="font-family: sans-serif;"><b>Nom du responsable</b> : {lead_name}<br><b>Courriel principal</b> : {lead_email}<br><b>Pipeline de plomb</b> : {lead_pipeline}<br><b>Étape principale</b> : {lead_stage}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Lead è stato assegnato a te.</span></p><p><span style="font-family: sans-serif;"><b>Nome del lead</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Conduttura di piombo</b> : {lead_pipeline}<br><b>Lead Stage</b> : {lead_stage}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">新しいリードが割り当てられました。</span></p><p><span style="font-family: sans-serif;"><b>リード名</b> : {lead_name}<br><b>リードメール</b> : {lead_email}<br><b>リードパイプライン</b> : {lead_pipeline}<br><b>リードステージ</b> : {lead_stage}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe lead is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Lead naam</b> : {lead_name}<br><b>E-mail leiden</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Hoofdfase</b> : {lead_stage}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowy potencjalny klient został do ciebie przypisany.</span></p><p><span style="font-family: sans-serif;"><b>Imię i nazwisko</b> : {lead_name}<br><b>Główny adres e-mail</b> : {lead_email}<br><b>Ołów rurociągu</b> : {lead_pipeline}<br><b>Etap prowadzący</b> : {lead_stage}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Новый Лид был назначен вам.</span></p><p><span style="font-family: sans-serif;"><b>Имя лидера</b> : {lead_name}<br><b>Ведущий Email</b> : {lead_email}<br><b>Ведущий трубопровод</b> : {lead_pipeline}<br><b>Ведущий этап</b> : {lead_stage}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Lead foi Designar para você.</span></p><p><span style="font-family: sans-serif;"><b>Nome do Lead</b> : {lead_name}<br><b>Lead Email</b> : {lead_email}<br><b>Lead Pipeline</b> : {lead_pipeline}<br><b>Estágio de Lead</b> : {lead_stage}</span></p>',
                ],
            ],
            'Move Lead' => [
                'subject' => 'Lead has been Moved',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم نقل العميل المحتمل من {lead_old_stage} إلى&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">اسم العميل المحتمل</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">البريد الإلكتروني الرئيسي</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">خط أنابيب الرصاص</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">مرحلة الرصاص</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">En leder er flyttet fra {lead_old_stage} til&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Blynavn</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead-e-mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Blyrørledning</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead scenen</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ein Lead wurde verschoben von {lead_old_stage} zu&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead Name</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead-E-Mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Lead has been move from {lead_old_stage} to&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead Name</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un plomo ha sido movido de {lead_old_stage} a&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nombre principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Correo electrónico principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Tubería de plomo</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Etapa de plomo</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Un lead a été déplacé de {lead_old_stage} à&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nom du responsable</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Courriel principal</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Pipeline de plomb</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Étape principale</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">È stato spostato un lead {lead_old_stage} per&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nome del lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Conduttura di piombo</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Stage</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">リードが移動しました {lead_old_stage} に&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">リード名</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">リードメール</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">リードパイプライン</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">リードステージ</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Er is een lead verplaatst van {lead_old_stage} naar&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Lead naam</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">E-mail leiden</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Hoofdfase</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Prowadzenie zostało przeniesione {lead_old_stage} do&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Imię i nazwisko</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Główny adres e-mail</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Ołów rurociągu</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Etap prowadzący</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Свинец был двигаться от {lead_old_stage} в&nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Имя лидера</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Ведущий Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Ведущий трубопровод</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Ведущий этап</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">A Lead foi mover-se de {lead_old_stage} para &nbsp; {lead_new_stage}.</span></p><p><span style="font-weight: bolder; font-family: sans-serif;">Nome do Lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_name}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Email</span><span style="font-family: sans-serif;">&nbsp;: {lead_email}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Lead Pipeline</span><span style="font-family: sans-serif;">&nbsp;: {lead_pipeline}</span><br style="font-family: sans-serif;"><span style="font-weight: bolder; font-family: sans-serif;">Estágio de Lead</span><span style="font-family: sans-serif;">&nbsp;: {lead_stage}</span><span style="font-family: sans-serif;"><br></span></p>',
                ],
            ],
            'Assign Estimation' => [
                'subject' => 'New Estimation Assign',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;">مرحبا،</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">تم تعيين تقدير جديد لك.</span></p><p><span style="font-family: sans-serif;"><b>معرف التقدير</b>: {estimation_name}<br><b>عميل تقدير</b> : {estimation_client}<br><b>حالة التقدير</b> : {estimation_status}</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;">Hej,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Ny estimering er blevet tildelt til dig.</span></p><p><span style="font-family: sans-serif;"><b>Estimations-id</b>: {estimation_name}<br><b>Estimeringsklient</b> : {estimation_client}<br><b>Estimeringsstatus</b> : {estimation_status}</span></p>',
                    'de' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Neue Schätzung wurde Ihnen zugewiesen.</span></p><p><span style="font-family: sans-serif;"><b>Schätz-Id</b>: {estimation_name}<br><b>Schätzungs-Client</b> : {estimation_client}<br><b>Schätzungsstatus</b> : {estimation_status}</span></p>',
                    'en' => '<p><span style="font-family: sans-serif;">Hello,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">New Estimation has been Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Estimation Id</b>: {estimation_name}<br><b>Estimation Client</b> : {estimation_client}<br><b>Estimation Status</b> : {estimation_status}</span></p>',
                    'es' => '<p><span style="font-family: sans-serif;">Hola,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Se le ha asignado una nueva estimación.</span></p><p><span style="font-family: sans-serif;"><b>ID de estimación</b>: {estimation_name}<br><b>Cliente de Estimación</b> : {estimation_client}<br><b>Estado de estimación</b> : {estimation_status}</span></p>',
                    'fr' => '<p><span style="font-family: sans-serif;">Bonjour,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Une nouvelle estimation vous a été attribuée.</span></p><p><span style="font-family: sans-serif;"><b>Identifiant d\'estimation</b>: {estimation_name}<br><b>Client d\'estimation</b> : {estimation_client}<br><b>Statut d\'estimation</b> : {estimation_status}</span></p>',
                    'it' => '<p><span style="font-family: sans-serif;">Ciao,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">La nuova stima è stata assegnata a te.</span></p><p><span style="font-family: sans-serif;"><b>ID stima</b>: {estimation_name}<br><b>Cliente di stima</b> : {estimation_client}<br><b>Stato della stima</b> : {estimation_status}</span></p>',
                    'ja' => '<p><span style="font-family: sans-serif;">こんにちは、</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">新しい見積もりが割り当てられました。</span></p><p><span style="font-family: sans-serif;"><b>見積もりID</b>: {estimation_name}<br><b>見積もりクライアント</b> : {estimation_client}<br><b>見積もり状況</b> : {estimation_status}</span></p>',
                    'nl' => '<p><span style="font-family: sans-serif;">Hallo,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nieuwe schatting is aan u toegewezen.</span></p><p><span style="font-family: sans-serif;"><b>Schattings-ID</b>: {estimation_name}<br><b>Schatting Client</b> : {estimation_client}<br><b>Schattingsstatus</b> : {estimation_status}</span></p>',
                    'pl' => '<p><span style="font-family: sans-serif;">Witaj,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nowe oszacowanie zostało Ci przypisane.</span></p><p><span style="font-family: sans-serif;"><b>Identyfikator szacunku</b>: {estimation_name}<br><b>Oszacowanie klienta</b> : {estimation_client}<br><b>Status oszacowania</b> : {estimation_status}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;">Привет,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Новая оценка была назначена вам.</span></p><p><span style="font-family: sans-serif;"><b>Идентификатор оценки</b>: {estimation_name}<br><b>Оценка клиента</b> : {estimation_client}<br><b>Статус оценки</b> : {estimation_status}</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;">Olá,</span><br style="font-family: sans-serif;"><span style="font-family: sans-serif;">Nova Estimation foi Assign to you.</span></p><p><span style="font-family: sans-serif;"><b>Estimation Id</b>: {estimation_name}<br><b>Estimation Client</b> : {estimation_client}<br><b>Status da Estimation</b> : {estimation_status}</span></p>',
                ],
            ],
            'Contract' => [
                'subject' => 'Contract',
                'lang' => [
                    'ar' => '<p><span style="font-family: sans-serif;"><strong>مرحبا </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong><strong>العقد :</strong> </strong>{ contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>تاريخ البدء</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>تاريخ الانتهاء</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">اتطلع للسمع منك. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Regards Regards ، </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'da' => '<p><span style="font-family: sans-serif;"><strong>Hej </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Aftaleemne:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart-dato</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Slutdato</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">Ser frem til at høre fra dig. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Kærlig hilsen </span></strong></p>
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
                    'ja' => '<p><span style="font-family: sans-serif;"><span style="font-family: sans-serif;"><strong>ハイ </strong>{contract_client} </span></span></p>
                    <p><span style="font-family: sans-serif;"><strong>契約件名:</strong> {契約対象} </span></p>
                    <p><strong><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: </span></strong><span style="font-family: sans-serif;">{contract_start_date}</span><span style="font-family: sans-serif;"> </span></p>
                    <p><span style="font-family: sans-serif;"><strong>終了日</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">お客様から連絡をお待ちしています。 </span></p>
                    <p><strong><span style="font-family: sans-serif;"><span style="font-family: sans-serif;">クインド・レード </span></span></strong></p>
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
                    <p><span style="font-family: sans-serif;"><strong>Data zakończenia</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Nie można się doczekać, aby usłyszeć od użytkownika. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Regaty typu, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{company_name}</span></p>',
                    'ru' => '<p><span style="font-family: sans-serif;"><strong>Привет </strong>{ contract_client } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Тема договора:</strong> { contract_subject } </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>дата запуска</strong>: { contract_start_date } </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Дата окончания</strong>: { contract_end_date } </span></p>
                    <p><span style="font-family: sans-serif;">С нетерпением ожидаю услышать от вас. </span></p>
                    <p><strong><span style="font-family: sans-serif;">Карты вида, </span></strong></p>
                    <p><span style="font-family: sans-serif;">{ company_name }</span></p>',
                    'pt' => '<p><span style="font-family: sans-serif;"><strong>Oi </strong>{contract_client} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Assunto do Contrato:</strong> {contract_subject} </span></p>
                    <p><strong><span style="font-family: sans-serif;">S</span></strong><span style="font-family: sans-serif;"><strong>tart Date</strong>: {contract_start_date} </span></p>
                    <p><span style="font-family: sans-serif;"><strong>Data de término</strong>: {contract_end_date} </span></p>
                    <p><span style="font-family: sans-serif;">Olhando para a frente para ouvir de você. </span></p>
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
