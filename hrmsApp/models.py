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
    dept_id= models.ForeignKey(Department,on_delete=models.CASCADE,related_name='candidate')
    apply_for= models.ForeignKey(JobRole, on_delete=models.CASCADE, related_name='candidates')
    int_date=models.DateField(default=date.today)
    referred=models.CharField(max_length=50)
    emp_name=models.CharField(max_length=70,null=True)
    emp_id=models.CharField(max_length=30,null=True)
    source=models.CharField(max_length=30,null=True)
    location=models.CharField(max_length=50)
    shift=models.CharField(max_length=30)
    r_shift=models.CharField(max_length=30)
    name=models.CharField(max_length=70)
    father_name=models.CharField(max_length=70,null=True)
    mobile=models.CharField(max_length=30,null=True)
    email=models.EmailField(unique=True,null=True)
    dob=models.DateField(null=True)
    gender=models.CharField(max_length=30,null=True)
    address=models.CharField(max_length=100,null=True)
    bgv_link=models.IntegerField(default=0)
    bgv_sent_on=models.DateField(null=True)
    bgv_received_on=models.DateField(null=True)
    doc_link=models.IntegerField(default=0)
    status=models.IntegerField(default=1)
    created_at = models.DateTimeField(auto_now_add=True)
    updated_at = models.DateTimeField(auto_now=True)

    def __str__(self):
        return str(self.id)
    
class Education(models.Model):
    candidate_id=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='ed_candidate')
    high_school=models.CharField(max_length=30,null=True)
    intermediate=models.CharField(max_length=30,null=True)
    diploma=models.CharField(max_length=30,null=True)
    graduation=models.CharField(max_length=30,null=True)
    pg=models.CharField(max_length=30,null=True)
    master=models.CharField(max_length=30,null=True)

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

class Experience(models.Model):
    candidate_id=models.ForeignKey(Candidate,on_delete=models.CASCADE,related_name='exp_candidate')
    exp_level=models.CharField(max_length=30)
    exp_year=models.CharField(max_length=30,null=True)
    exp_month=models.CharField(max_length=30,null=True)
    curr_month_salary=models.CharField(max_length=30,null=True)
    curr_year_salary=models.CharField(max_length=30,null=True)
    expec_month_salary=models.CharField(max_length=30,null=True)
    notice_period=models.CharField(max_length=30,null=True)
    resume = models.FileField(upload_to=upload_resume_path, null=True, blank=True)
    portfolio = models.FileField(upload_to=upload_portfolio_path, null=True, blank=True)
    links=models.CharField(max_length=255,null=True)

    def __str__(self):
        return f"{self.candidate_id}, {self.exp_level}"
    



