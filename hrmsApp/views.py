from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.forms import AuthenticationForm
from django.contrib.auth import login, logout
from .middlewares import auth, guest
from django.contrib.auth.models import User
from .models import Department, Candidate, Education, Experience, JobRole, Assign, Remark, ManagerRating, Bgv, Bgv_documents
from datetime import datetime
from django.contrib import messages
import secrets
from django.core.mail import send_mail
from django.http import HttpResponse
from django.urls import reverse


def bgv_view(request, token):
        candidate = Candidate.objects.get(token=token)
        if candidate.bgv_link == 1 :
            if candidate:
                return render(request, 'bgv.html',{'candidate':candidate})
            else:
                return HttpResponse('404!, candidate not found!')
        else:
            return HttpResponse('404!, url not found!') 

 
def bgv_verify_view(request, token):
    if request.POST:
         candidate = Candidate.objects.get(token=token)
         if candidate.bgv_link == 1 :
            record1 = {}
            record1['candidate'] = candidate
            record1['company_name'] = request.POST.get('company1')
            record1['location'] = request.POST.get('location1')
            record1['profile'] = request.POST.get('profile1')
            record1['emp_id'] = request.POST.get('emp_id1')
            record1['hr_name'] = request.POST.get('hr_name1')
            record1['hr_phone'] = request.POST.get('hr_phone1')
            record1['hr_mail'] = request.POST.get('hr_mail1')
            record1['manager_name'] = request.POST.get('manager_name1')
            record1['manager_phone'] = request.POST.get('manager_phone1')
            record1['manager_mail'] = request.POST.get('manager_mail1')
            record1['leaving_reason'] = request.POST.get('leaving_reason1')
            b_data = Bgv.objects.create(**record1)
            
            bgv_doc = Bgv_documents()
            bgbveri = {}
            bgbveri['bgv'] = b_data
            bgbveri['candidate'] = candidate
            bgbveri['offer_latter'] = request.FILES.get('offer_latter1')
            bgbveri['relieving_latter'] = request.FILES.get('relieving_letter1')
            bgbveri['exp_certificate'] = request.FILES.get('exp_certificate1')
            
            salary_slips = request.FILES.getlist('salary_slip[]')
            if salary_slips:
                for i, file in enumerate(salary_slips):
                    if i == 0:
                        bgbveri['salary_slip0'] = file
                    elif i == 1:
                        bgbveri['salary_slip1'] = file
                    elif i == 2:
                        bgbveri['salary_slip2'] = file
                    else:
                        break 

            for key, file in bgbveri.items():
                if file: 
                    setattr(bgv_doc, key, file)

            bgv_doc.save()

            record2 = {}
            record2['candidate'] = candidate
            record2['company_name'] = request.POST.get('company2')
            record2['location'] = request.POST.get('location2')
            record2['profile'] = request.POST.get('profile2')
            record2['emp_id'] = request.POST.get('emp_id2')
            record2['hr_name'] = request.POST.get('hr_name2')
            record2['hr_phone'] = request.POST.get('hr_phone2')
            record2['hr_mail'] = request.POST.get('hr_mail2')
            record2['manager_name'] = request.POST.get('manager_name2')
            record2['manager_phone'] = request.POST.get('manager_phone2')
            record2['manager_mail'] = request.POST.get('manager_mail2')
            record2['leaving_reason'] = request.POST.get('leaving_reason2')
            b_data2 = Bgv.objects.create(**record2)
            
            bgv_doc2 = Bgv_documents()
            bgbveri2 = {}
            bgbveri2['bgv'] = b_data2
            bgbveri2['candidate'] = candidate
            bgbveri2['offer_latter'] = request.FILES.get('offer_latter2')
            bgbveri2['relieving_latter'] = request.FILES.get('relieving_letter2')
            bgbveri2['exp_certificate'] = request.FILES.get('exp_certificate2')
            
            salary_slips2 = request.FILES.getlist('salary_slip2[]')
            if salary_slips2:
                for i, file in enumerate(salary_slips2):
                    if i == 0:
                        bgbveri2['salary_slip0'] = file
                    elif i == 1:
                        bgbveri2['salary_slip1'] = file
                    elif i == 2:
                        bgbveri2['salary_slip2'] = file
                    else:
                        break 

            for key, file in bgbveri2.items():
                if file: 
                    setattr(bgv_doc2, key, file)

            bgv_doc2.save()
            today_date = datetime.now().date() 
            formatted_date = today_date.strftime('%d-%m-%Y')
            candidate.bgv_link = 2
            candidate.bgv_received_on = datetime.now().date()
            candidate.save()
         
         return HttpResponse('Bvg upload Successfully!') 
    else:
        url = reverse('bgv_view', args=[token])
        return redirect(url)
        

