@extends('layouts.admin_layout') @section('content')
<style>
    .dis-none{
        display: none;
    }
    .dis-block{
        display: block;
    }
    label{
        display: inline !important;
    }
</style>
<div class="content-wrapper">
    <section class="content-header">
        <h1>
            Cadidate Details [{{$data->token_no}}]
        </h1>
        <ol class="breadcrumb">
            <li>
                <a href="{{route('adminHome')}}"><i class="fa fa-dashboard"></i> Home</a>
            </li>
            <li>Cadidate Details</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <!--<div class="col-md-1"></div>  -->
            <div class="col-md-12">
                <!-- Horizontal Form -->
                <div class="box">
                    <!-- /.box-header -->
                        <div class="box-body">
                            <div class="box-header with-border">
                            <h3 class="box-title pull-right">
                                <?php $int_r = null;
                                if(count($assigning) > 0){$int_r = $assigning[0]->int_round;}
                                if($hr_re != null){
                                if($data->bgv_link == 2 && $data->bgv_receive_on != null && $data->doc_link == 0){ 
                                if(Auth::user()->id === 1|| $hr_re->assign_to == Auth::user()->id){ ?>
                                <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#bgbRemark">BGV Remark</a>
                                <?php } } if(($int_r < 6 && Auth::user()->id === 1) || ($int_r < 6 && $hr_re->assign_to == Auth::user()->id)){ ?>
                                <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#finalRemark">Final HR Remark</a>
                                <?php } if($int_r === 4){ 
                                 $mn_re = \DB::table('assigning')->where('candidate_id',$data->id)->where('int_round',4)->first(); 
                                 if($mn_re->assign_to == Auth::user()->id){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#remarkManager">Manager Remark</a>
                                <?php } }elseif(($int_r === 3 && Auth::user()->id === 1) || ($int_r === 3 && $hr_re->assign_to == Auth::user()->id)){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#assignManager">Assign to Manager</a>
                                <?php }elseif($int_r === 2){
                                 $tl_re = \DB::table('assigning')->where('candidate_id',$data->id)->where('int_round',2)->first();
                                if($tl_re->assign_to == Auth::user()->id){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#remarkTL">TL Remark</a>
                                <?php } }elseif(($int_r === 1 && Auth::user()->id === 1) || ($int_r === 1 && $hr_re->assign_to == Auth::user()->id)){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#assignTL">Assign to TL</a>
                                <?php }elseif($int_r === 0){ 
                                if($hr_re->assign_to == Auth::user()->id){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#remarkHR">HR Remark</a>
                                <?php } } }
                                if($int_r === null){ ?>
                                    <a href="javascript:void(0)" class="btn btn-flat btn-primary" data-toggle="modal" data-target="#assignHR">Assign to HR</a>
                                <?php } ?>
                               
                            </h3>
                    </div>
                    <!--<div class="box-body">-->
                     <div class="nav-tabs-custom">
                       <ul class="nav nav-tabs" id="myTab">
                           <li class="active"><a href="#initial_info" data-toggle="tab" aria-expanded="false"><b><i class="fa fa-calendar text-blue" style="width:15px"></i> Initial information </b></a></li>       
                           <li><a href="#assigned" data-toggle="tab" aria-expanded="true"><b><i class="fa fa-birthday-cake text-blue" style="width:15px"></i> Assigned </b></a></li>
                           <li><a href="#bgv" data-toggle="tab"><b><i class="fa fa-briefcase text-blue" style="width:15px"></i> BGV </b></a></li>
                           <li><a href="#documents" data-toggle="tab"><b><i class="fa fa-briefcase text-blue" style="width:15px"></i> Documents </b></a></li>
        			   </ul>
                    <div class="tab-content">
                      <div class="tab-pane mail-scroll active" id="initial_info">
                           <p style="color:red"><u>Initial information</u></p>
                                <div class="row">
                                    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Department :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->department->dept_name}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Job Role :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->jobrole->des_name}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Referred :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->referral_by}}</p></div>
    								</div>
    							   
    								@if($data->referral_by == "Employee")
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Employee Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->ref_emp_name}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Employee ID :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->ref_emp_id}}</p></div>
    								</div>
    								@else
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Source :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->source}}</p></div>
    								</div>
    								@endif
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Location :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->location}}</p></div>
    								</div>
    							
    							
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Shift :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->shift}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Rotational Shift :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    								<div class="form-group"><p>{{$data->r_shift}}</p></div>
    								</div>
                                </div>
                                <hr>
                              
                                <p style="color:red"><u>Personal Information</u></p>
                                <div class="row">
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$data->name}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Father's Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$data->father_name}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Mobile No :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$data->phone_no}}</p></div>
    								</div>
                               
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Email ID :</strong> </label></div>
    								</div>
    								<div class="col-md-3">	
    										<div class="form-group"><p>{{$data->email}}</p></div>
    								</div>
    								
    								<div class="col-md-1">	
    									<div class="form-group"><label><strong>DOB :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$data->dob}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Age :</strong> </label></div>
    								</div>
    								<?php
                                    $dob = new DateTime($data->dob);
                                    $today = new DateTime();
                                    $age = $dob->diff($today)->y;
                                    ?>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$age}} Year</p></div>
    								</div>
                               
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Gender :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$data->gender}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>DOB :</strong> </label></div>
    								</div>
    								<div class="col-md-6">	
    										<div class="form-group"><p>{{$data->address}}</p></div>
    								</div>
                                </div>
                                <hr>
                                
                                <p style="color:red"><u>Academic Information</u></p>
                                <div class="row">
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Secondary School (10th) :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->high_school) ? $data->education->high_school : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Higher Secondary (12th) :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->intermediate) ? $data->education->intermediate : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Diploma :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->diploma) ? $data->education->diploma : '-'}}</p></div>
    								</div>
                                
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Graduation :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->graduation) ? $data->education->graduation : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Post Graduation :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->pg) ? $data->education->pg : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Doctorate :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->education->master) ? $data->education->master : '-'}}</p></div>
    								</div>
                                </div>
                                <hr>
                                
                                <p style="color:red"><u>Employment & Salary Information</u></p>
                                 <div class="row">
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Experience Level:</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{$data->experience->exp_level}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Total Work Experience :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    										<div class="form-group"><p>{{($data->experience->exp_year != 0) ? $data->experience->exp_year." Year" : '-'}} </p></div>
    								</div>
    								<div class="col-md-1">	
    										<div class="form-group"><p>{{($data->experience->exp_moth != 0) ? $data->experience->exp_moth." Month" : '-'}} </p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Current Salary (Monthly) :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->experience->curr_month_salary) ? $data->experience->curr_month_salary : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Current CTC (Yearly) :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->experience->curr_year_salary) ? $data->experience->curr_year_salary : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Expected Salary (Monthly) :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>{{isset($data->experience->expec_month_salary) ? $data->experience->expec_month_salary : '-'}}</p></div>
    								</div>
                               
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Resume :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    										<div class="form-group"><p>@if(isset($data->experience->resume))<a href="{{asset('images/resume/'.$data->experience->resume)}}" target="_blank">View</a> @else - @endif</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    										<div class="form-group"><label><strong> Porfolio :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>@if(isset($data->experience->portfolio))<a href="{{asset('images/portfolio/'.$data->experience->portfolio)}}" target="_blank">View</a> @else - @endif</p></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Additional Portfolio Links :</strong> </label></div>
    								</div>
    								<div class="col-md-6">	
    										<div class="form-group"><p>{{isset($data->experience->links) ? $data->experience->links : '-'}}</p></div>
    								</div>
                                  </div>
                            </div>
                      <div class="tab-pane mail-scroll" id="assigned" >             
                          @foreach($assigning as $key=>$assign)
                            <?php if($assign->int_round >= 0 && $assign->int_round < 2){ $heding = "HR";}
                            elseif($assign->int_round >= 2 && $assign->int_round < 4){ $heding = "Team Lead";}
                            elseif($assign->int_round >= 4 && $assign->int_round < 6){ $heding = "Manager";}
                            else{ $heding = "Final"; } ?>
                             
                            <?php if($assign->int_round === 6){ ?>
                            <p style="color:red"><u>Final Remark</u></p>
                             <?php $mark = \DB::table('remarks')->where('assigned_id',$assign->id)->first();
							  if($mark != null){ $values = explode("/", $mark->re_status); ?>
                            <div class="row">
                                <div class="col-md-1">	
									<div class="form-group"><label><strong>Interested :</strong> </label></div>
								</div>
								<div class="col-md-2">
									<div class="form-group"><p>{{$values[0]}}</p></div>
								</div>
								
								<div class="col-md-1">	
									<div class="form-group"><label><strong>Status :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{$values[1]}}</p></div>
								</div>
								
								<div class="col-md-1">	
									<div class="form-group"><label><strong>Remark :</strong> </label></div>
								</div>
								<div class="col-md-5">	
									<div class="form-group"><p>{{$mark->remark}}</p></div>
								</div>
								
							   	<div class="col-md-2">	
									<div class="form-group"><label><strong>Designation :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{isset($data->designation) ? $data->designation : '-'}}</p></div>
								</div>
								
								<div class="col-md-2">	
									<div class="form-group"><label><strong>Salary :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{isset($data->salary) ? $data->salary : '-'}}</p></div>
								</div>
								
								<div class="col-md-2">	
									<div class="form-group"><label><strong>Doj :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{isset($data->Doj) ? date('d-M-Y', strtotime($data->Doj)) : '-'}}</p></div>
								</div>
								
							   </div>
                            <?php } } if($assign->int_round < 6){ ?>
                            <p style="color:red"><u>Assigned {{$heding}}</u></p>
                            <div class="row">
                                <div class="col-md-1">	
									<div class="form-group"><label><strong>To :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{$assign->to->name}}</p></div>
								</div>
								
								<div class="col-md-1">	
									<div class="form-group"><label><strong>From :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{$assign->from->name}}</p></div>
								</div>
								
								<div class="col-md-1">	
									<div class="form-group"><label><strong>Interview Mode :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{$assign->int_mode}}</p></div>
								</div>
								
								<div class="col-md-1">	
									<div class="form-group"><label><strong>Date :</strong> </label></div>
								</div>
								<div class="col-md-2">	
									<div class="form-group"><p>{{$assign->int_date}} [ {{date('h:i A',strtotime($assign->int_time))}} ]</p></div>
								</div>
							   </div>
							 <?php 
							    $mark = \DB::table('remarks')->where('assigned_id',$assign->id)->first();
							    if($mark != null){ ?>
							    <div class="row">
                                    <div class="col-md-1">	
    									<div class="form-group"><label><strong>Rating :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$mark->rating}}</p></div>
    								</div>
								
                                    <div class="col-md-1">	
    									<div class="form-group"><label><strong>Status :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{$mark->re_status}}</p></div>
    								</div>
    								
    								<div class="col-md-1">	
    									<div class="form-group"><label><strong>comment :</strong> </label></div>
    								</div>
    								<div class="col-md-5">	
    									<div class="form-group"><p>{{$mark->remark}}</p></div>
    								</div>
							      </div>
								<?php $man_rting = DB::table('manager_ratings')->where('remark_id',$mark->id)->first(); if($man_rting != null){ ?>
								  <div class="row">
								    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Work Experience :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->work_exe}}</p></div>
    								</div>
								
								    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Applicable Skillse :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->applicable_skl}}</p></div>
    								</div>
						
								    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Appearance :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->appearance}}</p></div>
    								</div>
								
								    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Attiude :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->attiude}}</p></div>
    								</div>
								
								    <div class="col-md-2">	
    									<div class="form-group"><label><strong>Education :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->education}}</p></div>
    								</div>
								
									<div class="col-md-2">	
    									<div class="form-group"><label><strong>Enthusiasm :</strong> </label></div>
    								</div>
    								<div class="col-md-1">	
    									<div class="form-group"><p>{{$man_rting->enthusiasm}}</p></div>
    								</div>
								</div>
							   <?php } } } ?>
							   <hr>
                            @endforeach
                      </div>
                      <div class="tab-pane mail-scroll" id="bgv"> 
                      <?php if($data->bgv_link == 1 || $data->bgv_link == 2){ ?>
                            <p style="color:red"><u>BGV</u></p>
                            <div class="row">
                                <div class="col-md-2">	
									<div class="form-group"><label><strong>BGV Sent On :</strong> </label></div>
								</div>
								<div class="col-md-3">
									<p>{{date('d-m-Y',strtotime($data->bgv_sent_on))}}</p>
								</div>
								
								<?php if($data->bgv_link == 2){ ?>
								<div class="col-md-2">	
									<div class="form-group"><label><strong>BGV Received On :</strong> </label></div>
								</div>
								<div class="col-md-3">	
									<p>{{date('d-m-Y',strtotime($data->bgv_receive_on))}}</p>
								</div>
								 <?php } ?>
								</div>
							<hr>
							<?php } ?>
							<?php if($data->bgv_link == 2){ ?>
							 
							<?php foreach($bgb as $b){ ?>
							  <div class="row">
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Company Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->company_name) ? $b->company_name : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Location :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->location) ? $b->location : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Profile :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->profile) ? $b->profile : '-'}}</p></div>
    								</div>
								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Employee ID :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->emp_id) ? $b->emp_id : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> HR Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->hr_name) ? $b->hr_name : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> HR Number :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->hr_phone) ? $b->hr_phone : '-'}}</p></div>
    								</div>
							
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>HR Mail ID:</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->hr_mail) ? $b->hr_mail : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Manager Name :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->manager_name) ? $b->manager_name : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Manager Number :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->manager_phone) ? $b->manager_phone : '-'}}</p></div>
    								</div>
								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong>Manager Mail ID:</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>{{isset($b->manager_mail) ? $b->manager_mail : '-'}}</p></div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Reason For Leaving:</strong> </label></div>
    								</div>
    								<div class="col-md-5">	
    									<div class="form-group"><p>{{isset($b->leaving_reason) ? $b->leaving_reason : '-'}}</p></div>
    								</div>
    								
    								<?php if($b->bgvdocuments != null){ ?>
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Offer Latter:</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>
    									    <?php if(isset($b->bgvdocuments->offer_latter)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->offer_latter).' target="_blank">View</a>';}else{ echo '-';} ?>
    									    </p>
    									</div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Relieving Letter:</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>
    									    <?php if(isset($b->bgvdocuments->relieving_letter)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->relieving_letter).' target="_blank">View</a>';}else{ echo '-';} ?>
    									    </p>
    									</div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Exp Certificate :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>
    									    <?php if(isset($b->bgvdocuments->exp_certificate)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->exp_certificate).' target="_blank">View</a>';}else{ echo '-';} ?>
    									    </p>
    									</div>
    								</div>
    								
    								<div class="col-md-2">	
    									<div class="form-group"><label><strong> Salary Slip :</strong> </label></div>
    								</div>
    								<div class="col-md-2">	
    									<div class="form-group"><p>
    									    
    									    <?php if(!empty($b->bgvdocuments->salary_slip0)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->salary_slip0).' target="_blank">View</a>';}else{ echo '-';} ?>
    									    <?php if(!empty($b->bgvdocuments->salary_slip1)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->salary_slip1).' target="_blank">View</a>';}else{ echo '';} ?>
    									    <?php if(!empty($b->bgvdocuments->salary_slip2)){ echo '<a href='.asset("images/bgv-doc/".$b->bgvdocuments->salary_slip2).' target="_blank">View</a>';}else{ echo '';} ?>
    									    </p>
    									</div>
    								</div>
    								
    								<?php } ?>
    								
								  	</div>
								  	
							      <hr>
								<?php } ?>
						
						  <?php } ?>
                      </div>
                      <div class="tab-pane mail-scroll" id="documents"> 
                         <?php if($data->doc_link == 2){ ?>
                            <p style="color:red"><u>Documents</u></p>
                            <div class="row">
                                @foreach($data->document as $doc)
                                <div class="col-md-2">	
									<div class="form-group"><a href="{{asset('images/documents/'.$doc->doc)}}" target="_blank">View {{ucwords($doc->type)}} </p></div>
								</div>
							
								@endforeach
								</div>
							<hr>
                        <?php } ?>
                      </div>
                    </div>
                    <!--</div>-->
                   </div>
                  </div>
                </div> 
               </div>
            </div>
    </section>
