from django.urls import path
from . import views
from django.conf.urls.static import static
from django.conf import settings

urlpatterns = [
    # path('',views.home_view,name="home"),
    path('login/',views.login_view,name="login"),
    path('dashboard/',views.dashboard_view,name="dashboard"),
    path('candidate-form/',views.candidate_form_view,name="candidate_form"),
    path('candidate-add/',views.candidate_add_view,name="candidate_add"),
    path('candidate-edit/<str:token>/',views.candidate_edit_view,name="candidate_edit"),
    path('candidate-update/<str:token>/',views.candidate_update_view,name="candidate_update"),
    path('candidate-listing/',views.candidate_listing,name="candidate_listing"),
    path('candidate-info/<str:token>',views.candidate_info_view,name="candidate_info"),
    path('assign-to/<str:token>',views.assign_to_view,name="assign_to"),
    path('add-remark/<str:token>',views.add_remark_view,name="add_remark"),
    path('logout/',views.logout_view,name="logout"),
]+ static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