def home_view(request):
    return redirect('login')

@guest
def login_view(request):
    if request.method == 'POST':
        form  = AuthenticationForm(request, data=request.POST)
        if form.is_valid():
            user = form.get_user()
            login(request, user)
            return redirect('dashboard')
    else:
        initial_data = {'username':'', 'password': ''}
        form = AuthenticationForm(initial=initial_data)
         
    return render(request, 'pages/auth/login.html',{'form':form})

@auth
def dashboard_view(request):
    if request.user.userprofile.designation == "admin" or request.user.userprofile.designation == "hr" :
        a1 = Candidate.objects.count()
    else:
        a1 = 0

    assigned_data = Assign.objects.filter(assign_to=request.user.id)
    a2 = Candidate.objects.filter(
    id__in=assigned_data.values_list('candidate_id', flat=True),
    status=1
    ).count()
    return render(request, 'pages/home.html',{'a1':a1, 'a2':a2})

@auth
def candidate_form_view(request):
    departments = Department.objects.all()
    jobrole = JobRole.objects.all()
    return render(request,'pages/candidate-form.html',{'departments':departments, 'jobrole': jobrole})

@auth
def candidate_add_view(request):
    if request.POST:
       candidate = {
           'token' : secrets.token_hex(4).upper(),
           'dept_id' : request.POST.get('department_id'),
           'apply_for': request.POST.get('apply_for'),
           'int_date':request.POST.get('int_date'),
           'referred':request.POST.get('referral_by'), 
           'location':request.POST.get('location'),
           'shift':request.POST.get('shift'),
           'r_shift':request.POST.get('r_shift'),
           'name':request.POST.get('name'),
           'father_name':request.POST.get('father_name'),
           'mobile':request.POST.get('phone_no'),
           'email':request.POST.get('email'),
           'dob':request.POST.get('dob'),
           'address':request.POST.get('address'), 
       }

       candidate['dept_id'] = Department.objects.get(id=candidate['dept_id'])
       candidate['apply_for'] = JobRole.objects.get(id=candidate['apply_for'])

       candidate['int_date'] = (
            datetime.strptime(candidate['int_date'], "%Y-%m-%d").date() if candidate['int_date'] else None
        )
       candidate['dob'] = (
            datetime.strptime(candidate['dob'], "%Y-%m-%d").date() if candidate['dob'] else None
        )

       if request.POST.get('referral_by') == "Employee":
           candidate['emp_name'] = request.POST.get('ref_emp_name')
           candidate['emp_id'] = request.POST.get('ref_emp_id')
       else:
           candidate['source'] = request.POST.get('source')
       
       if request.POST.get('oth_gender') is not None:
           candidate['gender'] = request.POST.get('oth_gender')
       else:
           candidate['gender'] = request.POST.get('gender')
       
       c_data = Candidate.objects.create(**candidate)
   
       education = {}
       education['candidate_id'] = c_data
       education['high_school'] = request.POST.get('high_school') if request.POST.get('high_school') else None
       education['intermediate'] = request.POST.get('intermediate') if request.POST.get('intermediate') else None
       education['diploma'] = request.POST.get('diploma') if request.POST.get('diploma') else None
       education['graduation'] = request.POST.get('graduation') if request.POST.get('graduation') else None
       education['pg'] = request.POST.get('pg') if request.POST.get('pg') else None
       education['master'] = request.POST.get('master') if request.POST.get('master') else None
       Education.objects.create(**education)

       exp = {}
       exp['candidate_id'] = c_data
       exp['exp_level'] = request.POST.get('exp_level') if request.POST.get('exp_level') else None
       exp['exp_year'] = request.POST.get('exp_year') if request.POST.get('exp_year') else None
       exp['exp_month'] = request.POST.get('exp_month') if request.POST.get('exp_month') else None
       exp['curr_month_salary'] = request.POST.get('curr_month_salary') if request.POST.get('curr_month_salary') else None
       exp['curr_year_salary'] = request.POST.get('curr_year_salary') if request.POST.get('curr_year_salary') else None
       exp['expec_month_salary'] = request.POST.get('expec_month_salary') if request.POST.get('expec_month_salary') else None
       exp['notice_period'] = request.POST.get('notice_period') if request.POST.get('notice_period') else None
       exp['links'] = request.POST.get('links') if request.POST.get('links') else None
       exp['resume'] = request.FILES.get('resume')
       exp['portfolio'] = request.FILES.get('portfolio')
       Experience.objects.create(**exp)

       messages.success(request, "Candidate created successfully!")
       return redirect('candidates_listing')
    
