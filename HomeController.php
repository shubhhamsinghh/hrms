<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Candidate;
use App\Models\Department;
use App\Models\Education;
use App\Models\Experience;
use App\Models\User;
use App\Models\Bgvveri;
use App\Models\Bgvdocument;
use App\Models\Designations;
use App\Models\Document;
use Auth, DB, Session, File, Mail;
use Carbon\Carbon;
use App\Models\Assigning;

class HomeController extends Controller
{
    public function default()
    {
        return redirect()->route('index');
    }
    
    public function index()
    {
      return view('index');
    }
    
    public function verify_token(Request $request)
    {
      if($request->code == null){
         Session::flash("msg", "Interview Code can't be null"); 
         Session::flash("type", "error");
         return redirect()->back(); 
      }else{
         if(Candidate::where('token_no',$request->code)->exists()){
             $candidate = Candidate::where('token_no',$request->code)->first();
             $assigning = Assigning::where('candidate_id',$candidate->id)->where('int_round','>', 1)->first();
             if($assigning == null){
                Session::put('verifyToken',['code' => $request->code]);
                return redirect()->route('interview');  
             }else{
                $msg = 'URL not found';
                   return view('404',compact('msg')); 
             }
            
         }else{
            Session::flash("msg", "Invalid Interview Code"); 
            Session::flash("type", "error");
            return redirect()->back(); 
         }
      }
    }
    
    public function interview()
    {
        $getToken = Session::get('verifyToken');
        $candidate = Candidate::where('token_no',$getToken['code'])->first();
        $assigning = Assigning::where('candidate_id',$candidate->id)->where('int_round','>', 1)->first();
        if($assigning == null){
            $candidate = Candidate::with('education','experience','recruiter')->where('token_no',$getToken['code'])->first();
            $departments = Department::orderby('dept_name')->get();
            $designations = Designations::orderby('des_name')->get();
            $recruiters = User::where('designation','HR')->where('user_status',1)->get();
            return view('interview',compact('departments','candidate','designations','recruiters'));
        }else{
            $msg = 'URL not found';
            return view('404',compact('msg')); 
        }
    }
    
