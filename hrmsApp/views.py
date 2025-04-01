from django.shortcuts import render, redirect, get_object_or_404
from django.contrib.auth.forms import AuthenticationForm
from django.contrib.auth import login, logout
from .middlewares import auth, guest
from django.contrib.auth.models import User
from .models import Department, Candidate, Education, Experience, JobRole
from datetime import datetime
from django.contrib import messages
import secrets

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
    return render(request, 'pages/home.html')

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
       return redirect('candidate_listing')
    
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
        return redirect('candidate_listing')


def candidate_listing(request):
    data = Candidate.objects.all()
    return render(request, 'pages/candidate-list.html',{'data':data})

def logout_view(request):
    logout(request)
    return redirect('login')