@auth
def candidate_edit_view(request, token):

    departments = Department.objects.all()
    jobrole = JobRole.objects.all()
    candidate = get_object_or_404(
        Candidate.objects.prefetch_related('ed_candidate', 'exp_candidate'),
        token=token
    )
    education = candidate.ed_candidate.first() 
    experience = candidate.exp_candidate.first()
    return render(request, 'pages/candidate-edit.html',{
        'departments':departments,
        'jobrole':jobrole,
        'candidate': candidate,
        'education': education,
        'experience': experience
    })

@auth
def candidate_update_view(request,token):
    if request.POST:
        candidate = Candidate.objects.get(token=token)

        candidate.dept_id = Department.objects.get(id=request.POST.get('department_id'))
        candidate.apply_for = JobRole.objects.get(id=request.POST.get('apply_for'))
        candidate.int_date = datetime.strptime(request.POST.get('int_date'), "%Y-%m-%d").date() if request.POST.get('int_date') else None
        candidate.referred = request.POST.get('referral_by')
        candidate.location = request.POST.get('location')
        candidate.shift = request.POST.get('shift')
        candidate.r_shift = request.POST.get('r_shift')
        candidate.name = request.POST.get('name')
        candidate.father_name = request.POST.get('father_name')
        candidate.mobile = request.POST.get('phone_no')
        candidate.email = request.POST.get('email')
        candidate.dob = datetime.strptime(request.POST.get('dob'), "%Y-%m-%d").date() if request.POST.get('dob') else None
        candidate.address = request.POST.get('address')

        if request.POST.get('referral_by') == "Employee":
            candidate.emp_name = request.POST.get('ref_emp_name')
            candidate.emp_id = request.POST.get('ref_emp_id')
        else:
            candidate.source = request.POST.get('source')

        if request.POST.get('oth_gender'):
            candidate.gender = request.POST.get('oth_gender')
        else:
            candidate.gender = request.POST.get('gender')
        candidate.save()

        education = Education.objects.get(candidate_id=candidate)
        education.high_school = request.POST.get('high_school') if request.POST.get('high_school') else None
        education.intermediate = request.POST.get('intermediate') if request.POST.get('intermediate') else None
        education.diploma = request.POST.get('diploma') if request.POST.get('diploma') else None
        education.graduation = request.POST.get('graduation') if request.POST.get('graduation') else None
        education.pg = request.POST.get('pg') if request.POST.get('pg') else None
        education.master = request.POST.get('master') if request.POST.get('master') else None
        education.save()

        experience = Experience.objects.get(candidate_id=candidate)
        experience.exp_level = request.POST.get('exp_level') if request.POST.get('exp_level') else None
        experience.exp_year = request.POST.get('exp_year') if request.POST.get('exp_year') else None
        experience.exp_month = request.POST.get('exp_month') if request.POST.get('exp_month') else None
        experience.curr_month_salary = request.POST.get('curr_month_salary') if request.POST.get('curr_month_salary') else None
        experience.curr_year_salary = request.POST.get('curr_year_salary') if request.POST.get('curr_year_salary') else None
        experience.expec_month_salary = request.POST.get('expec_month_salary') if request.POST.get('expec_month_salary') else None
        experience.notice_period = request.POST.get('notice_period') if request.POST.get('notice_period') else None
        experience.links = request.POST.get('links') if request.POST.get('links') else None

        if experience.resume:
            experience.resume.delete(save=False)

        if request.FILES.get('resume'):
            experience.resume = request.FILES.get('resume')

        if experience.portfolio:
            experience.portfolio.delete(save=False)

        if request.FILES.get('portfolio'):
            experience.portfolio = request.FILES.get('portfolio')

        experience.save()
        messages.success(request, "Candidate updates successfully!")
        return redirect('candidates_listing')


