<?php

namespace App\Models;

use App\Mail\CommonEmailTemplate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\Tax;
use Pusher\Pusher;
use GuzzleHttp;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class Utility extends Model
{
    public static function settings()
    {
        $data = DB::table('settings');

        if(Auth::check())
        {
            $data->where('created_by', '=', Auth::user()->ownerId())->orWhere('created_by', '=', 1);
        }
        else
        {
            $data->where('created_by', '=', 1)->orWhere('created_by', '=', 2);
        }

    //    if (\Auth::check()) {

    //         $data=$data->where('created_by','=',\Auth::user()->creatorId())->get();
    //         if(count($data)==0){
    //             $data =DB::table('settings')->where('created_by', '=', 1 )->get();
    //         }

    //     } else {

    //         $data->where('created_by', '=', 1);
    //     }
        $data = $data->get();


        $settings = [
            "site_currency" => "USD",
            "site_currency_symbol" => "$",
            "site_enable_stripe" => "off",
            "site_stripe_key" => "",
            "site_stripe_secret" => "",
            "site_enable_paypal" => "off",
            "site_paypal_mode" => "sandbox",
            "site_paypal_client_id" => "",
            "site_paypal_secret_key" => "",
            "site_currency_symbol_position" => "pre",
            "site_date_format" => "M j, Y",
            "site_time_format" => "g:i A",
            "company_name" => "",
            "company_address" => "",
            "company_city" => "",
            "company_state" => "",
            "company_zipcode" => "",
            "company_country" => "",
            "company_telephone" => "",
            "company_email" => "",
            "company_email_from_name" => "",
            "invoice_prefix" => "#INV",
            "contract_prefix" => "#CON",
            "estimation_prefix" => "#EST",
            "invoice_template" => "template1",
            "invoice_color" => "ffffff",
            "invoice_logo" => "",
            "contract_template" => "template1",
            "contract_color" => "ffffff",
            "estimation_template" => "template1",
            "estimation_color" => "ffffff",
            "estimation_logo" => "",
            "mdf_prefix" => "#MDF",
            "mdf_template" => "template1",
            "mdf_color" => "ffffff",
            "mdf_logo" => "",
            "default_language" => "en",
            "enable_landing" => "yes",
            "footer_title" => "Payment Information",
            "footer_note" => "Thank you for your business.",
            "gdpr_cookie" => "",
            "cookie_text" => "",
            "zoom_api_key" =>"",
            "zoom_secret_key" =>"",
            "signup_button"=>"on",
            "color" => "theme-3",
            "cust_theme_bg" => "on",
            "cust_darklayout" => "off",
            "dark_logo" => "logo-dark.png",
            "light_logo" => "logo-light.png",
            "SITE_RTL" => "off",



            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }




    public static function payment_settings()
    {
        $data = DB::table('admin_payment_settings');

       
            $data->where('created_by', '=', Auth::user()->ownerId());
        
        $data = $data->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }
     public static function non_auth_payment_settings($id)
    {
        $data = \DB::table('admin_payment_settings');
        $data =$data->where('created_by', '=', $id);
        $data = $data->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public function paymentSetting()
    {

        $admin_payment_setting = Utility::payment_settings();
    
        $this->currancy_symbol = isset($admin_payment_setting['currency_symbol'])?$admin_payment_setting['currency_symbol']:'';
        $this->currancy = isset($admin_payment_setting['currency'])?$admin_payment_setting['currency']:'';
        $this->paypal_client_id = isset($admin_payment_setting['paypal_client_id'])?$admin_payment_setting['paypal_client_id']:'';
        $this->paypal_mode = isset($admin_payment_setting['paypal_mode'])?$admin_payment_setting['paypal_mode']:'';
        $this->paypal_secret_key = isset($admin_payment_setting['paypal_secret_key'])?$admin_payment_setting['paypal_secret_key']:'';
    
        return;
    }


    public static function set_payment_settings()
    {
        $data = DB::table('admin_payment_settings');

        if(Auth::check())
        {
            $data->where('created_by', '=', Auth::user()->ownerId());
        }
        else
        {
            $data->where('created_by', '=', 1);
        }
        $data = $data->get();
        $res = [];
        foreach ($data as $key => $value) {
            $res[$value->name] = $value->value;
        }

        return $res;
    }

    public static function getValByName($key)
    {
        // dd($key);
        $setting = self::settings();

        if(!isset($setting[$key]) || empty($setting[$key]))
        {
            $setting[$key] = '';
        }

        return $setting[$key];
    }

    public static function languages()
    {
        $dir     = base_path() . '/resources/lang/';
        $glob    = glob($dir . "*", GLOB_ONLYDIR);
        $arrLang = array_map(
            function ($value) use ($dir){
                return str_replace($dir, '', $value);
            }, $glob
        );
        $arrLang = array_map(
            function ($value) use ($dir){
                return preg_replace('/[0-9]+/', '', $value);
            }, $arrLang
        );
        $arrLang = array_filter($arrLang);

        return $arrLang;
    }

    public static function setEnvironmentValue(array $values)
    {
        $envFile = app()->environmentFilePath();
        $str     = file_get_contents($envFile);

        if(count($values) > 0)
        {
            foreach($values as $envKey => $envValue)
            {
                $keyPosition       = strpos($str, "{$envKey}=");
                $endOfLinePosition = strpos($str, "\n", $keyPosition);
                $oldLine           = substr($str, $keyPosition, $endOfLinePosition - $keyPosition);

                // If key does not exist, add it
                if(!$keyPosition || !$endOfLinePosition || !$oldLine)
                {
                    $str .= "{$envKey}='{$envValue}'\n";
                }
                else
                {
                    $str = str_replace($oldLine, "{$envKey}='{$envValue}'", $str);
                }
            }
        }

        $str = substr($str, 0, -1);
        $str .= "\n";

        if(!file_put_contents($envFile, $str))
        {
            return false;
        }

        return true;
    }

    public static function sendNotification($type, $user_id, $obj)
    {
        if(!Auth::check() || $user_id != \Auth::user()->id)
        {

            $notification = Notification::create(
                [
                    'user_id' => $user_id,
                    'type' => $type,
                    'data' => json_encode($obj),
                    'is_read' => 0,
                ]
            );

            // Push Notification
            $options = array(
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => false,
            );

            $pusher          = new Pusher(
                env('PUSHER_APP_KEY'), env('PUSHER_APP_SECRET'), env('PUSHER_APP_ID'), $options
            );
            $data            = [];
            $data['html']    = $notification->toHtml();
            $data['user_id'] = $notification->user_id;

                try{
                    $pusher->trigger('send_notification', 'notification', $data);
                }
                catch(\Exception $e)
                {

                }


            // End Push Notification
        }
    }

    public static function templateData()
    {
        $arr              = [];
        $arr['colors']    = [
            '003580',
            '666666',
            '6777f0',
            'f50102',
            'f9b034',
            'fbdd03',
            'c1d82f',
            '37a4e4',
            '8a7966',
            '6a737b',
            '050f2c',
            '0e3666',
            '3baeff',
            '3368e6',
            'b84592',
            'f64f81',
            'f66c5f',
            'fac168',
            '46de98',
            '40c7d0',
            'be0028',
            '2f9f45',
            '371676',
            '52325d',
            '511378',
            '0f3866',
            '48c0b6',
            '297cc0',
            'ffffff',
            '000',
        ];
        $arr['templates'] = [
            "template1" => "New York",
            "template2" => "Toronto",
            "template3" => "Rio",
            "template4" => "London",
            "template5" => "Istanbul",
            "template6" => "Mumbai",
            "template7" => "Hong Kong",
            "template8" => "Tokyo",
            "template9" => "Sydney",
            "template10" => "Paris",
        ];

        return $arr;
    }

    // Email Template Modules Function START
    // Common Function That used to send mail with check all cases
    public static function sendEmailTemplate($emailTemplate, $mailTo, $obj)
    {

        $usr = Auth::user();

        //Remove Current Login user Email don't send mail to them+
        // dd($emailTemplate, $mailTo, $obj);
        unset($mailTo[$usr->id]);

        $mailTo = array_values($mailTo);

        if($usr->type != 'Super Admin')
        {

            // find template is exist or not in our record
            $template = EmailTemplate::where('name', 'LIKE', $emailTemplate)->first();

            if(isset($template) && !empty($template))
            {
                // check template is active or not by company
                $is_active = UserEmailTemplate::where('template_id', '=', $template->id)->where('user_id', '=', $usr->ownerId())->first();

                if($is_active->is_active == 1)
                {
                    $settings = self::settings();

                    // get email content language base
                    $content = EmailTemplateLang::where('parent_id', '=', $template->id)->where('lang', 'LIKE', $usr->lang)->first();

                    $content->from = $template->from;
                    if(!empty($content->content))
                    {
                        $content->content = self::replaceVariable($content->content, $obj);

                        // send email

                        // dd($mailTo,$content, $settings);
                        try
                        {
                            \Mail::to($mailTo)->send(new CommonEmailTemplate($content, $settings));
                        }
                        catch(\Exception $e)
                        {
                            // $error = __('E-Mail has been not sent due to SMTP configuration');
                            $error = $e->getMessage();
                        }

                        if(isset($error))
                        {
                            $arReturn = [
                                'is_success' => false,
                                'error' => $error,
                            ];
                        }
                        else
                        {
                            $arReturn = [
                                'is_success' => true,
                                'error' => false,
                            ];
                        }
                    }
                    else
                    {
                        $arReturn = [
                            'is_success' => false,
                            'error' => __('Mail not send, email is empty'),
                        ];
                    }

                    return $arReturn;
                }
                else
                {
                    return [
                        'is_success' => true,
                        'error' => false,
                    ];
                }
            }
            else
            {
                return [
                    'is_success' => false,
                    'error' => __('Mail not send, email not found'),
                ];
            }
        }
    }


    // used for replace email variable (parameter 'template_name','id(get particular record by id for data)')
    public static function replaceVariable($content, $obj)
    {
        $arrVariable = [
            '{deal_name}',
            '{deal_pipeline}',
            '{deal_stage}',
            '{deal_status}',
            '{deal_price}',
            '{deal_old_stage}',
            '{deal_new_stage}',
            '{task_name}',
            '{task_priority}',
            '{task_status}',
            '{lead_name}',
            '{lead_email}',
            '{lead_pipeline}',
            '{lead_stage}',
            '{lead_old_stage}',
            '{lead_new_stage}',
            '{estimation_name}',
            '{estimation_client}',
            '{estimation_status}',
            '{contract_subject}',
            '{contract_client}',
            '{contract_start_date}',
            '{contract_end_date}',
            '{app_name}',
            '{company_name},',
            '{email}',
            '{password}',
            '{app_url}',
        ];
        $arrValue    = [
            'deal_name' => '-',
            'deal_pipeline' => '-',
            'deal_stage' => '-',
            'deal_status' => '-',
            'deal_price' => '-',
            'deal_old_stage' => '-',
            'deal_new_stage' => '-',
            'task_name' => '-',
            'task_priority' => '-',
            'task_status' => '-',
            'lead_name' => '-',
            'lead_email' => '-',
            'lead_pipeline' => '-',
            'lead_stage' => '-',
            'lead_old_stage' => '-',
            'lead_new_stage' => '-',
            'estimation_name' => '-',
            'estimation_client' => '-',
            'estimation_status' => '-',
            'contract_subject' => '-',
            'contract_client' => '-',
            'contract_start_date' => '-',
            'contract_end_date' => '-',
            'app_name' => '-',
            'company_name' => '-',
            'email' => '-',
            'password' => '-',
            'app_url' => '-',
        ];

        foreach($obj as $key => $val)
        {
            $arrValue[$key] = $val;
        }

        $arrValue['app_name']     = env('APP_NAME');
        $arrValue['company_name'] = self::settings()['company_name'];
        $arrValue['app_url']      = '<a href="' . env('APP_URL') . '" target="_blank">' . env('APP_URL') . '</a>';

        return str_replace($arrVariable, array_values($arrValue), $content);
    }

    // Make Entry in email_tempalte_lang table when create new language
    public static function makeEmailLang($lang)
    {
        $template = EmailTemplate::all();
        foreach($template as $t)
        {
            $default_lang                 = EmailTemplateLang::where('parent_id', '=', $t->id)->where('lang', 'LIKE', 'en')->first();
            $emailTemplateLang            = new EmailTemplateLang();
            $emailTemplateLang->parent_id = $t->id;
            $emailTemplateLang->lang      = $lang;
            $emailTemplateLang->subject   = $default_lang->subject;
            $emailTemplateLang->content   = $default_lang->content;
            $emailTemplateLang->save();
        }
    }
    // Email Template Modules Function END

    // get font-color code accourding to bg-color
    public static function hex2rgb($hex)
    {
        $hex = str_replace("#", "", $hex);

        if(strlen($hex) == 3)
        {
            $r = hexdec(substr($hex, 0, 1) . substr($hex, 0, 1));
            $g = hexdec(substr($hex, 1, 1) . substr($hex, 1, 1));
            $b = hexdec(substr($hex, 2, 1) . substr($hex, 2, 1));
        }
        else
        {
            $r = hexdec(substr($hex, 0, 2));
            $g = hexdec(substr($hex, 2, 2));
            $b = hexdec(substr($hex, 4, 2));
        }
        $rgb = array(
            $r,
            $g,
            $b,
        );

        //return implode(",", $rgb); // returns the rgb values separated by commas
        return $rgb; // returns an array with the rgb values
    }

    public static function getFontColor($color_code)
    {
        $rgb = self::hex2rgb($color_code);
        $R   = $G = $B = $C = $L = $color = '';

        $R = (floor($rgb[0]));
        $G = (floor($rgb[1]));
        $B = (floor($rgb[2]));

        $C = [
            $R / 255,
            $G / 255,
            $B / 255,
        ];

        for($i = 0; $i < count($C); ++$i)
        {
            if($C[$i] <= 0.03928)
            {
                $C[$i] = $C[$i] / 12.92;
            }
            else
            {
                $C[$i] = pow(($C[$i] + 0.055) / 1.055, 2.4);
            }
        }

        $L = 0.2126 * $C[0] + 0.7152 * $C[1] + 0.0722 * $C[2];

        if($L > 0.179)
        {
            $color = 'black';
        }
        else
        {
            $color = 'white';
        }

        return $color;
    }

    public static function delete_directory($dir)
    {
        if(!file_exists($dir))
        {
            return true;
        }

        if(!is_dir($dir))
        {
            return unlink($dir);
        }

        foreach(scandir($dir) as $item)
        {
            if($item == '.' || $item == '..')
            {
                continue;
            }

            if(!self::delete_directory($dir . DIRECTORY_SEPARATOR . $item))
            {
                return false;
            }

        }

        return rmdir($dir);
    }

    public static function addNewData()
    {
        Artisan::call('cache:forget spatie.permission.cache');
        Artisan::call('cache:clear');

        $usr            = Auth::user();
        $arrPermissions = [
            'Manage MDFs',
            'Request MDF',
            'Edit MDF',
            'Delete MDF',
            'View MDF',
            'Manage MDF Types',
            'Create MDF Type',
            'Edit MDF Type',
            'Delete MDF Type',
            'Manage MDF Sub Types',
            'Create MDF Sub Type',
            'Edit MDF Sub Type',
            'Delete MDF Sub Type',
            'Manage MDF Status',
            'Create MDF Status',
            'Edit MDF Status',
            'Delete MDF Status',
            'Create MDF Payment',
            'MDF Add Expense',
            'MDF Edit Expense',
            'MDF Delete Expense',
        ];

        foreach($arrPermissions as $ap)
        {
            // check if permission is not created then create it.
            $permission = Permission::where('name', 'LIKE', $ap)->first();
            if(empty($permission))
            {
                Permission::create(['name' => $ap]);
            }
        }

        $ownerRole        = Role::where('name', 'LIKE', 'Owner')->where('created_by', '=', $usr->ownerId())->first();
        $ownerPermissions = $ownerRole->getPermissionNames()->toArray();

        $ownerNewPermission = [
            'Manage MDFs',
            'Request MDF',
            'Edit MDF',
            'Delete MDF',
            'View MDF',
            'Manage MDF Types',
            'Create MDF Type',
            'Edit MDF Type',
            'Delete MDF Type',
            'Manage MDF Sub Types',
            'Create MDF Sub Type',
            'Edit MDF Sub Type',
            'Delete MDF Sub Type',
            'Manage MDF Status',
            'Create MDF Status',
            'Edit MDF Status',
            'Delete MDF Status',
            'Create MDF Payment',
            'MDF Add Expense',
            'MDF Edit Expense',
            'MDF Delete Expense',
        ];

        foreach($ownerNewPermission as $op)
        {
            // check if permission is not assign to owner then assign.
            if(!in_array($op, $ownerPermissions))
            {
                $permission = Permission::findByName($op);
                $ownerRole->givePermissionTo($permission);
            }
        }

        $userRole        = Role::where('name', 'LIKE', 'Employee')->first();
        $userPermissions = $userRole->getPermissionNames()->toArray();

        $userNewPermission = [
            'Manage MDFs',
            'Request MDF',
            'Edit MDF',
            'Delete MDF',
            'View MDF',
            'MDF Add Expense',
            'MDF Edit Expense',
            'MDF Delete Expense',
        ];

        foreach($userNewPermission as $op)
        {
            // check if permission is not assign to owner then assign.
            if(!in_array($op, $userPermissions))
            {
                $permission = Permission::findByName($op);
                $userRole->givePermissionTo($permission);
            }
        }
    }

    public static function get_messenger_packages_migration()
    {
        $totalMigration = 0;
        $messengerPath  = glob(base_path() . '/vendor/munafio/chatify/database/migrations' . DIRECTORY_SEPARATOR . '*.php');
        if(!empty($messengerPath))
        {
            $messengerMigration = str_replace('.php', '', $messengerPath);
            $totalMigration     = count($messengerMigration);
        }

        return $totalMigration;
    }

    // Used to check permission is exist or not in database
    public static function checkPermissionExist($permission)
    {
        $permission = Permission::where('name', 'LIKE', $permission)->count();

        return $permission;
    }



    public static function getselectedThemeColor(){
        $color = env('THEME_COLOR');
        if($color == "" || $color == null){
            $color = 'blue';
        }
        return $color;
    }

    public static function getAllThemeColors(){
        $colors = [
            'blue','denim','sapphire','olympic','violet','black','cyan','dark-blue-natural','gray-dark','light-blue','light-purple','magenta','orange-mute','pale-green','rich-magenta','rich-red','sky-gray'
        ];
        return $colors;
    }

    public static function checkImgTransparent($img){
        try{
            $im = imagecreatefrompng($img);
            $rgba = imagecolorat($im,1,1);
            $alpha = ($rgba & 0x7F000000) >> 24;
            if($alpha>0){
                return true;
            }else{
                return false;
            }
        }catch(\Exception $e){
            return false;
        }
    }

    public static function getDateFormated($date, $time = false)
    {
        if(!empty($date) && $date != '0000-00-00')
        {
            if($time == true)
            {
                return date("d M Y H:i A", strtotime($date));
            }
            else
            {
                return date("d M Y", strtotime($date));
            }
        }
        else
        {
            return '';
        }
    }

    // public static function ownerIdforInvoice($id){
    //     $user = User::where(['id' => $id])->first();
    //     if(!is_null($user)){
    //         if($user->type == "owner"){
    //             return $user->id;
    //         }else{
    //             $user1 = User::where(['id' => $user->created_by,$user->type => 'owner'])->first();
    //             if(!is_null($user1)){
    //                 return $user->id;
    //             }
    //         }
    //     }
    //     return 0;
    // }

    public static function ownerIdforInvoice($id){
        $user = User::where(['id' => $id])->first();
        if(!is_null($user)){
            if($user->type == "Owner"){
                return $user->id;
            }else{
                $user1 = User::where(['id' => $user->created_by,$user->type => 'Owner'])->first();
                if(!is_null($user1)){
                    return $user->id;
                }
            }
        }
        return 0;
    }
    public static function invoice_payment_settings($id)
        {
            $data = [];
            $user = User::where(['id' => $id])->first();

            if(!is_null($user)){
                $data = DB::table('admin_payment_settings');
                $data->where('created_by', '=', $user->id);
                $data = $data->get();
            }

            $res = [];
            foreach ($data as $key => $value) {
                $res[$value->name] = $value->value;
            }

            return $res;
        }
        public static function tax($taxes)
    {

        $taxArr = explode(',', $taxes);

        $taxes  = [];
        foreach($taxArr as $tax)
        {
            $taxes[] = Tax::find($tax);
        }

        return $taxes;
    }
    public static function taxRate($taxRate, $price)
    {


        return ($taxRate / 100) * ($price) ;
    }

    public static function send_slack_msg($msg) {
        try
        {
             $settings  = Utility::settings(Auth::user()->ownerId());
             if(isset($settings['slack_webhook']) && !empty($settings['slack_webhook'])){
                $ch = curl_init();

                curl_setopt($ch, CURLOPT_URL, $settings['slack_webhook']);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['text' => $msg]));

                $headers = array();
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

                $result = curl_exec($ch);
                if (curl_errno($ch)) {
                    echo 'Error:' . curl_error($ch);
                }
                curl_close($ch);
            }
        }
        catch(\Exception $e)
        {
            return $e;
        }





    }

      public static function send_telegram_msg($resp) {
        try
        {
            $settings  = Utility::settings();


            $msg = $resp;

            // Set your Bot ID and Chat ID.
            $telegrambot    = $settings['telegrambot'];
            $telegramchatid = $settings['telegramchatid'];

            // Function call with your own text or variable
            $url     = 'https://api.telegram.org/bot' . $telegrambot . '/sendMessage';
            $data    = array(
                'chat_id' => $telegramchatid,
                'text' => $msg,
            );

            $options = array(
                'http' => array(
                    'method' => 'POST',
                    'header' => "Content-Type:application/x-www-form-urlencoded\r\n",
                    'content' => http_build_query($data),
                ),
            );

            $context = stream_context_create($options);

            $result  = file_get_contents($url, false, $context);
            $url     = $url;
        }
        catch(\Exception $e)
        {
            return $e;
        }

    }


    public static function colorset(){
        if(\Auth::user())
        {
            $user = \Auth::user();

            $setting = DB::table('settings')->where('created_by',$user->ownerId())->pluck('value','name')->toArray();

        }
        else{
            $setting = DB::table('settings')->pluck('value','name')->toArray();
        }
        return $setting;
    }

    public static function get_superadmin_logo()
    {
        $is_dark_mode = self::getValByName('cust_darklayout');
        if($is_dark_mode == 'on'){
            return 'logo-light.png';
        }else{
            return 'logo-dark.png';
        }
    }

    public static function get_company_logo()
    {
        $is_dark_mode = self::getValByName('cust_darklayout');

        if($is_dark_mode == 'on'){
            $logo = self::getValByName('cust_darklayout');
            return Utility::getValByName('light_logo');
        }else{
            return Utility::getValByName('dark_logo');
        }
    }




    public static function getStorageSetting()
    {

        $data = DB::table('settings');
        $data = $data->where('created_by', '=', 1);
        $data     = $data->get();
        $settings = [
            "storage_setting" => "local",
            "local_storage_validation" => "jpg,jpeg,png,xlsx,xls,csv,pdf",
            "local_storage_max_upload_size" => "2048000",
            "s3_key" => "",
            "s3_secret" => "",
            "s3_region" => "",
            "s3_bucket" => "",
            "s3_url"    => "",
            "s3_endpoint" => "",
            "s3_max_upload_size" => "",
            "s3_storage_validation" => "",
            "wasabi_key" => "",
            "wasabi_secret" => "",
            "wasabi_region" => "",
            "wasabi_bucket" => "",
            "wasabi_url" => "",
            "wasabi_root" => "",
            "wasabi_max_upload_size" => "",
            "wasabi_storage_validation" => "",
        ];

        foreach($data as $row)
        {
            $settings[$row->name] = $row->value;
        }

        return $settings;
    }


    public static function upload_file($request,$key_name,$name,$path,$custom_validation =[]){
        try{
            $settings = Utility::getStorageSetting();
        //    dd($settings);

            if(!empty($settings['storage_setting'])){

                if($settings['storage_setting'] == 'wasabi'){

                    config(
                        [
                            'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                            'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                            'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                            'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                            'filesystems.disks.wasabi.endpoint' => 'https://s3.'.$settings['wasabi_region'].'.wasabisys.com'
                        ]
                    );

                    $max_size = !empty($settings['wasabi_max_upload_size'])? $settings['wasabi_max_upload_size']:'2048';
                    $mimes =  !empty($settings['wasabi_storage_validation'])? $settings['wasabi_storage_validation']:'';

                }else if($settings['storage_setting'] == 's3'){
                    config(
                        [
                            'filesystems.disks.s3.key' => $settings['s3_key'],
                            'filesystems.disks.s3.secret' => $settings['s3_secret'],
                            'filesystems.disks.s3.region' => $settings['s3_region'],
                            'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                            'filesystems.disks.s3.use_path_style_endpoint' => false,
                        ]
                    );
                    $max_size = !empty($settings['s3_max_upload_size'])? $settings['s3_max_upload_size']:'2048';
                    $mimes =  !empty($settings['s3_storage_validation'])? $settings['s3_storage_validation']:'';


                }else{
                    $max_size = !empty($settings['local_storage_max_upload_size'])? $settings['local_storage_max_upload_size']:'2048';

                    $mimes =  !empty($settings['local_storage_validation'])? $settings['local_storage_validation']:'';
                }


                $file = $request->$key_name;


                if(count($custom_validation) > 0){
                    $validation =$custom_validation;
                }else{

                    $validation =[
                        'mimes:'.$mimes,
                        'max:'.$max_size,
                    ];

                }
                $validator = \Validator::make($request->all(), [
                    $key_name =>$validation
                ]);

                if($validator->fails()){
                    $res = [
                        'flag' => 0,
                        'msg' => $validator->messages()->first(),
                    ];
                    return $res;
                } else {

                    $name = $name;

                    if($settings['storage_setting']=='local')
                    {
                        $request->$key_name->move(storage_path($path), $name);
                        $path = $path.$name;
                    }
                    else if($settings['storage_setting'] == 'wasabi'){

                        $path = \Storage::disk('wasabi')->putFileAs(
                            $path,
                            $file,
                            $name
                        );

                        // $path = $path.$name;

                    }else if($settings['storage_setting'] == 's3'){

                        $path = \Storage::disk('s3')->putFileAs(
                            $path,
                            $file,
                            $name
                        );
                        // $path = $path.$name;
                        // dd($path);
                    }


                    $res = [
                        'flag' => 1,
                        'msg'  =>'success',
                        'url'  => $path
                    ];
                    return $res;
                }

            }else{
                $res = [
                    'flag' => 0,
                    'msg' => __('Please set proper configuration for storage.'),
                ];
                return $res;
            }

        }catch(\Exception $e){
            $res = [
                'flag' => 0,
                'msg' => $e->getMessage(),
            ];
            return $res;
        }
    }


    public static function get_file($path){
        $settings = Utility::getStorageSetting();

        try {
            if($settings['storage_setting'] == 'wasabi'){
                config(
                    [
                        'filesystems.disks.wasabi.key' => $settings['wasabi_key'],
                        'filesystems.disks.wasabi.secret' => $settings['wasabi_secret'],
                        'filesystems.disks.wasabi.region' => $settings['wasabi_region'],
                        'filesystems.disks.wasabi.bucket' => $settings['wasabi_bucket'],
                        'filesystems.disks.wasabi.endpoint' => 'https://s3.'.$settings['wasabi_region'].'.wasabisys.com'
                    ]
                );
            }elseif($settings['storage_setting'] == 's3'){
                config(
                    [
                        'filesystems.disks.s3.key' => $settings['s3_key'],
                        'filesystems.disks.s3.secret' => $settings['s3_secret'],
                        'filesystems.disks.s3.region' => $settings['s3_region'],
                        'filesystems.disks.s3.bucket' => $settings['s3_bucket'],
                        'filesystems.disks.s3.use_path_style_endpoint' => false,
                    ]
                );
            }

            return \Storage::disk($settings['storage_setting'])->url($path);
        } catch (\Throwable $th) {
            return '';
        }
    }


}
