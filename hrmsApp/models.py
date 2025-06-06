from django.contrib.auth.models import User
from django.db import models
from datetime import date
import datetime
import os


class UserProfile(models.Model):
    user = models.OneToOneField(User, on_delete=models.CASCADE)
    designation = models.CharField(max_length=100, blank=True, null=True)

    def __str__(self):
        return f"{self.user.username}'s Profile"

class Department(models.Model):
    dept_name= models.CharField(max_length=70)

    def __str__(self):
        return self.dept_name
     
class JobRole(models.Model):
    name = models.CharField(max_length=100)

    def __str__(self):
        return self.name

class Candidate(models.Model):
    token=models.CharField(max_length=15)
    dept_id=models.ForeignKey(Department,on_delete=models.CASCADE,related_name='candidate')
    apply_for=models.ForeignKey(JobRole, on_delete=models.CASCADE, related_name='candidates')
    int_date=models.DateField(default=date.today)
    referred=models.CharField(max_length=50)
    emp_name=models.CharField(max_length=70,null=True,blank=True)
    emp_id=models.CharField(max_length=30,null=True,blank=True)
    source=models.CharField(max_length=30,null=True,blank=True)
    location=models.CharField(max_length=50)
    shift=models.CharField(max_length=30)
    r_shift=models.CharField(max_length=30)
    name=models.CharField(max_length=70)
    father_name=models.CharField(max_length=70,null=True,blank=True)
    mobile=models.CharField(max_length=30,null=True,blank=True)
    email=models.EmailField(unique=True,null=True,blank=True)
    dob=models.DateField(null=True,blank=True)
    gender=models.CharField(max_length=30,null=True,blank=True)
    address=models.CharField(max_length=100,null=True,blank=True)
    designation=models.CharField(max_length=100,null=True,blank=True)
    salary=models.CharField(max_length=100,null=True,blank=True)
    doj=models.DateField(null=True,blank=True)
    reporting_time=models.CharField(max_length=100,null=True,blank=True)
    bgv_link=models.IntegerField(default=0)
    bgv_sent_on=models.DateField(null=True,blank=True)
    bgv_received_on=models.DateField(null=True,blank=True)
    doc_link=models.IntegerField(default=0)
    cand_status=models.IntegerField(default=0,help_text="0=>In Process | 1=>Rejected | 2=>On board")
    status=models.IntegerField(default=1)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return f"{str(self.id)} {self.name}"
    
class Education(models.Model):
    candidate_id=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='ed_candidate')
    high_school=models.CharField(max_length=30,null=True,blank=True)
    intermediate=models.CharField(max_length=30,null=True,blank=True)
    diploma=models.CharField(max_length=30,null=True,blank=True)
    graduation=models.CharField(max_length=30,null=True,blank=True)
    pg=models.CharField(max_length=30,null=True,blank=True)
    master=models.CharField(max_length=30,null=True,blank=True)

    def __str__(self):
        return f"{self.candidate_id} , {self.high_school}"

def upload_resume_path(instance, filename):
    base_name, ext = os.path.splitext(filename)
    timestamp = datetime.datetime.now().strftime("%Y%m%d%H%M%S%f") 
    new_filename = f"{base_name}_{timestamp}{ext}"
    return os.path.join('resumes/', new_filename)


def upload_portfolio_path(instance, filename):
    base_name, ext = os.path.splitext(filename)
    timestamp = datetime.datetime.now().strftime("%Y%m%d%H%M%S%f")
    new_filename = f"{base_name}_{timestamp}{ext}"
    return os.path.join('portfolios/', new_filename)

def upload_dgv_doc_path(instance, filename):
    base_name, ext = os.path.splitext(filename)
    timestamp = datetime.datetime.now().strftime("%Y%m%d%H%M%S%f")
    new_filename = f"{base_name}_{timestamp}{ext}"
    return os.path.join('bgv-documents/', new_filename)

