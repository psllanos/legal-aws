<?php

namespace App\Http\Controllers;

use App\Models\CustomField;
use App\Models\Deal;
use App\Models\EmailTemplateLang;
use App\Models\Lead;
use App\Models\Mdf;
use App\Models\Notification;
use App\Models\Order;
use App\Models\Plan;
use App\Models\User;
use App\Models\UserDeal;
use App\Models\Utility;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(Auth::user()->can('Manage Users'))
        {
            $user  = Auth::user();
            $users = User::where('created_by', '=', $user->ownerId())->where('type', '!=', 'Client')->get();

            return view('users.index', compact('users'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function create()
    {
        if(Auth::user()->can('Create User'))
        {
            $user = Auth::user();
            if($user->type == 'Super Admin')
            {
                return view('users.create');
            }
            else
            {
                $roles        = Role::where('created_by', '=', $user->ownerId())->get();
                $customFields = CustomField::where('module', '=', 'user')->get();

                return view('users.create_owner', compact('roles', 'customFields'));
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function store(Request $request)
    {
        if(\Auth::user()->can('Create User'))
        {
            $objUser      = \Auth::user();
            $resp         = '';
            $default_lang = Utility::getValByName('default_language');

            if($objUser->type == 'Super Admin')
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'email' => 'required|email|unique:users',
                                       'password' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $user      = User::create(
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'type' => 'Owner',
                        'lang' => $default_lang,
                        'created_by' => \Auth::user()->id,
                    ]
                );
                $adminRole = Role::findByName('Owner');
                $user->assignRole($adminRole);
                $user->assignPlan(1);
                $user->userDefaultData();
                $user->makeEmployeeRole();
            }
            else
            {
                $validator = \Validator::make(
                    $request->all(), [
                                       'name' => 'required',
                                       'email' => 'required|email|unique:users',
                                       'password' => 'required',
                                       'role' => 'required',
                                   ]
                );
                if($validator->fails())
                {
                    $messages = $validator->getMessageBag();

                    return redirect()->route('users')->with('error', $messages->first());
                }

                $role = Role::findById($request->role);
                $user = User::create(
                    [
                        'name' => $request->name,
                        'email' => $request->email,
                        'password' => Hash::make($request->password),
                        'type' => $role->name,
                        'job_title' => $request->job_title,
                        'created_by' => $objUser->ownerId(),
                        'lang' => $default_lang,
                    ]
                );
                $user->assignRole($role);

                CustomField::saveData($user, $request->customField);

                $uArr = [
                    'email' => $user->email,
                    'password' => $request->password,
                ];
                // Send Email
                $resp = Utility::sendEmailTemplate('New User', [$user->id => $user->email], $uArr);
            }

            return redirect()->route('users')->with('success', __('User created Successfully!') . ((!empty($resp) && $resp['is_success'] == false && !empty($resp['error'])) ? '<br> <span class="text-danger">' . $resp['error'] . '</span>' : ''));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function show($user_id)
    {
        // dd('ndbad');
        $usr  = Auth::user();
        $user = User::find($user_id);

        if($usr->id == $user->ownerId() && $user_id != $usr->id && $user->type != 'Client')
        {
            $deals       = $user->deals()->orderByDesc('deals.id')->get();
            $arr_deal    = $deals->pluck('id')->toArray();
            $deal_in     = Deal::whereIn('id', $arr_deal);
            $curr_month  = $deal_in->whereMonth('created_at', '=', date('m'))->get();
            $curr_week   = $deal_in->whereBetween(
                'created_at', [
                                \Carbon\Carbon::now()->startOfWeek(),
                                \Carbon\Carbon::now()->endOfWeek(),
                            ]
            )->get();
            $last_30days = $deal_in->whereDate('created_at', '>', \Carbon\Carbon::now()->subDays(30))->get();

            // Deal Summary
            $cnt_deal                = [];
            $cnt_deal['total']       = Deal::getDealSummary($deals);
            $cnt_deal['this_month']  = Deal::getDealSummary($curr_month);
            $cnt_deal['this_week']   = Deal::getDealSummary($curr_week);
            $cnt_deal['last_30days'] = Deal::getDealSummary($last_30days);

            $cnt_deal['cnt_total']       = $deals->count();
            $cnt_deal['cnt_this_month']  = $curr_month->count();
            $cnt_deal['cnt_this_week']   = $curr_week->count();
            $cnt_deal['cnt_last_30days'] = $last_30days->count();

            // get leads
            $leads = $user->leads()->orderByDesc('leads.id')->get();

            // MDF Summary
            $mdfs        = $user->mdfs()->orderByDesc('id')->get();
            $arr_mdf     = $mdfs->pluck('id')->toArray();
            $mdf_in      = Mdf::whereIn('id', $arr_mdf);
            $curr_month  = $mdf_in->whereMonth('date', '=', date('m'))->get();
            $curr_week   = $mdf_in->whereBetween(
                'date', [
                          \Carbon\Carbon::now()->startOfWeek(),
                          \Carbon\Carbon::now()->endOfWeek(),
                      ]
            )->get();
            $last_30days = $mdf_in->whereDate('date', '>', \Carbon\Carbon::now()->subDays(30))->get();

            $cnt_mdf                = [];
            $cnt_mdf['total']       = Mdf::getMdfSummary($mdfs);
            $cnt_mdf['this_month']  = Mdf::getMdfSummary($curr_month);
            $cnt_mdf['this_week']   = Mdf::getMdfSummary($curr_week);
            $cnt_mdf['last_30days'] = Mdf::getMdfSummary($last_30days);

            $cnt_mdf['cnt_total']       = $mdfs->count();
            $cnt_mdf['cnt_this_month']  = $curr_month->count();
            $cnt_mdf['cnt_this_week']   = $curr_week->count();
            $cnt_mdf['cnt_last_30days'] = $last_30days->count();

            return view('users.show', compact('user', 'deals', 'cnt_deal', 'leads', 'mdfs', 'cnt_mdf'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function edit($id)
    {
        if(Auth::user()->can('Edit User'))
        {
            $user = User::where('id', '=', $id)->where('created_by', '=', Auth::user()->ownerId())->first();
            if($user)
            {
                $objUser = Auth::user();
                if($objUser->type == 'Super Admin')
                {
                    return view('users.edit', compact('user'));
                }
                else
                {
                    $roles    = Role::where('created_by', '=', $user->ownerId())->get();
                    $userRole = $user->roles->first();
                    if($userRole)
                    {
                        $userRole = $userRole->id;
                    }
                    else
                    {
                        $userRole = '';
                    }
                    $user->customField = CustomField::getData($user, 'user');
                    $customFields      = CustomField::where('module', '=', 'user')->get();

                    return view('users.edit_owner', compact('roles', 'userRole', 'user', 'customFields'));
                }
            }
            else
            {
                return response()->json(['error' => __('Invalid User.')], 401);
            }
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function update($id, Request $request)
    {
        if(Auth::user()->can('Edit User'))
        {
            $user = User::where('id', '=', $id)->where('created_by', '=', Auth::user()->ownerId())->first();
            if($user)
            {
                $objUser = Auth::user();
                if($objUser->type == 'Super Admin')
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'name' => 'required',
                                           'email' => 'required|email|unique:users,email,' . $id,
                                       ]
                    );
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('users')->with('error', $messages->first());
                    }

                    $post         = [];
                    $post['name'] = $request->name;
                    if(!empty($request->password))
                    {
                        $validation['password'] = 'required';
                        $post['password']       = Hash::make($request->password);
                    }

                    $post['email'] = $request->email;

                    $user->update($post);
                }
                else
                {
                    $validator = \Validator::make(
                        $request->all(), [
                                           'name' => 'required',
                                           'email' => 'required|email|unique:users,email,' . $id,
                                           'role' => 'required',
                                       ]
                    );
                    if($validator->fails())
                    {
                        $messages = $validator->getMessageBag();

                        return redirect()->route('users')->with('error', $messages->first());
                    }

                    $post         = [];
                    $post['name'] = $request->name;
                    if(!empty($request->password))
                    {
                        $validation['password'] = 'required';
                        $post['password']       = Hash::make($request->password);
                    }

                    $post['email']     = $request->email;
                    $post['job_title'] = $request->job_title;
                    $post['type']      = Role::findById($request->role)->name;
                    $user->update($post);
                    $roles[] = $request->role;
                    $user->roles()->sync($roles);
                    CustomField::saveData($user, $request->customField);
                }

                return redirect()->route('users')->with('success', __('User Updated Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid User.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroy($id)
    {
        $usr = Auth::user();

        if($usr->can('Delete User'))
        {
            $user = User::where('id', '=', $id)->where('created_by', '=', $usr->ownerId())->first();
            if($user)
            {
                if($usr->type == 'Super Admin')
                {
                    if($user->delete_status == 0)
                    {
                        $user->delete_status = 1;
                    }
                    else
                    {
                        $user->delete_status = 0;
                    }
                    $user->save();
                }
                else
                {
                    UserDeal::where('user_id', '=', $user->id)->delete();
                    Notification::where('user_id', '=', $user->id)->delete();

                    $user->delete();
                }

                return redirect()->route('users')->with('success', __('User Deleted Successfully!'));
            }
            else
            {
                return redirect()->back()->with('error', __('Invalid User.'));
            }
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function profile()
    {
        $user = Auth::user();

        return view('users.profile', compact('user'));
    }

    public function deleteAvatar()
    {
        $objUser = Auth::user();
        \File::delete(storage_path('avatars/' . $objUser->avatar));
        $objUser->avatar = '';
        $objUser->save();

        return redirect()->route('profile')->with('success', __('Avatar deleted successfully'));
    }

    public function updateProfile(Request $request)
    {

        $userDetail = \Auth::user();
        $user       = User::findOrFail($userDetail['id']);
        $this->validate(
            $request, [
                        'name' => 'required|max:120',
                        'email' => 'required|email|unique:users,email,' . $userDetail['id'],
                        'profile' => 'image',
                    ]
        );
        if($request->hasFile('profile'))
        {
            $filenameWithExt = $request->file('profile')->getClientOriginalName();
            $filename        = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension       = $request->file('profile')->getClientOriginalExtension();
            $fileNameToStore = $filename . '_' . time() . '.' . $extension;
            $settings = Utility::getStorageSetting();

            if($settings['storage_setting']=='local'){
                $dir        = 'uploads/avatar/';
            }
            else{
                    $dir        = 'uploads/avatar';
                }
            $image_path = $dir . $userDetail['avatar'];

            if(\File::exists($image_path))
            {
                \File::delete($image_path);
            }

                // if(!file_exists($dir))
                // {
                //     mkdir($dir, 0777, true);
                // }
            $url = '';
            // $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);
            // dd($path);
            $path = Utility::upload_file($request,'profile',$filenameWithExt,$dir,[]);

            if($path['flag'] == 1){
                $url = $path['url'];
            }else{
                return redirect()->route('profile', \Auth::user()->id)->with('error', __($path['msg']));
            }

        // dd($path);
            // $path = $request->file('profile')->storeAs('uploads/avatar/', $fileNameToStore);

        }

        if(!empty($request->profile))
        {
            $user['avatar'] =  $url;
        }
        $user['name']  = $request['name'];
        $user['email'] = $request['email'];
        $user->save();
        CustomField::saveData($user, $request->customField);

        return redirect()->back()->with(
            'success', 'Profile successfully updated.'
        );
    }

    public function updatePassword(Request $request)
    {
        if(Auth::Check())
        {
            $request->validate(
                [
                    'old_password' => 'required',
                    'password' => 'required|same:password',
                    'password_confirmation' => 'required|same:password',
                ]
            );
            $objUser          = Auth::user();
            $request_data     = $request->All();
            $current_password = $objUser->password;

            if(Hash::check($request_data['old_password'], $current_password))
            {
                $user_id            = Auth::User()->id;
                $obj_user           = User::find($user_id);
                $obj_user->password = Hash::make($request_data['password']);;
                $obj_user->save();

                return redirect()->route('profile')->with('success', __('Password Updated Successfully!'));
            }
            else
            {
                return redirect()->route('profile')->with('error', __('Please Enter Correct Current Password!'));
            }
        }
        else
        {
            return redirect()->route('profile')->with('error', __('Something is wrong!'));
        }
    }

    public function lang($currantLang)
    {
        if(Auth::user()->can('Manage Languages'))
        {
            $user = Auth::user();

            $dir = base_path() . '/resources/lang/' . $user->id . "/" . $currantLang;
            if(!is_dir($dir))
            {
                $dir = base_path() . '/resources/lang/' . $currantLang;
                if(!is_dir($dir))
                {
                    $dir = base_path() . '/resources/lang/en';
                }
            }
            $arrLabel = json_decode(file_get_contents($dir . '.json'));

            $arrFiles   = array_diff(
                scandir($dir), array(
                                 '..',
                                 '.',
                             )
            );
            $arrMessage = [];
            foreach($arrFiles as $file)
            {
                $fileName = basename($file, ".php");
                $fileData = $myArray = include $dir . "/" . $file;
                if(is_array($fileData))
                {
                    $arrMessage[$fileName] = $fileData;
                }
            }

            return view('lang.index', compact('currantLang', 'arrLabel', 'arrMessage'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function createLang()
    {
        if(Auth::user()->can('Create Language'))
        {
            return view('lang.create');
        }
        else
        {
            return response()->json(['error' => __('Permission Denied.')], 401);
        }
    }

    public function storeLang(Request $request)
    {
        if(Auth::user()->can('Create Language'))
        {
            $Filesystem = new Filesystem();
            $langCode   = strtolower($request->code);
            $langDir    = base_path() . '/resources/lang/';
            $dir        = $langDir;
            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $dir      = $dir . '/' . $langCode;
            $jsonFile = $dir . ".json";
            \File::copy($langDir . 'en.json', $jsonFile);

            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $Filesystem->copyDirectory($langDir . "en", $dir . "/");

            // make entry in email_tempalte_lang table for email template content
            Utility::makeEmailLang($langCode);

            return redirect()->route('lang', $langCode)->with('success', __('Language Created Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function storeLangData($currantLang, Request $request)
    {
        if(Auth::user()->can('Edit Language'))
        {
            //            $Filesystem = new Filesystem();
            $dir = base_path() . '/resources/lang';
            if(!is_dir($dir))
            {
                mkdir($dir);
                chmod($dir, 0777);
            }
            $jsonFile = $dir . "/" . $currantLang . ".json";

            file_put_contents($jsonFile, json_encode($request->label));

            $langFolder = $dir . "/" . $currantLang;

            if(!is_dir($langFolder))
            {
                mkdir($langFolder);
                chmod($langFolder, 0777);
            }

            if(!empty($request->message))
            {
                foreach($request->message as $fileName => $fileData)
                {
                    $content = "<?php return [";
                    $content .= $this->buildArray($fileData);
                    $content .= "];";
                    file_put_contents($langFolder . "/" . $fileName . '.php', $content);
                }
            }

            return redirect()->route('lang', $currantLang)->with('success', __('Language Save Successfully!'));
        }
        else
        {
            return redirect()->back()->with('error', __('Permission Denied.'));
        }
    }

    public function destroyLang($lang)
    {
        $settings     = Utility::settings();
        $default_lang = $settings['default_language'];

        // Remove Email Template Language
        EmailTemplateLang::where('lang', 'LIKE', $lang)->delete();

        $langDir = base_path() . '/resources/lang/';

        if(is_dir($langDir))
        {
            // remove directory and file
            Utility::delete_directory($langDir . $lang);
            unlink($langDir . $lang . '.json');

            // update user that has assign deleted language.
            User::where('lang', 'LIKE', $lang)->update(['lang' => $default_lang]);
        }

        return redirect()->route('lang', $default_lang)->with('success', __('Language Deleted Successfully!'));
    }

    public function buildArray($fileData)
    {
        $content = "";
        foreach($fileData as $lable => $data)
        {
            if(is_array($data))
            {
                $content .= "'$lable'=>[" . $this->buildArray($data) . "],";
            }
            else
            {
                $content .= "'$lable'=>'" . addslashes($data) . "',";
            }
        }

        return $content;
    }

    public function changeLang($lang)
    {
        $user       = Auth::user();
        $user->lang = $lang;
        $user->save();

        return redirect()->back()->with('success', __('Language Change Successfully!'));
    }

    public function search(Request $request)
    {
        $html   = '';
        $usr    = Auth::user();
        $type   = $usr->type;
        $search = $request->keyword;

        if(!empty($search))
        {
            if($type == 'Client')
            {
                $objDeal = Deal::select(
                    [
                        'deals.id',
                        'deals.name',
                    ]
                )->join('client_deals', 'client_deals.deal_id', '=', 'deals.id')->whereRaw('FIND_IN_SET(' . $usr->id . ',client_deals.client_id)')->where('deals.name', 'LIKE', $search . "%")->get();

                $html .= '<li class="mt-2">
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Deals') . '
                            </span>
                        </li>';

                if($objDeal->count() > 0)
                {
                    foreach($objDeal as $deal)
                    {
                        $html .= '<li class="mt-2">
                            <a class="list-link pl-4" href="' . route('deals.show', $deal->id) . '">
                                <span>' . $deal->name . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li class="mt-2">
                                <a class="list-link pl-4" href="#">
                                    <span>' . __('No Deals Found.') . '</span>
                                </a>
                            </li>';
                }
            }
            else
            {
                // Deal Wise Searching
                $objDeal = Deal::select(
                    [
                        'deals.id',
                        'deals.name',
                    ]
                )->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', $usr->id)->where('deals.name', 'LIKE', $search . "%")->get();

                $html .= '<li class="mt-2">
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Deals') . '
                            </span>
                        </li>';

                if($objDeal->count() > 0)
                {
                    foreach($objDeal as $deal)
                    {
                        $html .= '<li class="mt-2">
                            <a class="list-link pl-4" href="' . route('deals.show', $deal->id) . '">
                                <span class="ml-2">' . $deal->name . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li class="mt-2">
                                <a class="list-link pl-4" href="#">
                                    <span class="ml-2">' . __('No Deals Found.') . '</span>
                                </a>
                            </li>';
                }
                // Deal Wise Searching end

                // Task Wise Searching
                $objTask = Deal::select(
                    [
                        'deal_tasks.id',
                        'deal_tasks.name',
                        'deals.id AS deal_id',
                    ]
                )->join('user_deals', 'user_deals.deal_id', '=', 'deals.id')->join('deal_tasks', 'deal_tasks.deal_id', '=', 'deals.id')->where('user_deals.user_id', '=', $usr->id)->where('deal_tasks.name', 'LIKE', $search . "%")->get();

                $html .= '<li class="mt-2">
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Tasks') . '
                            </span>
                        </li>';

                if($objTask->count() > 0)
                {
                    foreach($objTask as $task)
                    {
                        $html .= '<li class="mt-2">
                            <a class="list-link pl-4" href="' . route('deals.show', $task->deal_id) . '">
                                <span class="ml-2">' . $task->name . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li class="mt-2">
                                <a class="list-link pl-4" href="#">
                                    <span class="ml-2">' . __('No Tasks Found.') . '</span>
                                </a>
                            </li>';
                }

                // Task Wise Searching End

                // Lead Wise Searching
                $objLead = Lead::select(
                    [
                        'leads.id',
                        'leads.name',
                    ]
                )->join('user_leads', 'user_leads.lead_id', '=', 'leads.id')->where('user_leads.user_id', '=', $usr->id)->where('leads.name', 'LIKE', $search . "%")->get();

                $html .= '<li class="mt-2">
                            <span class="list-link">
                                <i class="fas fa-search"></i>' . __('Leads') . '
                            </span>
                        </li>';

                if($objLead->count() > 0)
                {
                    foreach($objLead as $lead)
                    {

                        $html .= '<li class="mt-2">
                            <a class="list-link pl-4" href="' . route('leads.show', $lead->id) . '">
                                <span class="ml-2">' . $lead->name . '</span>
                            </a>
                        </li>';
                    }
                }
                else
                {
                    $html .= '<li class="mt-2">
                                <a class="list-link pl-4" href="#">
                                    <span class="ml-2">' . __('No Leads Found.') . '</span>
                                </a>
                            </li>';
                }
                // Lead Wise Searching End
            }
        }
        else
        {
            $html .= '<li class="mt-2">
                        <a class="list-link pl-4" href="#">
                        <i class="fas fa-search"></i>
                            <span class="ml-2">' . __('Type and search By Deal, Lead and Tasks.') . '</span>
                        </a>
                      </li>';
        }

        print_r($html);
    }

    public function upgradePlan($user_id)
    {
        $user  = User::find($user_id);
        $plans = Plan::get();

        return view('users.plan', compact('user', 'plans'));
    }

    public function activePlan(Request $request, $user_id, $plan_id)
    {

        $user       = User::find($user_id);
        $user->plan = $plan_id;
        $user->save();

        $plan       = Plan::find($plan_id);
        $assignPlan = $user->assignPlan($plan_id, $request->duration);
        $price      = $plan->{$request->duration . '_price'};

        if($assignPlan['is_success'] == true && !empty($plan))
        {
            $orderID = strtoupper(str_replace('.', '', uniqid('', true)));
            Order::create(
                [
                    'order_id' => $orderID,
                    'name' => null,
                    'card_number' => null,
                    'card_exp_month' => null,
                    'card_exp_year' => null,
                    'plan_name' => $plan->name,
                    'plan_id' => $plan->id,
                    'price' => $price,
                    'price_currency' => env('CURRENCY'),
                    'txn_id' => '',
                    'payment_type' => __('Manually Upgrade By Super Admin'),
                    'payment_status' => 'succeeded',
                    'receipt' => null,
                    'user_id' => $user->id,
                ]
            );

            return redirect()->back()->with('success', __('Plan successfully upgraded.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Plan fail to upgrade.'));
        }
    }

    public function notificationSeen($user_id)
    {
        Notification::where('user_id', '=', $user_id)->update(['is_read' => 1]);

        return response()->json(['is_success' => true], 200);
    }

      public function employeePassword($id)
    {
        $eId        = \Crypt::decrypt($id);
        $user = User::find($eId);



        return view('users.reset', compact('user'));
    }

    public function employeePasswordReset(Request $request, $id){
        $validator = \Validator::make(
            $request->all(), [
                               'password' => 'required|confirmed|same:password_confirmation',
                           ]
        );

        if($validator->fails())
        {
            $messages = $validator->getMessageBag();

            return redirect()->back()->with('error', $messages->first());
        }


        $user                 = User::where('id', $id)->first();
        $user->forceFill([
            'password' => Hash::make($request->password),
        ])->save();

        return redirect()->back()->with(
                     'success', 'Password successfully updated.'
                 );

    }


}