def candidate_listing(request):
    data = Candidate.objects.all().order_by('-id')
    return render(request, 'pages/candidate-list.html',{'data':data})

def assigned_candidate_listing(request):
    assigned = User.objects.get(id=request.user.id)
    assigned_data = Assign.objects.filter(assign_to=assigned)
    data = Candidate.objects.filter(
    id__in=assigned_data.values_list('candidate_id', flat=True),
    status=1
    )
    assign_from_map = {
        assign.candidate_id: {
            'assign_from': assign.assign_from,
            'int_date': assign.int_date
        }
        for assign in assigned_data
    }

    candidate_info = []
    for candidate in data:
        assign_info = assign_from_map.get(candidate.id)
        candidate_info.append({
            'candidate': candidate,
            'assign_from': assign_info['assign_from'] if assign_info else None,
            'int_date': assign_info['int_date'] if assign_info else None
        })

    return render(request, 'pages/assigned-candidates.html',{'data':candidate_info})

def closed_candidate_listing(request):
    assigned = User.objects.get(id=request.user.id)
    assigned_data = Assign.objects.filter(assign_to=assigned)
    data = Candidate.objects.filter(
    id__in=assigned_data.values_list('candidate_id', flat=True),
    status=0
    )
    return render(request, 'pages/candidate-list.html',{'data':data})

def candidate_info_view(request, token):
     
    candidate = get_object_or_404(
        Candidate.objects.prefetch_related('ed_candidate', 'exp_candidate'),
        token=token
    )
    education = candidate.ed_candidate.get() 
    experience = candidate.exp_candidate.get()
    hr_users = User.objects.filter(userprofile__designation='hr',is_active=1)
    tls = User.objects.filter(userprofile__designation='tl / supervisor',is_active=1)
    managers = User.objects.filter(userprofile__designation='manager',is_active=1)
    currentAssign = Assign.objects.filter(candidate=candidate.id).order_by('-id').first()
    # assigning = Assign.objects.filter(candidate=candidate.id).order_by('-id')
    # assigning = assigning.prefetch_related('assign_candidate')
    assigning = Assign.objects.filter(candidate=candidate).order_by('-id') \
        .prefetch_related('assign_candidate__remark_info')
    
    bgv = Bgv.objects.filter(candidate=candidate)
    bgv = bgv.prefetch_related('bgv_doc')
    bgv_assign = Assign.objects.filter(candidate=candidate,int_round=7).first()
   
    if bgv_assign:
        bgv_remark = Remark.objects.get(assigned_id=bgv_assign.id)
    else:
        bgv_remark = None

    if currentAssign is None:
        int_r = None
    else:
        int_r = currentAssign.int_round 

    
    hr_re = Assign.objects.filter(candidate=candidate.id,int_round__in=[0, 1]).first()
    mn_re = Assign.objects.filter(candidate=candidate.id,int_round=4).first()
    tl_re = Assign.objects.filter(candidate=candidate.id,int_round=2).first()

    return render(request, 'pages/candidate-info.html',{
        'candidate': candidate,
        'education': education,
        'experience': experience,
        'hr_users': hr_users,
        'int_r': int_r,
        'assigning': assigning,
        'tls' : tls,
        'managers': managers,
        'hr_re': hr_re,
        'mn_re': mn_re,
        'tl_re': tl_re,
        'bgv': bgv,
        'bgv_remark': bgv_remark
    })

