from django.urls import path
from . import views
from django.conf.urls.static import static
from django.conf import settings

urlpatterns = [
    path('',views.index_view,name="index_view"),
    path('verify-token',views.verify_token_view,name="verify_token_view"),
    path('interview',views.interview_view,name="interview_view"),
    path('interview-details',views.interview_details_view,name="interview_details_view"),
    path('bgv-verification/<str:token>',views.bgv_view,name="bgv_view"),
    path('bgv_verify/<str:token>',views.bgv_verify_view,name="bgv_verify"),
    path('login/',views.login_view,name="login"),
    path('dashboard/',views.dashboard_view,name="dashboard"),
    path('candidate-form/',views.candidate_form_view,name="candidate_form"),
    path('candidate-add/',views.candidate_add_view,name="candidate_add"),
    path('candidate-edit/<str:token>/',views.candidate_edit_view,name="candidate_edit"),
    path('candidate-update/<str:token>/',views.candidate_update_view,name="candidate_update"),
    path('candidates-listing/',views.candidate_listing,name="candidates_listing"),
    path('candidate-info/<str:token>',views.candidate_info_view,name="candidate_info"),
    path('assign-to/<str:token>',views.assign_to_view,name="assign_to"),
    path('add-remark/<str:token>',views.add_remark_view,name="add_remark"),
    path('final-remark/<str:token>',views.final_remark_view,name="final_remark"),
    path('bgv-remark/<str:token>',views.bgv_remark_view,name="bgv_remark"),
    path('doc-remark/<str:token>',views.doc_remark_view,name="doc_remark"),
    path('assigned-candidates/',views.assigned_candidate_listing,name="assigned_candidates"),
    path('closed-candidates/',views.closed_candidate_listing,name="closed_candidates"),
    path('send-email/', views.send_test_email, name='send_test_email'),
    path('logout/',views.logout_view,name="logout"),
]+ static(settings.MEDIA_URL, document_root=settings.MEDIA_ROOT)