class Experience(models.Model):
    candidate_id=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='exp_candidate')
    exp_level=models.CharField(max_length=30)
    exp_year=models.CharField(max_length=30,null=True,blank=True)
    exp_month=models.CharField(max_length=30,null=True,blank=True)
    curr_month_salary=models.CharField(max_length=30,null=True,blank=True)
    curr_year_salary=models.CharField(max_length=30,null=True,blank=True)
    expec_month_salary=models.CharField(max_length=30,null=True,blank=True)
    notice_period=models.CharField(max_length=30,null=True,blank=True)
    resume = models.FileField(upload_to=upload_resume_path, null=True, blank=True)
    portfolio = models.FileField(upload_to=upload_portfolio_path, null=True, blank=True)
    links=models.CharField(max_length=255,null=True,blank=True)

    def __str__(self):
        return f"{self.candidate_id.name}"
    
class Assign(models.Model):
    candidate=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='candidate_d')
    assign_from=models.ForeignKey(User,on_delete=models.CASCADE,related_name="assigned_from")
    assign_to=models.ForeignKey(User,on_delete=models.CASCADE,null=True,blank=True,related_name="assigned_to")
    int_mode=models.CharField(max_length=30)
    int_date=models.DateField(null=True,blank=True)
    int_time=models.CharField(max_length=30,null=True,blank=True)
    int_round=models.IntegerField(default=0,help_text="0=>Assign to HR | 1=>HR Remarked | 2=>Assign to TL | 3=> TL Remarked | 4=>Assign to Manager | 5=>Manager Remarked | 6=>Final Remarked | 7=>BGV Remarked")

    def __str__(self):
        return f"{self.candidate.name}" 
    
class Remark(models.Model):
    candidate=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='remark_to_cand')
    assigned_id=models.ForeignKey(Assign,on_delete=models.CASCADE,related_name='assign_candidate')
    rating=models.CharField(max_length=30)
    re_status=models.CharField(max_length=70)
    remark=models.TextField(null=True)

    def __str__(self):
        return f"{self.candidate}"
    

class ManagerRating(models.Model):
    remark=models.ForeignKey(Remark,on_delete=models.CASCADE,related_name="remark_info")
    work_exe=models.IntegerField()
    applicable_skl=models.IntegerField()
    appearance=models.IntegerField()
    attiude=models.IntegerField()
    education=models.IntegerField()
    enthusiasm=models.IntegerField()

    def __str__(self):
        return f"{self.remark}"
    

class Bgv(models.Model):
    candidate=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='bgv_candidate')
    company_name=models.CharField(max_length=100, null=True, blank=True)
    location=models.CharField(max_length=100, null=True, blank=True)
    profile=models.CharField(max_length=100, null=True, blank=True)
    emp_id=models.CharField(max_length=100, null=True, blank=True)
    hr_name=models.CharField(max_length=100, null=True, blank=True)
    hr_phone=models.CharField(max_length=100, null=True, blank=True)
    hr_mail=models.EmailField(null=True, blank=True)
    manager_name=models.CharField(max_length=100, null=True, blank=True)
    manager_phone=models.CharField(max_length=100, null=True, blank=True)
    manager_mail=models.EmailField(null=True, blank=True)
    leaving_reason=models.CharField(max_length=100, null=True, blank=True)

    def __str__(self):
        return f"{self.candidate} {self.company_name}"
    
class Bgv_documents(models.Model):
    candidate=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='bgv_doc_candidate')
    bgv=models.ForeignKey(Bgv,on_delete=models.CASCADE,related_name='bgv_doc')
    offer_latter=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)
    relieving_latter=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)
    exp_certificate=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)
    salary_slip0=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)
    salary_slip1=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)
    salary_slip2=models.FileField(upload_to=upload_dgv_doc_path, null=True, blank=True)

    def __str__(self):
        return f"{self.candidate} {self.bgv}"
    