</div>
   
<?php if($int_r === null){ ?>
<!-- Assign HR Modal-->
<div class="modal fade" id="assignHR" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Assign To HR</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('assigning',['token'=>$data->token_no])}}">
                        @csrf
                            <div class="col-md-12">
                            <div class="form-group">
                               <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="recruiter">Assign To <span style="color: red;">*</span></label>
                                        <select class="form-control" name="assign_to" id="recruiter" required>
                                            <option value="">-- Select Assign To --</option>
                                            @foreach($recruiters as $rec)
                                                <option value="{{ $rec->id }}">{{ $rec->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 48%;">
                                        <label for="recruiter">Interview Mode <span style="color: red;">*</span></label>
                                        <br>
                                         <input type="radio" id="telephonic" name="int_mode" value="Telephonic" checked> <label for="telephonic"> Telephonic</label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="virtual" name="int_mode" value="Virtual"> <label for="virtual"> Virtual </label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="f2f" name="int_mode" value="F2F"> <label for="f2f"> F2F</label>
                                    </div>
                                    
                               </div>
                            </div>
                          
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="int_date">Date <span style="color: red;">*</span></label>
                                        <input type="date" class="form-control" name="int_date" id="int_date" required>
                                    </div>
                                    
                                    <div style="width: 48%;">
                                       <label for="location">Timing</label>
                                       <input type="time" class="form-control" name="int_time" id="int_time"> 
                                    </div>
                                  
                               </div>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                      </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php } 
if($int_r === 0){ ?>
<!-- HR Remark Modal-->
<div class="modal fade" id="remarkHR" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">HR Remark</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('addRemark',['token'=>$data->token_no])}}" >
                        @csrf
                            <div class="col-md-12">
                            <input type="hidden" name="assigned_id" value="{{isset($hr_re) ? $hr_re->id : '' }}">
                            <input type="hidden" name="key" value="hr">
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 60%;">
                                        <label for="rating">Rating <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="poor" name="rating" value="2" checked> <label for="poor"> (1-2 Poor)</label> 
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="fair" name="rating" value="3"> <label for="fair">(3 Fair) </label> 
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="good" name="rating" value="4"> <label for="good"> (4 Good)</label>
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="exe" name="rating" value="5"> <label for="exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 39%;">
                                       <label for="location">Status <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="shortlisted" name="re_status" value="Shortlisted" checked> <label for="shortlisted "> Shortlisted </label> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="onhold" name="re_status" value="On Hold"> <label for="onhold"> On Hold </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="rejected" name="re_status" value="Rejected"> <label for="rejected"> Rejected </label> 
                                    </div>
                                  
                               </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="remark">Remark <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="remark" rows="3" cloumn="30" required></textarea>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                      </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php  }if($int_r === 1){ ?>
<!-- Assign TL Modal-->
<div class="modal fade" id="assignTL" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Assign To HR</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('assigning',['token'=>$data->token_no])}}">
                        @csrf
                            <div class="col-md-12">
                            <div class="form-group">
                               <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="recruiter">Assign To <span style="color: red;">*</span></label>
                                        <select class="form-control" name="assign_to" id="recruiter" required>
                                            <option value="">-- Select Assign To --</option>
                                            @foreach($tls as $tl)
                                                <option value="{{ $tl->id }}">{{ $tl->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 48%;">
                                        <label for="recruiter">Interview Mode <span style="color: red;">*</span></label>
                                        <br>
                                         <input type="radio" id="telephonic" name="int_mode" value="Telephonic" checked> <label for="telephonic"> Telephonic</label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="virtual" name="int_mode" value="Virtual"> <label for="virtual"> Virtual </label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="f2f" name="int_mode" value="F2F"> <label for="f2f"> F2F</label>
                                    </div>
                                    
                               </div>
                            </div>
                          
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="int_date">Date <span style="color: red;">*</span></label>
                                        <input type="date" class="form-control" name="int_date" id="int_date" required>
                                    </div>
                                    
                                    <div style="width: 48%;">
                                       <label for="location">Timing</label>
                                       <input type="time" class="form-control" name="int_time" id="int_time"> 
                                    </div>
                                  
                               </div>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                      </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php }if($int_r === 2){ ?>
<!-- TL Remark Modal-->
<div class="modal fade" id="remarkTL" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">TL Remark</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('addRemark',['token'=>$data->token_no])}}" >
                        @csrf
                            <div class="col-md-12">
                            <input type="hidden" name="assigned_id" value="{{isset($tl_re) ? $tl_re->id : '' }}">
                            <input type="hidden" name="key" value="tl">
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 60%;">
                                        <label for="rating">Rating <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="poor" name="rating" value="2" checked> <label for="poor"> (1-2 Poor)</label> 
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="fair" name="rating" value="3"> <label for="fair">(3 Fair) </label> 
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="good" name="rating" value="4"> <label for="good"> (4 Good)</label>
                                         &nbsp;&nbsp;&nbsp;<input type="radio" id="exe" name="rating" value="5"> <label for="exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 39%;">
                                       <label for="location">Status <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="shortlisted" name="re_status" value="Shortlisted" checked> <label for="shortlisted"> Shortlisted</label> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="onhold" name="re_status" value="On Hold"> <label for="onhold"> On Hold </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="rejected" name="re_status" value="Rejected"> <label for="rejected"> Rejected </label> 
                                    </div>
                                  
                               </div>
                            </div>
                            
                            <div class="form-group">
                                <label for="remark">Remark <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="remark" rows="3" cloumn="30" required></textarea>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                           </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php }if($int_r === 3){ ?>
<!-- Assign Manager Modal-->
<div class="modal fade" id="assignManager" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Assign To Manager</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('assigning',['token'=>$data->token_no])}}">
                        @csrf
                            <div class="col-md-12">
                            <div class="form-group">
                               <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="assign_to">Assign To <span style="color: red;">*</span></label>
                                        <select class="form-control" name="assign_to" id="assign_to" required>
                                            <option value="">-- Select Assign To --</option>
                                            @foreach($managers as $manager)
                                                <option value="{{ $manager->id }}">{{ $manager->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div style="width: 48%;">
                                        <label for="mode">Interview Mode <span style="color: red;">*</span></label>
                                        <br>
                                         <input type="radio" id="telephonic" name="int_mode" value="Telephonic" checked> <label for="telephonic"> Telephonic</label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="virtual" name="int_mode" value="Virtual"> <label for="virtual"> Virtual </label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="f2f" name="int_mode" value="F2F"> <label for="f2f"> F2F</label>
                                    </div>
                                    
                               </div>
                            </div>
                          
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 48%;">
                                        <label for="int_date">Date <span style="color: red;">*</span></label>
                                        <input type="date" class="form-control" name="int_date" id="int_date" required>
                                    </div>
                                    
                                    <div style="width: 48%;">
                                       <label for="location">Timing</label>
                                       <input type="time" class="form-control" name="int_time" id="int_time"> 
                                    </div>
                                  
                               </div>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                      </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php }if($int_r === 4){ ?>
<!-- Manager Remark Modal-->
<div class="modal fade" id="remarkManager" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Manager Remark</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('addRemark',['token'=>$data->token_no])}}" >
                        @csrf
                            <div class="col-md-12">
                            <input type="hidden" name="assigned_id" value="{{isset($mn_re) ? $mn_re->id : '' }}">
                            <input type="hidden" name="key" value="manager">
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 50%;">
                                        <label for="rating">Rating <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="poor" name="rating" value="2" checked> <label for="poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="fair" name="rating" value="3"> <label for="fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="good" name="rating" value="4"> <label for="good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="exe" name="rating" value="5"> <label for="exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 50%;">
                                       <label for="location">Status <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="shortlisted" name="re_status" value="Shortlisted" checked> <label for="shortlisted"> Shortlisted </label> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="onhold" name="re_status" value="On Hold"> <label for="onhold"> On Hold </label>
                                        &nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="rejected" name="re_status" value="Rejected"> <label for="rejected"> Rejected </label> 
                                    </div>
                                  
                               </div>
                            </div>
                            <p style="color:red;"><u>For Final Interviews</u></p>
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 50%;">
                                        <label for="rating">Work Experience <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="work_poor" name="work_exe" value="2" checked> <label for="work_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="work_fair" name="work_exe" value="3"> <label for="work_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="work_good" name="work_exe" value="4"> <label for="work_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="work_exe" name="work_exe" value="5"> <label for="work_exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 50%;">
                                        <label for="rating">Applicable Skills <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="applicable_poor" name="applicable_skl" value="2" checked> <label for="applicable_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="applicable_fair" name="applicable_skl" value="3"> <label for="applicable_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="applicable_good" name="applicable_skl" value="4"> <label for="applicable_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="applicable_exe" name="applicable_skl" value="5"> <label for="applicable_exe"> (5 Excellent)</label>
                                    </div>
                                  
                               </div>
                            </div>
                            <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 50%;">
                                        <label for="rating">Appearance <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="appe_poor" name="appearance" value="2" checked> <label for="appe_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="appe_fair" name="appearance" value="3"> <label for="appe_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="appe_good" name="appearance" value="4"> <label for="appe_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="appe_exe" name="appearance" value="5"> <label for="appe_exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 50%;">
                                        <label for="rating">Attiude <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="attiude_poor" name="attiude" value="2" checked> <label for="attiude_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="attiude_fair" name="attiude" value="3"> <label for="attiude_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="attiude_good" name="attiude" value="4"> <label for="attiude_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="attiude_exe" name="attiude" value="5"> <label for="attiude_exe"> (5 Excellent)</label>
                                    </div>
                                  
                               </div>
                            </div>
                             <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 50%;">
                                        <label for="rating">Education <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="education_poor" name="education" value="2" checked> <label for="education_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="education_fair" name="education" value="3"> <label for="education_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="education_good" name="education" value="4"> <label for="education_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="education_exe" name="education" value="5"> <label for="education_exe"> (5 Excellent)</label>
                                    </div>
                                    
                                    <div style="width: 50%;">
                                        <label for="rating">Enthusiasm <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="enthusiasm_poor" name="enthusiasm" value="2" checked> <label for="enthusiasm_poor"> (1-2 Poor)</label> 
                                         &nbsp;<input type="radio" id="enthusiasm_fair" name="enthusiasm" value="3"> <label for="enthusiasm_fair">(3 Fair) </label> 
                                         &nbsp;<input type="radio" id="enthusiasm_good" name="enthusiasm" value="4"> <label for="enthusiasm_good"> (4 Good)</label>
                                         &nbsp;<input type="radio" id="enthusiasm_exe" name="enthusiasm" value="5"> <label for="enthusiasm_exe"> (5 Excellent)</label>
                                    </div>
                                  
                               </div>
                            </div>
                            <div class="form-group">
                                <label for="remark">Remark <span style="color: red;">*</span></label>
                                <textarea class="form-control" name="remark" rows="3" cloumn="30" required></textarea>
                            </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                      </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php }if($int_r < 6){ ?>
<div class="modal fade" id="finalRemark" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">Final Remark</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- Assinged Form Start -->
                    <form method="post" action="{{route('finalRemark',['token'=>$data->token_no])}}" >
                        @csrf
                            <div class="col-md-12">
                              <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                    <div style="width: 60%;">
                                        <label for="int">Interested <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="yes" name="interested" value="Yes" checked> <label for="yes"> Yes </label> 
                                         &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="no" name="interested" value="No"> <label for="no"> No </label> 
                                    </div>
                                    
                                    <div style="width: 39%;">
                                       <label for="location">Status <span style="color: red;">*</span></label>
                                        <br>
                                        <input type="radio" id="fi_selected" name="fi_status" value="Selected" checked> <label for="fi_selected"> Selected</label> 
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="fi_rejected" name="fi_status" value="Rejected"> <label for="fi_rejected"> Rejected </label> 
                                    </div>
                                 </div>
                               </div>
                            
                              <div class="form-group">
                                  <label for="designation"> Designation </label>
                                  <input type="text" class="form-control" name="designation">
                              </div>
                              
                              <div class="form-group">
                                <div style="display: flex; justify-content: space-between;">
                                   <div style="width: 49%;">
                                        <label for="location">Salary</label>
                                        <input type="text" class="form-control" name="salary">
                                    </div>
                                    &nbsp;&nbsp;&nbsp;
                                    <div style="width: 49%;">
                                        <label for="location">Doj</label>
                                        <input type="date" class="form-control" name="Doj">
                                    </div>
                               </div>
                              </div>
                            
                              <div class="form-group">
                                 <label for="remark">Remark <span style="color: red;">*</span></label>
                                 <textarea class="form-control" name="remark" rows="3" cloumn="30" required></textarea>
                              </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                           </div>
                    </form>
                    <!-- Assinged Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php } if($data->bgv_link == 2 && $data->bgv_receive_on != null){ ?>
<!-- BGV Remark -->
<div class="modal fade" id="bgbRemark" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h4 class="modal-title">BGV Remark</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <!-- BGV Form Start -->
                    <form method="post" action="{{route('bgvRemark',['token'=>$data->token_no])}}" >
                        @csrf
                            <div class="col-md-12">
                              <div class="form-group">
                              <label for="">BG Verify<span style="color: red;">*</span></label>
                              <br>
                              <input type="radio" id="bgv_yes" name="bgv_verify" value="Yes" checked> <label for="bgv_yes"> Yes </label> 
                              &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="radio" id="bgv_no" name="bgv_verify" value="No"> <label for="bgv_no"> No </label> 
                              </div>
                            
                            
                              <div class="form-group">
                                 <label for="remark">Remark <span style="color: red;">*</span></label>
                                 <textarea class="form-control" name="bgv_remark" rows="3" cloumn="30" required></textarea>
                              </div>
                        
                            <button type="submit" class="btn btn-success btn-flat pull-right"><i class="fa fa-hdd-o" style="width: 20px;"></i> Save</button>
                        
                           </div>
                    </form>
                    <!-- BGV Form End -->
                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>

@endsection 
@section('script')
<script>
    $(document).ready(function() {
        dateInput();
        let $timeInput = $("#int_time");
        $timeInput.on("click", function() {
            this.showPicker();
        });
    });
    
    function dateInput(){
        let today = new Date().toISOString().split('T')[0]; 
        let $dateInput = $("#int_date");
        $dateInput.attr("min", today).val(today);
        $dateInput.on("click", function() {
            this.showPicker();
        });
    }
    
    // $(".assignHR").on('submit', function(e){
    //     e.preventDefault();
    //     let formData = $(this).serialize(); 
    //     $.ajax({
    //         url: "{{route('assigning',['token'=>$data->token_no])}}",  
    //         type: "GET",
    //         data: formData, 
    //         success: function (response) {
    //           if(response.success){
    //               toastr.success(response.msg); 
    //               $("#assignHR").modal('hide');
    //               $(".assignHR").trigger("reset");
    //               dateInput();
    //           }else{
    //               toastr.error(response.msg); 
    //           }
    //         },
    //         error: function (xhr) {
    //             console.log(xhr.responseText);
    //         }
    //     });
    // })
</script>
@endsection
