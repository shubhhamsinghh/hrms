from django.shortcuts import redirect
from django.conf import settings
import re

def auth(view_function):
    def auth_view(request, *args, **kwargs):
        if request.user.is_authenticated == False:
            return redirect('login')
        return view_function(request, *args, **kwargs)
    return auth_view

def guest(view_function):
    def guest_view(request, *args, **kwargs):
        if request.user.is_authenticated:
            return redirect('dashboard')
        return view_function(request, *args, **kwargs)
    return guest_view

            
class AuthenticationMiddleware:
    def __init__(self, get_response):
        self.get_response = get_response

    def __call__(self, request):
        PUBLIC_PATHS = [ '/bgv-verification/','/bgv_verify/', '/login/']
        ADMIN_PATH = '/admin' 

        # if (
        #     not request.user.is_authenticated 
        #     and not request.path.startswith(ADMIN_PATH) 
        #     and request.path not in PUBLIC_PATHS
        # ):
        #     return redirect(settings.LOGIN_URL)

        
        for path in PUBLIC_PATHS:
            if re.match(f"^{path}\S*$", request.path):  # \S* matches the token part
                break
        else:
            if not request.user.is_authenticated and not request.path.startswith(ADMIN_PATH):
                return redirect(settings.LOGIN_URL)

        return self.get_response(request)
