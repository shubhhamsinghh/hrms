from django.contrib import admin
from .models import UserProfile, Department, JobRole, Candidate, Education, Experience

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
