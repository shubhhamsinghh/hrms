from django.contrib import admin
from .models import UserProfile, Department, JobRole, Candidate, Education, Experience, Assign, Remark, ManagerRating, Bgv, Bgv_documents

class UserProfileAdmin(admin.ModelAdmin):
    list_display = ('user', 'designation')

admin.site.register(UserProfile, UserProfileAdmin)

@admin.register(JobRole)
class JobRoleAdmin(admin.ModelAdmin):
    list_display = ['id', 'name']

@admin.register(Department)
class DepartmenteAdmin(admin.ModelAdmin):
    list_display = ['dept_name']

@admin.register(Candidate)
class CandidateAdmin(admin.ModelAdmin):
    list_display = ['name','dept_id','apply_for','referred','email']

@admin.register(Education)
class EducationAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','high_school']

    def candidate_name(self, obj):
        return obj.candidate_id.name
    

@admin.register(Experience)
class ExperienceAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','exp_level']

    def candidate_name(self, obj):
        return obj.candidate_id.name
    

@admin.register(Assign)
class AssignAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','from_name','to_name']

    def candidate_name(self, obj):
        return obj.candidate.name
    
    def from_name(self, obj):
        return obj.assign_from
    
    def to_name(self, obj):
        return obj.assign_to
    
@admin.register(Remark)
class RemarkAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','re_status','remark']

    def candidate_name(self, obj):
        return obj.candidate.name
    
@admin.register(ManagerRating)
class RatingAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','remark','work_exe']

    def candidate_name(self, obj):
        return obj.remark.candidate.name
    
@admin.register(Bgv)
class BgvAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','company_name']

    def candidate_name(self, obj):
        return obj.candidate.name
    
@admin.register(Bgv_documents)
class bgvdocAdmin(admin.ModelAdmin):
    list_display = ['candidate_name','bgv']

    def candidate_name(self, obj):
        return obj.candidate.name
    
