@extends('layouts.web_layout')
@section('title') Background Verification @endsection
@section('context')
<style>
   .bg-verification-info p {
            font-size: 14px;
            color: #707070;
        }
    .form-box span{
        display: inline-block;
        font-size: 14px;
        color: #707070;
    }
</style>
<section class="Interview_SecA">
        <div class="container">
            <div class="bg-verification-info">
                <h4 class="">BACKGROUND VERIFICATION</h4>
                <p>Dear {{$candidate->name}}</p>
                <p>As part of our Background Verification (BGV) process, please provide details of your last two employers along with supporting documents to ensure smooth joining process. Kindly fill in the required details below:</p>
            </div>

            <!-- First Section -->
            <h4 class="mt-5">PREVIOUS EMPLOYMENT DETAILS</h4>
            <form action="{{route('background_verifi_submit',['token' => $candidate->token_no ])}}" method="post" enctype="multipart/form-data">
                @csrf
            <div class="form-box">
                <h5>Company 1 (Most Recent Employer)</h5>
                <p class="mb-2 text-muted">Provide the information below</p>
                <div class="row g-2 row-define">
                    <div class="col-md-6">
                        <label class="form-label">Current Company Name</label>
                        <input type="text" class="form-control" name="company1" required minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location1" required minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Title/profile/position/Department</label>
                        <input type="text" class="form-control" name="profile1" required minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-control" name="emp_id1" required minlength="3" maxlength="25">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">HR Name</label>
                        <input type="text" class="form-control" name="hr_name1" minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="hr_phone1" minlength="8" maxlength="15" onkeypress="return isNumberKey(event)">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Mail ID</label>
                        <input type="email" class="form-control" name="hr_mail1" minlength="8" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Reporting Manager Name</label>
                        <input type="text" class="form-control" name="manager_name1" minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="manager_phone1" minlength="8" maxlength="15" onkeypress="return isNumberKey(event)">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Mail ID</label>
                        <input type="email" class="form-control" name="manager_mail1" minlength="8" maxlength="40">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason For Leaving</label>
                        <textarea class="form-control" rows="3" name="leaving_reason1"></textarea>
                    </div>
                    <h5 class="mt-4">Supporting Documents:</h5>
                    <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Offer Letter</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="offer_latter1" id="offer_letter1">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="attach_file">
                            <label class="form-label">Relieving Letter</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="relieving_letter1" id="relieving_letter1">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Experience Certificate</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="exp_certificate1" id="experience_letter1">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Last Three Months’ Salary Slips/Bank Statement</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="salary_slip[]" id="salary_slip1" multiple>
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form-box">
                <h5>Company 2 (Second Last Employer)</h5>
                <p class="mb-2 text-muted">Provide the information below</p>
                <div class="row g-2 row-define">
                    <div class="col-md-6">
                        <label class="form-label">Company Name</label>
                        <input type="text" class="form-control" name="company2" minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Location</label>
                        <input type="text" class="form-control" name="location2" minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Job Title/profile/position/Department</label>
                        <input type="text" class="form-control" name="profile2" minlength="3" maxlength="40">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Employee ID</label>
                        <input type="text" class="form-control" name="emp_id2" minlength="3" maxlength="25">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">HR Name</label>
                        <input type="text" class="form-control" name="hr_name2" minlength="8" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="hr_phone2" minlength="8" maxlength="15" onkeypress="return isNumberKey(event)">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Mail ID</label>
                        <input type="email" class="form-control" name="hr_mail2" minlength="8" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Reporting Manager Name</label>
                        <input type="text" class="form-control" name="manager_name2" minlength="8" maxlength="40">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Contact Number</label>
                        <input type="text" class="form-control" name="manager_phone2" minlength="8" maxlength="15" onkeypress="return isNumberKey(event)">
                    </div>
                    <div class="col-md-4 col-12">
                        <label class="form-label">Mail ID</label>
                        <input type="text" class="form-control" name="manager_mail2" minlength="8" maxlength="40">
                    </div>
                    <div class="col-12">
                        <label class="form-label">Reason For Leaving</label>
                        <textarea class="form-control" rows="3" name="leaving_reason2"></textarea>
                    </div>
                    <h5 class="mt-4">Supporting Documents:</h5>
                     <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Offer Letter</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="offer_latter2" id="offer_letter2">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                         <div class="attach_file">
                            <label class="form-label">Relieving Letter</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="relieving_letter2" id="relieving_letter2">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Experience Certificate</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="exp_certificate2" id="experience_letter2">
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="attach_file">
                            <label class="form-label">Last Three Months’ Salary Slips/Bank Statement</label>
                            <div class="file-box-fix">
                                <input type="file" class="file_upload h-65" name="salary_slip2[]" id="salary_slip2" multiple>
                                <p id="fileName h-65">📂 Drop files here</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="col-12 text-center">
                        <button class="submit-btn mt-3 w-auto">SUBMIT</button>
                    </div>
                </div>
            </div>
            </form>
        </div>
        </section>
<div class="footer">
     &copy;  {{date('Y')}} <a href="https://www.advologysolution.com/">Advology Solution</a>. All rights reserved.
</div>

@endsection