def assign_to_view(request, token):
    if request.POST:
        candidate = Candidate.objects.get(token=token)
        getMaxgetMaxassign = Assign.objects.filter(candidate=candidate).order_by('-int_round').first()
        assign = {}
        assign['candidate'] = candidate
        assign['assign_from'] = User.objects.get(id=request.user.id)
        assign['assign_to'] = User.objects.get(id=request.POST.get('assign_to'))
        assign['int_mode'] = request.POST.get('int_mode')
        assign['int_date'] = request.POST.get('int_date')
        assign['int_time'] = request.POST.get('int_time')
        if request.POST.get('key') is not None:
            if request.POST.get('key') == "manager":
                assign['int_round'] = 4
        else:        
            if getMaxgetMaxassign is not None:
                assign['int_round'] = getMaxgetMaxassign.int_round + 1
        Assign.objects.create(**assign)
        messages.success(request, "Candidate Assigned successfully!")
        return redirect(request.META.get('HTTP_REFERER', 'dashboard'))
    

def add_remark_view(request, token):
    if request.POST:
        candidate = Candidate.objects.get(token=token)
        assign = Assign.objects.get(id=request.POST.get('assigned_id'))
        remark = {}
        remark['candidate'] = candidate
        remark['assigned_id'] = assign
        remark['rating'] = request.POST.get('rating')
        remark['re_status'] = request.POST.get('re_status')
        remark['remark'] = request.POST.get('remark')
        rSave = Remark.objects.create(**remark)
        if rSave:
            if request.POST.get('key') == 'hr':
                s = 1
            elif request.POST.get('key') == 'tl':
                s = 3
            elif request.POST.get('key') == 'manager':
                s = 5
                manager = {}
                manager['remark'] = rSave
                manager['work_exe'] = request.POST.get('work_exe')
                manager['applicable_skl'] = request.POST.get('applicable_skl')
                manager['appearance'] = request.POST.get('appearance')
                manager['attiude'] = request.POST.get('attiude')
                manager['education'] = request.POST.get('education')
                manager['enthusiasm'] = request.POST.get('enthusiasm')
                ManagerRating.objects.create(**manager)
            elif request.POST.get('key') == 'final hr':
                s = 6
            assign = Assign.objects.get(id=assign.id)
            assign.int_round = s
            assign.save()

    messages.success(request, "Remark add successfully!")
    return redirect(request.META.get('HTTP_REFERER', 'dashboard'))

