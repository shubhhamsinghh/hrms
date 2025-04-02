<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\MatchOldPassword;
use DataTables;
use Illuminate\Support\Facades\Input;
use View,Response,Validator,Mail,Session,Auth, DB, File;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Department;
use Illuminate\Support\Str;
use App\Models\Candidate;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Designations;
use App\Models\Assigning;
use App\Models\Remark;
use App\Models\Bgvveri;
use App\Models\Bgvdocument;
use Carbon\Carbon;


class AdminController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    
    public function home(){
        $a1 = User::count();
        $a2 = Candidate::count();
        return view('admin_pages.home',compact('a1','a2'));
    }

    public function candidate_form(){
        $departments = Department::orderby('dept_name')->get();
        $designations = Designations::get();
        $recruiters = User::where('designation','HR')->where('user_status',1)->get();
        return view('admin_pages.candidate_form',compact('departments','designations','recruiters'));
    }
    
    public function candidate_form_add(Request $request){
        
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'.time();
        $token = substr(str_shuffle($characters), 0, 8);

        $can_data = [ 
            'token_no' => $token,
            'department_id' => $request->department_id,
            'apply_for' => $request->apply_for,
            'interview_date' => $request->interview_date,
            'created_by' => Auth::user()->id,
            'referral_by' => $request->referral_by,
            'ref_emp_name' => $request->ref_emp_name,
            'ref_emp_id' => $request->ref_emp_id,
            'source' => $request->source,
            'location' => $request->location,
            'shift' => $request->shift,
            'r_shift' => $request->r_shift,
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone_no' => $request->phone_no,
            'email' => $request->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
        ];
        
        if($request->gender != "Male" && $request->gender != "Female"){
            $can_data['gender'] = $request->oth_gender;
        }
        
        $candidate = DB::table('candidate_details')->insertGetId($can_data);
        $education = ['candidate_id' => $candidate];;
    
        if($candidate != null){
            if($request->highsch_lvl == "on" && $request->high_school != null){
                $education['high_school'] = $request->high_school;
            }
            if($request->intermediate_lvl == "on" && $request->intermediate != null){
                $education['intermediate'] = $request->intermediate;
            }
            if($request->diploma_lvl == "on" && $request->diploma != null){
                $education['diploma'] = $request->diploma;
            }
            if($request->graduation_lvl == "on" && $request->graduation != null){
                $education['graduation'] = $request->graduation;
            }
            if($request->postgrad_lvl == "on" && $request->pg != null){
                $education['pg'] = $request->pg;
            }
            if($request->master_lvl == "on" && $request->master != null){
                $education['master'] = $request->master;
            }
        Education::insert($education);
        
        $exp = [
            'candidate_id' => $candidate,
            'exp_level' => $request->exp_level,
            'exp_year' => $request->exp_year,
            'exp_moth' => $request->exp_moth,
            'curr_month_salary' => $request->curr_month_salary,
            'curr_year_salary' => $request->curr_year_salary,
            'expec_month_salary' => $request->expec_month_salary,
            'notice_period' => $request->notice_period,
         ];
         
        $filename=''; $filename1='';$filename2=''; $filename21='';
        $destinationpath = 'images/resume/';
        $destinationpath2 = 'images/portfolio/';
        
        if (request()->hasFile('resume')) {
            if ($request->file('resume')->isValid()) {
                $file     = $request->file('resume')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('resume')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('resume')->move($destinationpath, $filename1);
                $exp['resume'] = $filename1;
            }
        }  
        if (request()->hasFile('portfolio')) {
            if ($request->file('portfolio')->isValid()) {
                $file   = $request->file('portfolio')->getClientOriginalName();
                $filename2 = pathinfo($file, PATHINFO_FILENAME);
                $filename2 = strtolower($filename2);
                $filename2 = preg_replace("/[^a-z0-9_\s-]/", "", $filename2);
                $filename2 = preg_replace("/[\s-]+/", " ", $filename2);
                $filename2 = preg_replace("/[\s_]/", "-", $filename2);
                $extension1 = $request->file('portfolio')->getClientOriginalExtension();
                $filename21 = $filename2. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('portfolio')->move($destinationpath2, $filename21);
                $exp['portfolio'] = $filename21;
            }
         }
        Experience::insert($exp);
        }
        
        Session::flash('msg', 'Application Updated Successfully !!'); 
        Session::flash('type', 'success');
        return redirect()->route('candidateListing');
    }
    
    public function candidate_form_edit($token){
       
        $data = Candidate::with('education','experience')->where('token_no',$token)->first();
        $departments = Department::orderby('dept_name')->get();
        $designations = Designations::get();
        $recruiters = User::where('designation','HR')->where('user_status',1)->get();
        if($data->status == 1){
        return view('admin_pages.candidate_form_edit',compact('departments','data','designations','recruiters'));
        }else{
            return redirect()->route('candidateListing');
        }
    }
    
    public function candidate_form_update(Request $request, $token){
        
        $can_id = Candidate::where('token_no',$token)->first();
         $can_data = [ 
            'department_id' => $request->department_id,
            'apply_for' => $request->apply_for,
            'interview_date' => $request->interview_date,
            'referral_by' => $request->referral_by,
            'ref_emp_name' => $request->ref_emp_name,
            'ref_emp_id' => $request->ref_emp_id,
            'source' => $request->source,
            'location' => $request->location,
            'shift' => $request->shift,
            'r_shift' => $request->r_shift,
            'name' => $request->name,
            'father_name' => $request->father_name,
            'phone_no' => $request->phone_no,
            'email' => $request->email,
            'dob' => $request->dob,
            'gender' => $request->gender,
            'address' => $request->address,
        ];
        
        if($request->gender != "Male" && $request->gender != "Female"){
            $can_data['gender'] = $request->oth_gender;
        }
        
        $candidate = Candidate::where('token_no',$token)->update($can_data);
        
    
        if($candidate != null){
            if($request->highsch_lvl == "on" && $request->high_school != null){
                $education['high_school'] = $request->high_school;
            }else{
                $education['high_school'] = null;
            }
            if($request->intermediate_lvl == "on" && $request->intermediate != null){
                $education['intermediate'] = $request->intermediate;
            }else{
                $education['intermediate'] = null;
            }
            if($request->diploma_lvl == "on" && $request->diploma != null){
                $education['diploma'] = $request->diploma;
            }else{
                $education['diploma'] = null;
            }
            if($request->graduation_lvl == "on" && $request->graduation != null){
                $education['graduation'] = $request->graduation;
            }else{
                $education['graduation'] = null;
            }
            if($request->postgrad_lvl == "on" && $request->pg != null){
                $education['pg'] = $request->pg;
            }else{
                $education['pg'] = null;
            }
            if($request->master_lvl == "on" && $request->master != null){
                $education['master'] = $request->master;
            }else{
               $education['master'] = null;
            }
        Education::where('candidate_id',$can_id->id)->update($education);
        
        $exp = [
            'exp_level' => $request->exp_level,
            'exp_year' => $request->exp_year,
            'exp_moth' => $request->exp_moth,
            'curr_month_salary' => $request->curr_month_salary,
            'curr_year_salary' => $request->curr_year_salary,
            'expec_month_salary' => $request->expec_month_salary,
            'notice_period' => $request->notice_period,
            'links' => $request->links,
         ];
         
        $filename=''; $filename1='';$filename2=''; $filename21='';
        $destinationpath = 'images/resume/';
        $destinationpath2 = 'images/portfolio/';
        
        if (request()->hasFile('resume')) {
            if ($request->file('resume')->isValid()) {
                $file     = $request->file('resume')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('resume')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('resume')->move($destinationpath, $filename1);
                
                $data = DB::table('experience')->where(['candidate_id' => $can_id->id])->select('resume')->first();
				$image_path = 'images/resume/'.$data->resume;
				if(File::exists($image_path)) { File::delete($image_path); }

				DB::table('experience')->where('candidate_id', $can_id->id)->update(['resume' => $filename1]);
            }
        }  
        if (request()->hasFile('portfolio')) {
            if ($request->file('portfolio')->isValid()) {
                $file = $request->file('portfolio')->getClientOriginalName();
                $filename2 = pathinfo($file, PATHINFO_FILENAME);
                $filename2 = strtolower($filename2);
                $filename2 = preg_replace("/[^a-z0-9_\s-]/", "", $filename2);
                $filename2 = preg_replace("/[\s-]+/", " ", $filename2);
                $filename2 = preg_replace("/[\s_]/", "-", $filename2);
                $extension1 = $request->file('portfolio')->getClientOriginalExtension();
                $filename21 = $filename2. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('portfolio')->move($destinationpath2, $filename21);
                
                $data = DB::table('experience')->where(['candidate_id' => $can_id->id])->select('portfolio')->first();
				$image_path = 'images/portfolio/'.$data->portfolio;
				if(File::exists($image_path)) { File::delete($image_path); }

				DB::table('experience')->where('candidate_id', $can_id->id)->update(['portfolio' => $filename21]);
            }
         }
          DB::table('experience')->where('candidate_id', $can_id->id)->update($exp);
        }
        
        Session::flash('msg', 'Application Updated Successfully !!'); 
        Session::flash('type', 'success');
        return redirect()->route('candidateListing');
    }

    public function candidate_listing(){
        return view('admin_pages.candidate_listing');
    }
    
    public function get_candidate_listing_data(){
       $data = Candidate::orderby('id', 'desc')->get();
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('date',function($data){
          return date('d-M-Y',strtotime($data->created_at)); 
        })
        ->addColumn('options',function($data){
            $html = '<center> <a class="btn btn-primary btn-sm" href="'.route('candidateView',['token' => $data->token_no]).'" title="View"><i class="fa fa-eye"></i></a>';
            if($data->status == 1){ 
            $html .= '&nbsp;<a class="btn btn-warning btn-sm" href="'.route('candidateFormEdit',['token' => $data->token_no]).'" title="Edit"><i class="fa fa-edit"></i></a>';
            }
            $html .= '</center>';
            return $html; 
        })
        ->rawColumns(['options'])
        ->make(true);
    }
    
    public function candidate_view(Request $request, $token){
       
       $data = Candidate::with('education','experience','department','jobrole','document')->where('token_no',$token)->first();
       $bgb = Bgvveri::with('bgvdocuments')->where('candidate_id',$data->id)->get();
       $recruiters = User::where('designation','HR')->where('user_status',1)->get();
       $tls = User::where('designation','TL / Supervisor')->where('user_status',1)->get();
       $managers = User::where('designation','Manager')->where('user_status',1)->get();
       $assigning  = Assigning::with('from','to','candidate')->where('candidate_id',$data->id)->orderby('id','desc')->get();
       $hr_re = DB::table('assigning')->where('candidate_id',$data->id)->where(function ($query) {
                $query->where('int_round', 0)
                  ->orWhere('int_round', 1);
                })->first();
       return view('admin_pages.candidate_view',compact('data','recruiters','assigning','tls','managers','hr_re','bgb'));
    }
    
    public function assign_to(Request $request, $token){
        
      $canID = Candidate::where('token_no',$token)->pluck('id')->first();
      $getMaxassign = Assigning::where('candidate_id',$canID)->orderby('int_round','desc')->first();
      $assign = new Assigning();
      $assign->assign_from = Auth::user()->id;
      $assign->assign_to = $request->assign_to;
      $assign->candidate_id = $canID;
      $assign->int_mode =$request->int_mode;
      $assign->int_date = $request->int_date;
      $assign->int_time = $request->int_time;
      if($getMaxassign != null){
          $assign->int_round = ++$getMaxassign->int_round; 
      }
      $assign->save();
      
      Session::flash('msg', 'Application Updated Successfully !!'); 
      Session::flash('type', 'success');
      
      return redirect()->back();
      
    }
    
    public function assigned_candidate(){
        
      return view('admin_pages.assigned_candidate');
    }
    
    public function assigned_candidate_data(){
    
      $candi_array = [];
      if(Auth::user()->id == 1){
         $allUData = DB::table('assigning')->distinct()->pluck('candidate_id');
      }else{
         $allUData = DB::table('assigning')->where('assign_to',Auth::user()->id)->distinct()->pluck('candidate_id');
      }
      
      foreach($allUData as $aud){
         if(Auth::user()->id != 1){
             $d = DB::table('assigning')->where('candidate_id',$aud)->where('assign_to',Auth::user()->id)->first();
         }else{
             $d = DB::table('assigning')->where('candidate_id',$aud)->first();
         }
         array_push($candi_array,$d->id);
      }
     
      $data = Assigning::with('candidate')
              ->whereHas('candidate', function ($query) {
              $query->where('status', 1);
              });
             
              
      if(Auth::user()->id != 1){
         $data->where('assign_to',Auth::user()->id);
      }
      
      $data->whereIn('id',$candi_array)->orderby('id', 'desc')->get();
     
        return DataTables::of($data)
        ->addIndexColumn()
        ->addColumn('date',function($data){
          return date('d-M-Y',strtotime($data->int_date)); 
        })
        ->addColumn('token',function($data){
          return $data->candidate->token_no; 
        })
        ->addColumn('name',function($data){
          return $data->candidate->name; 
        })
        ->addColumn('phone_no',function($data){
          return $data->candidate->phone_no; 
        })
        ->addColumn('email',function($data){
          return $data->candidate->email; 
        })
        ->addColumn('from',function($data){
          return $data->from->name; 
        })
        ->addColumn('address',function($data){
          return $data->candidate->address; 
        })
        ->addColumn('options',function($data){
            $token_no = optional($data->candidate)->token_no;
            $html = '<center> <a class="btn btn-primary btn-sm" href="'.route('candidateView',['token' => $token_no]).'" title="View"><i class="fa fa-eye"></i></a></center>';
            return $html; 
        })
        ->rawColumns(['options'])
         ->filter(function ($query) {
            if (request()->has('search') && request()->input('search.value') != '') {
                $searchValue = request()->input('search.value');
                $query->whereHas('candidate', function ($q) use ($searchValue) {
                    $q->where('token_no', 'like', "%{$searchValue}%")
                      ->orwhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('name', 'like', "%{$searchValue}%")
                      ->orWhere('phone_no', 'like', "%{$searchValue}%")
                      ->orWhere('address', 'like', "%{$searchValue}%");
                });
            }
        })
        ->make(true);
    }

    public function add_remark(Request $request, $token){
        
        $can_id = Candidate::where('token_no',$token)->first();
        $remark = new Remark();
        $remark->assigned_id = $request->assigned_id;
        $remark->candidate_id = $can_id->id;
        $remark->rating = $request->rating;
        $remark->re_status = $request->re_status;
        $remark->remark = $request->remark;
        if($remark->save()){
            if($request->key == 'hr'){ $s = 1;}elseif($request->key == 'tl'){ $s = 3;}elseif($request->key == 'manager'){ 
            $s = 5;
            DB::table('manager_ratings')->insert(
                ['remark_id' => $remark->id,
                 'work_exe' => $request->work_exe,
                 'applicable_skl' => $request->applicable_skl,
                 'appearance' => $request->appearance,
                 'attiude' => $request->attiude,
                 'education' => $request->education,
                 'enthusiasm' => $request->enthusiasm
                ]);     
            }elseif($request->key == 'final hr'){ $s = 6;}
            DB::table('assigning')->where('id',$request->assigned_id)->update(['int_round' => $s]);
        }
        
        Session::flash("msg", "Remark Added Successfully !!"); 
        Session::flash("type", "success");
        return redirect()->back(); 
    }
    
    public function final_remark(Request $request, $token){
       
      $canID = Candidate::where('token_no',$token)->select('id','name','email')->first();
      $assign = new Assigning();
      $assign->assign_from = Auth::user()->id;
      $assign->assign_to = 0;
      $assign->candidate_id = $canID->id;
      $assign->int_mode = 0;
      $assign->int_round = 6; 
      if($assign->save()){
        $remark = new Remark();
        $remark->assigned_id = $assign->id;
        $remark->candidate_id = $canID->id;
        $remark->rating = 0;
        $remark->re_status = $request->interested."/".$request->fi_status;
        $remark->remark = $request->remark;
        if($remark->save()){
            $arr = [ 
                'designation' => $request->designation,
                'salary' => $request->salary,
                'doj' => $request->doj
            ];
            if(($request->interested == "Yes" && $request->fi_status == "Selected")){
                $checkExp = Experience::where('candidate_id',$canID->id)->select('exp_level')->first();
                if($checkExp->exp_level == "Experienced"){
                Mail::send('mails.bg_verification',['name' => $canID->name,'token' => $token], function($message) use ($canID)
                {
                    $to_emails = $canID->email;
                    $message->to($to_emails)->subject('Background Verifivation Mail From HRMS');
                });
                $arr['bgv_sent_on'] = date('Y-m-d H:i:s');
                $arr['bgv_link'] = 1;
                }else{
                Mail::send('mails.upload_doc',['name' => $canID->name,'token' => $token], function($message) use ($canID)
                {
                    $to_emails = $canID->email;
                    $message->to($to_emails)->subject('Upload Document Mail From HRMS');
                });
                $arr['doc_link'] = 1;   
                } 
            }else{
                 $arr['status'] = 0; 
            }
          
          DB::table('candidate_details')->where('id',$canID->id)->update($arr);
        }
         
      }
      
      Session::flash('msg', 'Application Updated Successfully !!'); 
      Session::flash('type', 'success');
      return redirect()->back();
      
    }
    
    public function bgv_remark(Request $request, $token){
       
      $canID = Candidate::where('token_no',$token)->select('id','name','email')->first();
      $assign = new Assigning();
      $assign->assign_from = Auth::user()->id;
      $assign->assign_to = 0;
      $assign->candidate_id = $canID->id;
      $assign->int_mode = 0;
      $assign->int_round = 7; 
      if($assign->save()){
        $remark = new Remark();
        $remark->assigned_id = $assign->id;
        $remark->candidate_id = $canID->id;
        $remark->rating = 0;
        $remark->re_status = $request->bgv_verify;
        $remark->remark = $request->bgv_remark;
        if($remark->save()){
            $arr = array();
            if(($request->bgv_verify == "Yes")){
                $checkExp = Experience::where('candidate_id',$canID->id)->select('exp_level')->first();
                Mail::send('mails.upload_doc',['name' => $canID->name,'token' => $token], function($message) use ($canID)
                {
                    $to_emails = $canID->email;
                    $message->to($to_emails)->subject('Upload Document Mail From HRMS');
                });
                $arr['doc_link'] = 1;   
              
            }else{
                 $arr['status'] = 0; 
            }
          
          DB::table('candidate_details')->where('id',$canID->id)->update($arr);
        }
         
      }
      
      Session::flash('msg', 'Application Updated Successfully !!'); 
      Session::flash('type', 'success');
      return redirect()->back();
      
    }
    
    public function closed_candidate(){
      return view('admin_pages.closed_candidate');
    }
    
    public function closed_candidate_data(){
      $candi_array = [];
      if(Auth::user()->id == 1){
         $allUData = DB::table('assigning')->distinct()->pluck('candidate_id');
      }else{
         $allUData = DB::table('assigning')->where('assign_to',Auth::user()->id)->distinct()->pluck('candidate_id');
      }
      
      foreach($allUData as $aud){
         if(Auth::user()->id != 1){
             $d = DB::table('assigning')->where('candidate_id',$aud)->where('assign_to',Auth::user()->id)->first();
         }else{
             $d = DB::table('assigning')->where('candidate_id',$aud)->first();
         }
         array_push($candi_array,$d->id);
      }
     
      $data = Assigning::with('candidate')
              ->whereHas('candidate', function ($query) {
              $query->where('status', 0);
              });
             
              
      if(Auth::user()->id != 1){
         $data->where('assign_to',Auth::user()->id);
      }
      
      $data->whereIn('id',$candi_array)->orderby('id', 'desc')->get();
     
        return DataTables::of($data)
        ->addIndexColumn()
        // ->addColumn('date',function($data){
        //   return date('d-M-Y',strtotime($data->int_date)); 
        // })
        ->addColumn('token',function($data){
          return $data->candidate->token_no; 
        })
        ->addColumn('name',function($data){
          return $data->candidate->name; 
        })
        ->addColumn('phone_no',function($data){
          return $data->candidate->phone_no; 
        })
        ->addColumn('email',function($data){
          return $data->candidate->email; 
        })
        ->addColumn('from',function($data){
          return $data->from->name; 
        })
        ->addColumn('address',function($data){
          return $data->candidate->address; 
        })
        ->addColumn('options',function($data){
            $token_no = optional($data->candidate)->token_no;
            $html = '<center> <a class="btn btn-primary btn-sm" href="'.route('candidateView',['token' => $token_no]).'" title="View"><i class="fa fa-eye"></i></a></center>';
            return $html; 
        })
        ->rawColumns(['options'])
          ->filter(function ($query) {
            if (request()->has('search') && request()->input('search.value') != '') {
                $searchValue = request()->input('search.value');
                $query->whereHas('candidate', function ($q) use ($searchValue) {
                    $q->where('token_no', 'like', "%{$searchValue}%")
                      ->orwhere('email', 'like', "%{$searchValue}%")
                      ->orWhere('name', 'like', "%{$searchValue}%")
                      ->orWhere('phone_no', 'like', "%{$searchValue}%")
                      ->orWhere('address', 'like', "%{$searchValue}%");
                });
            }
        })
        ->make(true);
    }
    
    public function users(){
        $users = User::where('id', '!=', 1)->orderby('id','desc')->get();
        return view('admin_pages.users',compact('users'));
    }
    
    public function user_form_post(Request $request){
        
        $emailExists = User::where('email', $request->user_email)->exists();
        $userIdExists = User::where('user_id', $request->user_id)->exists();
        
        if ($emailExists) {
            Session::flash("msg", "Email already exists!"); 
            Session::flash("type", "error");
            return redirect()->back(); 
        }
        
        if ($userIdExists) {
            Session::flash("msg", "User ID already exists!"); 
            Session::flash("type", "error");
            return redirect()->back(); 
        }
        
        if ($request->user_id != NULL && $request->password===$request->c_password) {
            $user = new User();
            $user->name = $request->user_name;
            $user->email = $request->user_email;
            $user->user_id = $request->user_id;
            $user->designation = $request->designation;
            $user->password = Hash::make($request->password);
            $user->user_status = 1;
            $user->save();
        
            Session::flash("msg", "User successfully created!"); 
            Session::flash("type", "success");
            return redirect()->back(); 
        }
    }
    
    public function user_form_edit_post(Request $request){
     
        $emailExists = User::where('email', $request->user_email)
                        ->where('id', '!=', $request->u_id)
                        ->exists();
    
        $userIdExists = User::where('user_id', $request->user_id)
                            ->where('id', '!=', $request->u_id)
                            ->exists();
    
        if ($emailExists) {
            Session::flash("msg", "Email already exists!"); 
            Session::flash("type", "error");
            return redirect()->back(); 
        }
        
        if ($userIdExists) {
            Session::flash("msg", "User ID already exists!"); 
            Session::flash("type", "error");
            return redirect()->back(); 
        }
        
        $user_data = User::find($request->u_id);

        $user_data->name = $request->user_name;
        $user_data->email = $request->user_email;
        $user_data->user_id = $request->user_id;
        $user_data->designation = $request->designation;
    
        if ($request->filled('password')) {
            $user_data->password = Hash::make($request->password);
        }
        $user_data->save();
        Session::flash("msg", "User successfully updated!"); 
        Session::flash("type", "success");
        return redirect()->back();
    }
    
    public function update_user_status(Request $request){
         $data = User::find($request->user_id);
         $newStatus = $data->user_status == 1 ? 0 : 1; 
         $user = User::find($request->user_id);
        if ($user) {
            $user->user_status = $newStatus;
            $user->save();
           return response(['success' => true,'msg' =>"User status updated successfully!"]);   
        }else{
            return response(['success' => false, 'msg' =>"User status not updated!"]); 
        }
    }

    public function update_password(Request $request){
        
        $request->validate([
            'current_password' => ['required', new MatchOldPassword],
            'new_password' => ['required'],
            'new_confirm_password' => ['same:new_password'],
        ]);
   
        User::find(auth()->user()->id)->update(['password'=> Hash::make($request->new_password)]);
   
        Session::flash('msg', 'New password updated successfully !!'); 
        Session::flash('type', 'success');

        return redirect()->back();
    }
}