    public function interview_details(Request $request){
        
        $getToken = Session::get('verifyToken');
        $can_id = Candidate::where('token_no',$getToken['code'])->first();
        $can_data = [ 
            'department_id' => $request->department_id,
            'apply_for' => $request->apply_for,
            'interview_date' => $request->interview_date,
            'recruiter' => $request->recruiter,
            'referral_by' => $request->referral_by,
            'ref_emp_name' => $request->ref_emp_name,
            'ref_emp_id' => $request->ref_emp_id,
            'source' => $request->source,
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
        
        $candidate = Candidate::where('token_no',$getToken['code'])->update($can_data);
    
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
        return redirect()->back();
    }
    
    public function background_verifi($token){
       
         $candidate = Candidate::where('token_no',$token)->first();
         
         if($candidate != null){
            if($candidate->bgv_receive_on == null){
              $bgbSentOn = Carbon::parse($candidate->bgv_sent_on);
              $currentTime = Carbon::now();
              $hoursDifference = $bgbSentOn->diffInHours($currentTime);
               
              if ($hoursDifference >= 96) {
                    $msg = 'Link has been expired';
                    return view('404',compact('msg'));
                } else {
                    return view('background_verifi',compact('candidate'));
                }
            }else{
                   $msg = 'BGV already done';
                   return view('404',compact('msg'));
            }
          }else{
                   $msg = 'URL not found';
                   return view('404',compact('msg'));
          }
    }
    
     public function background_verifi_submit(Request $request,$token){
         
         $candidate = Candidate::where('token_no',$token)->first();
         $record1 = new Bgvveri();
         $record1->candidate_id = $candidate->id;
         $record1->company_name = $request->company1;
         $record1->location = $request->location1;
         $record1->profile = $request->profile1;
         $record1->emp_id = $request->emp_id1;
         $record1->hr_name = $request->hr_name1;
         $record1->hr_phone = $request->hr_phone1;
         $record1->hr_mail = $request->hr_mail1;
         $record1->manager_name = $request->manager_name1;
         $record1->manager_phone = $request->manager_phone1;
         $record1->manager_mail = $request->manager_mail1;
         $record1->leaving_reason = $request->leaving_reason1;
         $record1->save();
         
         $bgbveri['bgv_id'] = $record1->id;
         $bgbveri['candidate_id'] = $candidate->id;
         
        $filename=''; $filename1='';
        $destinationpath = 'images/bgv-doc/';
         
        if (request()->hasFile('offer_latter1')) {
            if ($request->file('offer_latter1')->isValid()) {
                $file   = $request->file('offer_latter1')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('offer_latter1')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('offer_latter1')->move($destinationpath, $filename1);
                $bgbveri['offer_latter'] = $filename1;
            }
        }  
        if (request()->hasFile('relieving_letter1')) {
            if ($request->file('relieving_letter1')->isValid()) {
                $file = $request->file('relieving_letter1')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('relieving_letter1')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('relieving_letter1')->move($destinationpath, $filename1);
                $bgbveri['relieving_letter'] = $filename1;
            }
         }
        if (request()->hasFile('exp_certificate1')) {
            if ($request->file('exp_certificate1')->isValid()) {
                $file = $request->file('exp_certificate1')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('exp_certificate1')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('exp_certificate1')->move($destinationpath, $filename1);
                $bgbveri['exp_certificate'] = $filename1;
            }
        }
        if (request()->hasFile('salary_slip')) {
             foreach ($request->file('salary_slip') as $key=>$image) {
              if ($image->isValid()) {
                $file = $image->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $image->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $image->move($destinationpath, $filename1);
                $bgbveri['salary_slip'.$key] = $filename1;
             }
           }
         }
        
        Bgvdocument::insert($bgbveri); 
        
        $record2 = new Bgvveri();
         $record2->candidate_id = $candidate->id;
         $record2->company_name = $request->company2;
         $record2->location = $request->location2;
         $record2->profile = $request->profile2;
         $record2->emp_id = $request->emp_id2;
         $record2->hr_name = $request->hr_name2;
         $record2->hr_phone = $request->hr_phon21;
         $record2->hr_mail = $request->hr_mail2;
         $record2->manager_name = $request->manager_name2;
         $record2->manager_phone = $request->manager_phone2;
         $record2->manager_mail = $request->manager_mail2;
         $record2->leaving_reason = $request->leaving_reason2;
         $record2->save();
         
         $bgbveri1['bgv_id'] = $record2->id;
         $bgbveri1['candidate_id'] = $candidate->id;
         
        if (request()->hasFile('offer_latter2')) {
            if ($request->file('offer_latter2')->isValid()) {
                $file   = $request->file('offer_latter2')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('offer_latter2')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('offer_latter2')->move($destinationpath, $filename1);
                $bgbveri1['offer_latter'] = $filename1;
            }
        }  
        if (request()->hasFile('relieving_letter2')) {
            if ($request->file('relieving_letter2')->isValid()) {
                $file = $request->file('relieving_letter2')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('relieving_letter2')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('relieving_letter2')->move($destinationpath, $filename1);
                $bgbveri1['relieving_letter'] = $filename1;
            }
         }
        if (request()->hasFile('exp_certificate2')) {
            if ($request->file('exp_certificate2')->isValid()) {
                $file = $request->file('exp_certificate2')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('exp_certificate2')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('exp_certificate2')->move($destinationpath, $filename1);
                $bgbveri1['exp_certificate'] = $filename1;
            }
        }
        if (request()->hasFile('salary_slip2')) {
             foreach ($request->file('salary_slip2') as $key=>$image) {
              if ($image->isValid()) {
                $file = $image->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $image->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $image->move($destinationpath, $filename1);
                $bgbveri1['salary_slip'.$key] = $filename1;
             }
           }
         }
        
        Bgvdocument::insert($bgbveri1); 
        DB::table('candidate_details')->where('id',$candidate->id)->update(['bgv_link' => 2, 'bgv_receive_on' => date('Y-m-d H:i:s')]);
        
        Session::flash('msg', 'Background Verification Submitted Successfully !!'); 
        Session::flash('type', 'success');
        $msg = 'URL not found';
        return view('404',compact('msg'));
    }
    
    public function upload_doc($token){
        $candidate = Candidate::where('token_no',$token)->first();
        if($candidate != null && $candidate->doc_link ==1){
             return view('upload_doc',compact('candidate'));
        }else{
             $msg = 'URL not found';
             return view('404',compact('msg'));
        }
    }
    
    public function upload_doc_submit(Request $request, $token){
        $candidate = Candidate::where('token_no',$token)->first();
        if($candidate != null && $candidate->doc_link ==1){
            
        $filename=''; $filename1='';
        $destinationpath = 'images/documents/';
        
         if (request()->hasFile('marksheets')) {
             foreach ($request->file('marksheets') as $key=>$image) {
              if ($image->isValid()) {
                $file = $image->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $image->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $image->move($destinationpath, $filename1);
                DB::table('documents')->insert(['candidate_id' => $candidate->id, 'type' => 'marksheet', 'doc' => $filename1]);
             }
           }
         }
         
        if (request()->hasFile('certificates')) {
             foreach ($request->file('certificates') as $key=>$image) {
              if ($image->isValid()) {
                $file = $image->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $image->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $image->move($destinationpath, $filename1);
                DB::table('documents')->insert(['candidate_id' => $candidate->id, 'type' => 'certificate', 'doc' => $filename1]);
             }
           }
         }
        
        if (request()->hasFile('aadhar_card')) {
            if ($request->file('aadhar_card')->isValid()) {
                $file = $request->file('aadhar_card')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('aadhar_card')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('aadhar_card')->move($destinationpath, $filename1);
                DB::table('documents')->insert(['candidate_id' => $candidate->id, 'type' => 'aadhar card', 'doc' => $filename1]);
            }
        }
        
        if (request()->hasFile('pan_card')) {
            if ($request->file('pan_card')->isValid()) {
                $file = $request->file('pan_card')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('pan_card')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('pan_card')->move($destinationpath, $filename1);
                DB::table('documents')->insert(['candidate_id' => $candidate->id, 'type' => 'pan card', 'doc' => $filename1]);
            }
        }
        
        if (request()->hasFile('photo')) {
            if ($request->file('photo')->isValid()) {
                $file = $request->file('photo')->getClientOriginalName();
                $filename = pathinfo($file, PATHINFO_FILENAME);
                $filename = strtolower($filename);
                $filename = preg_replace("/[^a-z0-9_\s-]/", "", $filename);
                $filename = preg_replace("/[\s-]+/", " ", $filename);
                $filename = preg_replace("/[\s_]/", "-", $filename);
                $extension1 = $request->file('photo')->getClientOriginalExtension();
                $filename1 = $filename. '_' . rand(11111, 99999) . '.' . $extension1;
                $request->file('photo')->move($destinationpath, $filename1);
                DB::table('documents')->insert(['candidate_id' => $candidate->id, 'type' => 'photo', 'doc' => $filename1]);
            }
        }
            
       
        DB::table('candidate_details')->where('id',$candidate->id)->update(['doc_link' => 2, 'status' => 0]);
        
             Session::flash('msg', 'Document Submitted Successfully!!'); 
             Session::flash('type', 'success');
             $msg = 'URL not found';
             return view('404',compact('msg'));
        }else{
             $msg = 'URL not found';
             return view('404',compact('msg'));
        }
    }
}