def final_remark_view(request, token):
    print(request.POST)
    candidate = Candidate.objects.get(token=token)
    assign = {}
    assign['candidate'] = candidate
    assign['assign_from'] = User.objects.get(id=request.user.id)
    assign['assign_to'] = None
    assign['int_mode'] = 0
    assign['int_round'] = 6
    a_data = Assign.objects.create(**assign)
    if a_data:
        remark = {}
        remark['candidate'] = candidate
        remark['assigned_id'] = a_data
        remark['rating'] = 0
        remark['re_status'] = request.POST.get('interested')+ "/" +request.POST.get('fi_status')
        remark['remark'] = request.POST.get('remark')
        r_data = Remark.objects.create(**remark)

        if r_data:
            candidate.designation = request.POST.get('designation'),
            candidate.salary = request.POST.get('salary'),
            candidate.doj = request.POST.get('doj')
            candidate.reporting_time = request.POST.get('reporting_time')
    
            if request.POST.get('interested') == "Yes" and request.POST.get('fi_status') == "Selected" :
                checkExp = Experience.objects.get(candidate_id=candidate.id)   
                if checkExp.exp_level == "Experienced":
                    # Mail::send('mails.bg_verification',['name' => $canID->name,'token' => $token], function($message) use ($canID)
                    # {
                    #     $to_emails = $canID->email;
                    #     $message->to($to_emails)->subject('Background Verifivation Mail From HRMS');
                    # });
                    candidate.bgv_sent_on = datetime.now().strftime('%Y-%m-%d')
                    candidate.bgv_link = 1
                else:
                    # Mail::send('mails.upload_doc',['name' => $canID->name,'token' => $token], function($message) use ($canID)
                    # {
                    #     $to_emails = $canID->email;
                    #     $message->to($to_emails)->subject('Upload Document Mail From HRMS');
                    # });
                    candidate.doc_link = 1   
            else:
                candidate.status = 0

            if request.POST.get('fi_status') == "Rejected":
                candidate.cand_status = 1

            candidate.save()
    
    messages.success(request, "Remark add successfully!")
    return redirect(request.META.get('HTTP_REFERER', 'dashboard'))

def bgv_remark_view(request, token):
    if request.POST:
      candidate = Candidate.objects.get(token=token)
      assign = Assign()
      assign.assign_from = User.objects.get(id=request.user.id)
      assign.assign_to = None
      assign.candidate = candidate
      assign.int_mode = 0
      assign.int_round = 7
      assign.save()
      remark = Remark()
      remark.assigned_id = assign
      remark.candidate = candidate
      remark.rating = 0
      remark.re_status = request.POST.get('bgv_verify')
      remark.remark = request.POST.get('bgv_remark')
      remark.save()
      if request.POST.get('bgv_verify') == "Yes":
            # Mail::send('mails.upload_doc',['name' => $canID->name,'token' => $token], function($message) use ($canID)
            # {
            #     $to_emails = $canID->email;
            #     $message->to($to_emails)->subject('Upload Document Mail From HRMS');
            # });
            candidate.doc_link = 1
      else:
        candidate.status = 0
      candidate.doc_link=1
      candidate.save()
      messages.success(request, "Remark add successfully!")
      return redirect(request.META.get('HTTP_REFERER', 'dashboard'))
    
def doc_remark_view(request,token):
    
    candidate = Candidate.objects.get(token=token)
    assign = Assign.objects.get(candidate_id=candidate.id, int_round=6)
    remark = Remark.objects.get(assigned_id=assign.id)
    if remark:
        mrk = remark.re_status
        statusPart = mrk.split('/')
        if len(statusPart) == 2:
           statusPart[1] =  request.POST.get('doc_status')
           remark.re_status = '/'.join(statusPart)
        remark.remark = remark.remark+"/"+request.POST.get('doc_remark')
        remark.save()
    if request.POST.get('doc_status') == "Rejected":
        candidate.cand_status = 1
    else:
        candidate.cand_status = 2

    candidate.status = 0
    candidate.save()

    messages.success(request, "Application Update Successfully !!")
    return redirect(request.META.get('HTTP_REFERER', 'dashboard'))

def send_test_email(request):
    # Email content
    subject = 'Test Email from Django'
    message = 'This is a test email sent from Django using the console backend.'
    from_email = 'webmaster@localhost'  # Can be anything for testing
    recipient_list = ['shubhamsingh@unibiztec.com']  # This can be any email address

    # Send email (it will be printed to the console)
    send_mail(subject, message, from_email, recipient_list)

    # Return a response to the user
    return HttpResponse('Test email sent! Check the console.')


def logout_view(request):
    logout(request)
    return redirect('login')