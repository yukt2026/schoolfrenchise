<?php

use App\Http\Controllers\Admin\AdminAdmissionController;
use App\Http\Controllers\Admin\AdminAuditLogController;
use App\Http\Controllers\Admin\AdminBatchController;
use App\Http\Controllers\Admin\AdminCertificateController;
use App\Http\Controllers\Admin\AdminCentreController;
use App\Http\Controllers\Admin\AdminCmsController;
use App\Http\Controllers\Admin\AdminCourseController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminDownloadController;
use App\Http\Controllers\Admin\AdminExamController;
use App\Http\Controllers\Admin\AdminExpenseController;
use App\Http\Controllers\Admin\AdminFeeController;
use App\Http\Controllers\Admin\AdminMaterialController;
use App\Http\Controllers\Admin\AdminNotificationController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminSettingController;
use App\Http\Controllers\Admin\AdminStudentController;
use App\Http\Controllers\Admin\AdminSupportController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminWalletController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\Centre\CentreBatchController;
use App\Http\Controllers\Centre\CentreDashboardController;
use App\Http\Controllers\Centre\CentreDownloadController;
use App\Http\Controllers\Centre\CentreExamController;
use App\Http\Controllers\Centre\CentreFeeController;
use App\Http\Controllers\Centre\CentreProfileController;
use App\Http\Controllers\Centre\CentreStudentController;
use App\Http\Controllers\Centre\CentreSupportController;
use App\Http\Controllers\Centre\CentreWalletController;
use App\Http\Controllers\CoursePublicController;
use App\Http\Controllers\EnquiryPublicController;
use App\Http\Controllers\FranchisePublicController;
use App\Http\Controllers\GeoController;
use App\Http\Controllers\PaymentPublicController;
use App\Http\Controllers\PublicController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\Student\StudentAcademicController;
use App\Http\Controllers\Student\StudentDashboardController;
use App\Http\Controllers\Student\StudentFeeController;
use App\Http\Controllers\Student\StudentNotificationController;
use App\Http\Controllers\Student\StudentPortalController;
use App\Http\Controllers\Student\StudentSupportController;
use App\Http\Controllers\VerificationController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes - TECH ERA COMPUTER
|--------------------------------------------------------------------------
| Public website, courses, verification engine, franchise portal,
| payments, and multi-guard authentication.
|
*/

// 1. Core Public Informational Pages
Route::get('/', [PublicController::class, 'home'])->name('home');
Route::get('/about-us', [PublicController::class, 'about'])->name('about');
Route::get('/company', [PublicController::class, 'company'])->name('company');
Route::get('/chairman', [PublicController::class, 'chairman'])->name('chairman');
Route::get('/mission', [PublicController::class, 'mission'])->name('mission');
Route::get('/corporate-members', [PublicController::class, 'members'])->name('members');
Route::get('/facilities', [PublicController::class, 'facilities'])->name('facilities');
Route::get('/gallery', [PublicController::class, 'gallery'])->name('gallery');
Route::get('/contact-us', [PublicController::class, 'contact'])->name('contact');
Route::post('/contact-us', [PublicController::class, 'submitContact'])->name('contact.submit');
Route::get('/privacy-policy', [PublicController::class, 'privacy'])->name('privacy');
Route::get('/terms-conditions', [PublicController::class, 'terms'])->name('terms');
Route::get('/refund-policy', [PublicController::class, 'refund'])->name('refund');

// 2. Enquiries, Admissions & Public Forms (Rate limited)
Route::middleware('throttle:15,1')->group(function () {
    Route::post('/demo-request', [EnquiryPublicController::class, 'storeDemo'])->name('demo.request');
    Route::post('/contact-us', [PublicController::class, 'submitContact'])->name('contact.submit');
    Route::post('/admission-enquiry', [EnquiryPublicController::class, 'storeAdmissionEnquiry'])->name('enquiry.admission.submit');
    Route::post('/admission-apply', [EnquiryPublicController::class, 'storeAdmissionApplication'])->name('admission.apply.submit');
    Route::post('/admission-status', [EnquiryPublicController::class, 'checkAdmissionStatus'])->name('admission.status.submit');
    Route::post('/frenchise/submit', [FranchisePublicController::class, 'store'])->name('franchise.apply.submit');
    Route::post('/franchise/submit', [FranchisePublicController::class, 'store']);
    Route::post('/frenchise-status', [FranchisePublicController::class, 'checkStatus'])->name('franchise.status.submit');
    Route::post('/franchise-status', [FranchisePublicController::class, 'checkStatus']);
});

Route::get('/admission-enquiry', [EnquiryPublicController::class, 'admissionEnquiryIndex'])->name('enquiry.admission');
Route::get('/admission-apply', [EnquiryPublicController::class, 'admissionApplyIndex'])->name('admission.apply');
Route::get('/admission-status', [EnquiryPublicController::class, 'admissionStatusIndex'])->name('admission.status');
Route::get('/admission/acknowledgement/{application_no}', [EnquiryPublicController::class, 'printAcknowledgement'])
    ->name('admission.acknowledgement')
    ->where('application_no', '.*');

// 3. Courses Directory & Slug Details
Route::get('/courses', [CoursePublicController::class, 'index'])->name('courses.index');
Route::get('/course/{slug}', [CoursePublicController::class, 'show'])->name('courses.show');

// 4. Verification Engine (Certificates, Marksheets, QR scan, and Admissions - Rate limited)
Route::middleware('throttle:30,1')->group(function () {
    Route::get('/check-certificate-marksheet', [VerificationController::class, 'certificateIndex'])->name('verify.certificate');
    Route::post('/check-certificate-marksheet', [VerificationController::class, 'verifyCertificate'])->name('verify.certificate.submit');
    Route::get('/verify/cert/{cert_no}', [VerificationController::class, 'directVerifyCert'])->where('cert_no', '.*')->name('verify.cert.direct');
    Route::get('/check-admission', [VerificationController::class, 'admissionIndex'])->name('verify.admission');
    Route::post('/check-admission', [VerificationController::class, 'verifyAdmission'])->name('verify.admission.submit');
});

// 5. Online Fee Payment Workflow & Gateway Verification
Route::get('/online-payment', [PaymentPublicController::class, 'index'])->name('payment.index');
Route::post('/online-payment/initiate', [PaymentPublicController::class, 'initiateOrder'])->middleware('throttle:15,1')->name('payment.initiate');
Route::post('/online-payment/process', [PaymentPublicController::class, 'process'])->middleware('throttle:15,1')->name('payment.process');
Route::post('/online-payment/verify', [PaymentPublicController::class, 'verifyPayment'])->middleware('throttle:15,1')->name('payment.verify');
Route::post('/api/payment/webhook', [PaymentPublicController::class, 'webhook'])->name('payment.webhook');
Route::get('/online-payment/receipt/{transaction_id}', [PaymentPublicController::class, 'receipt'])->name('payment.receipt');

// 6. Franchise Public Zone
Route::get('/frenchise', [FranchisePublicController::class, 'create'])->name('franchise.apply');
Route::get('/franchise', [FranchisePublicController::class, 'create']);

Route::get('/search_centre', [FranchisePublicController::class, 'searchIndex'])->name('franchise.search');
Route::get('/search-centre', [FranchisePublicController::class, 'searchIndex']);

Route::get('/frenchise-status', [FranchisePublicController::class, 'statusIndex'])->name('franchise.status');
Route::get('/franchise-status', [FranchisePublicController::class, 'statusIndex']);

// 7. Authentication & Rate-limited Access Routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:6,1')->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/student', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
Route::get('/student/login', [StudentAuthController::class, 'showLoginForm']);
Route::post('/student/login', [StudentAuthController::class, 'login'])->middleware('throttle:6,1')->name('student.login.submit');

// Password Reset Routes (Rate limited)
Route::get('/password/reset', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/password/email', [ForgotPasswordController::class, 'sendResetLinkEmail'])->middleware('throttle:6,1')->name('password.email');
Route::get('/password/reset/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/password/reset', [ResetPasswordController::class, 'reset'])->middleware('throttle:6,1')->name('password.update');

// 8. Protected Portals (Multi-Guard / Role-Gated)
Route::middleware(['role:super_admin,admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');

    // Module 2: Students
    Route::get('/students/export', [AdminStudentController::class, 'export'])->name('students.export');
    Route::resource('students', AdminStudentController::class);

    // Module 3: Admissions & Applications
    Route::get('/admissions', [AdminAdmissionController::class, 'index'])->name('admissions.index');
    Route::get('/admissions/create', [AdminAdmissionController::class, 'create'])->name('admissions.create');
    Route::post('/admissions', [AdminAdmissionController::class, 'store'])->name('admissions.store');
    Route::get('/admissions/applications/{application}', [AdminAdmissionController::class, 'showApplication'])->name('admissions.application.show');
    Route::post('/admissions/applications/{application}/approve', [AdminAdmissionController::class, 'approveApplication'])->name('admissions.application.approve');
    Route::post('/admissions/applications/{application}/reject', [AdminAdmissionController::class, 'rejectApplication'])->name('admissions.application.reject');
    Route::get('/admissions/{admission}/edit', [AdminAdmissionController::class, 'edit'])->name('admissions.edit');
    Route::put('/admissions/{admission}', [AdminAdmissionController::class, 'update'])->name('admissions.update');
    Route::get('/admissions/{admission}/print', [AdminAdmissionController::class, 'printAdmissionForm'])->name('admissions.print');
    Route::delete('/admissions/{admission}', [AdminAdmissionController::class, 'destroy'])->name('admissions.destroy');

    // Module 4: Courses & Curriculum
    Route::resource('courses', AdminCourseController::class);

    // Module 5: Batches & Attendance
    Route::get('/batches/attendance/report', [AdminBatchController::class, 'attendanceReport'])->name('batches.attendance.report');
    Route::get('/batches/{batch}/attendance', [AdminBatchController::class, 'attendanceSheet'])->name('batches.attendance.sheet');
    Route::post('/batches/{batch}/attendance', [AdminBatchController::class, 'markAttendance'])->name('batches.attendance.mark');
    Route::resource('batches', AdminBatchController::class);

    // Module 6: Examinations, Question Bank & Results
    Route::get('/exams/questions', [AdminExamController::class, 'questions'])->name('exams.questions');
    Route::get('/exams/questions/create', [AdminExamController::class, 'createQuestion'])->name('exams.questions.create');
    Route::post('/exams/questions', [AdminExamController::class, 'storeQuestion'])->name('exams.questions.store');
    Route::get('/exams/questions/{question}/edit', [AdminExamController::class, 'editQuestion'])->name('exams.questions.edit');
    Route::put('/exams/questions/{question}', [AdminExamController::class, 'updateQuestion'])->name('exams.questions.update');
    Route::delete('/exams/questions/{question}', [AdminExamController::class, 'destroyQuestion'])->name('exams.questions.destroy');

    Route::get('/exams/results', [AdminExamController::class, 'results'])->name('exams.results');
    Route::get('/exams/results/create', [AdminExamController::class, 'createResult'])->name('exams.results.create');
    Route::post('/exams/results', [AdminExamController::class, 'storeResult'])->name('exams.results.store');
    Route::get('/exams/results/{result}', [AdminExamController::class, 'showResult'])->name('exams.results.show');
    Route::post('/exams/results/{result}/status', [AdminExamController::class, 'updateResultStatus'])->name('exams.results.status');

    Route::get('/exams/course-subjects/{course}', [AdminExamController::class, 'getCourseSubjects'])->name('exams.course.subjects');
    Route::resource('exams', AdminExamController::class);

    // Module 7: Study Materials
    Route::post('/materials/{material}/toggle-status', [AdminMaterialController::class, 'toggleStatus'])->name('materials.toggle-status');
    Route::resource('materials', AdminMaterialController::class);

    // Module 8: Certificates & Marksheets
    Route::get('/certificates/{certificate}/print', [AdminCertificateController::class, 'print'])->name('certificates.print');
    Route::get('/certificates/{certificate}/marksheet', [AdminCertificateController::class, 'marksheet'])->name('certificates.marksheet');
    Route::post('/certificates/{certificate}/revoke', [AdminCertificateController::class, 'revoke'])->name('certificates.revoke');
    Route::post('/certificates/{certificate}/reissue', [AdminCertificateController::class, 'reissue'])->name('certificates.reissue');
    Route::resource('certificates', AdminCertificateController::class);

    // Module 9: Fees & Payments
    Route::get('/fees/dues', [AdminFeeController::class, 'dues'])->name('fees.dues');
    Route::get('/fees/receipts/{receipt}', [AdminFeeController::class, 'receipt'])->name('fees.receipt');
    Route::post('/fees/payments/{payment}/refund', [AdminFeeController::class, 'refund'])->name('fees.refund');
    Route::get('/fees/structures', [AdminFeeController::class, 'structures'])->name('fees.structures');
    Route::get('/fees/structures/create', [AdminFeeController::class, 'createStructure'])->name('fees.structures.create');
    Route::post('/fees/structures', [AdminFeeController::class, 'storeStructure'])->name('fees.structures.store');
    Route::get('/fees/structures/{structure}/edit', [AdminFeeController::class, 'editStructure'])->name('fees.structures.edit');
    Route::put('/fees/structures/{structure}', [AdminFeeController::class, 'updateStructure'])->name('fees.structures.update');
    Route::delete('/fees/structures/{structure}', [AdminFeeController::class, 'destroyStructure'])->name('fees.structures.destroy');
    Route::get('/fees/admissions/{admission}/schedule', [AdminFeeController::class, 'assignScheduleForm'])->name('fees.schedule');
    Route::post('/fees/admissions/{admission}/schedule', [AdminFeeController::class, 'storeScheduleAssignment'])->name('fees.schedule.store');
    Route::resource('fees', AdminFeeController::class)->only(['index', 'create', 'store']);

    // Module 10: Franchise & Centres
    Route::get('/centres/applications', [AdminCentreController::class, 'applications'])->name('centres.applications');
    Route::get('/centres/applications/{application}', [AdminCentreController::class, 'showApplication'])->name('centres.applications.show');
    Route::post('/centres/applications/{application}/approve', [AdminCentreController::class, 'approveApplication'])->name('centres.applications.approve');
    Route::post('/centres/applications/{application}/reject', [AdminCentreController::class, 'rejectApplication'])->name('centres.applications.reject');
    Route::get('/centres/{centre}/certificate', [AdminCentreController::class, 'certificate'])->name('centres.certificate');
    Route::post('/centres/{centre}/toggle-status', [AdminCentreController::class, 'toggleStatus'])->name('centres.toggle-status');
    Route::resource('centres', AdminCentreController::class);

    // Module 11: Downloads & Orders
    Route::get('/downloads/orders', [AdminDownloadController::class, 'orders'])->name('downloads.orders');
    Route::get('/downloads/orders/create', [AdminDownloadController::class, 'createOrder'])->name('downloads.orders.create');
    Route::post('/downloads/orders', [AdminDownloadController::class, 'storeOrder'])->name('downloads.orders.store');
    Route::get('/downloads/orders/{order}', [AdminDownloadController::class, 'showOrder'])->name('downloads.orders.show');
    Route::post('/downloads/orders/{order}/status', [AdminDownloadController::class, 'updateOrderStatus'])->name('downloads.orders.status');
    Route::post('/downloads/{download}/toggle-status', [AdminDownloadController::class, 'toggleStatus'])->name('downloads.toggle-status');
    Route::get('/downloads/{download}/download', [AdminDownloadController::class, 'downloadFile'])->name('downloads.download-file');
    Route::resource('downloads', AdminDownloadController::class);

    // Module 12: Wallet History & Ledger Adjustments
    Route::get('/wallet', [AdminWalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/adjust', [AdminWalletController::class, 'adjust'])->name('wallet.adjust');
    Route::post('/wallet/adjust', [AdminWalletController::class, 'storeAdjustment'])->name('wallet.adjust.store');
    Route::post('/wallet/transactions/{transaction}/approve', [AdminWalletController::class, 'approveTransaction'])->name('wallet.transactions.approve');
    Route::post('/wallet/transactions/{transaction}/reject', [AdminWalletController::class, 'rejectTransaction'])->name('wallet.transactions.reject');
    Route::post('/wallet/centres/{centre}/freeze', [AdminWalletController::class, 'toggleFreeze'])->name('wallet.centres.freeze');
    Route::get('/wallet/centres/{centre}/statement', [AdminWalletController::class, 'statement'])->name('wallet.statement');

    // Module 13: Help & Support
    Route::post('/support/{ticket}/reply', [AdminSupportController::class, 'reply'])->name('support.reply');
    Route::post('/support/{ticket}/assign', [AdminSupportController::class, 'assign'])->name('support.assign');
    Route::post('/support/{ticket}/status', [AdminSupportController::class, 'updateStatus'])->name('support.status');
    Route::resource('support', AdminSupportController::class)->parameters(['support' => 'ticket']);

    // Module 14: Expenses & Operating Costs
    Route::get('/expenses/marketing', [AdminExpenseController::class, 'marketing'])->name('expenses.marketing');
    Route::get('/expenses/marketing/create', [AdminExpenseController::class, 'createMarketing'])->name('expenses.marketing.create');
    Route::post('/expenses/marketing', [AdminExpenseController::class, 'storeMarketing'])->name('expenses.marketing.store');
    Route::get('/expenses/marketing/{marketing}/edit', [AdminExpenseController::class, 'editMarketing'])->name('expenses.marketing.edit');
    Route::put('/expenses/marketing/{marketing}', [AdminExpenseController::class, 'updateMarketing'])->name('expenses.marketing.update');
    Route::delete('/expenses/marketing/{marketing}', [AdminExpenseController::class, 'destroyMarketing'])->name('expenses.marketing.destroy');
    Route::resource('expenses', AdminExpenseController::class);

    // Module 15: Website CMS
    Route::get('/cms', [AdminCmsController::class, 'index'])->name('cms.index');

    // CMS Notices / Announcements
    Route::get('/cms/notices', [AdminCmsController::class, 'notices'])->name('cms.notices');
    Route::get('/cms/notices/create', [AdminCmsController::class, 'createNotice'])->name('cms.notices.create');
    Route::post('/cms/notices', [AdminCmsController::class, 'storeNotice'])->name('cms.notices.store');
    Route::get('/cms/notices/{notice}/edit', [AdminCmsController::class, 'editNotice'])->name('cms.notices.edit');
    Route::put('/cms/notices/{notice}', [AdminCmsController::class, 'updateNotice'])->name('cms.notices.update');
    Route::delete('/cms/notices/{notice}', [AdminCmsController::class, 'destroyNotice'])->name('cms.notices.destroy');
    Route::post('/cms/notices/{notice}/toggle-status', [AdminCmsController::class, 'toggleNoticeStatus'])->name('cms.notices.toggle-status');

    // CMS Gallery & Visuals
    Route::get('/cms/gallery', [AdminCmsController::class, 'gallery'])->name('cms.gallery');
    Route::get('/cms/gallery/create', [AdminCmsController::class, 'createGallery'])->name('cms.gallery.create');
    Route::post('/cms/gallery', [AdminCmsController::class, 'storeGallery'])->name('cms.gallery.store');
    Route::get('/cms/gallery/{gallery}/edit', [AdminCmsController::class, 'editGallery'])->name('cms.gallery.edit');
    Route::put('/cms/gallery/{gallery}', [AdminCmsController::class, 'updateGallery'])->name('cms.gallery.update');
    Route::delete('/cms/gallery/{gallery}', [AdminCmsController::class, 'destroyGallery'])->name('cms.gallery.destroy');
    Route::post('/cms/gallery/{gallery}/toggle-status', [AdminCmsController::class, 'toggleGalleryStatus'])->name('cms.gallery.toggle-status');

    // CMS Public Inquiries & Leads
    Route::get('/cms/enquiries', [AdminCmsController::class, 'enquiries'])->name('cms.enquiries');
    Route::get('/cms/enquiries/{enquiry}', [AdminCmsController::class, 'showEnquiry'])->name('cms.enquiries.show');
    Route::put('/cms/enquiries/{enquiry}', [AdminCmsController::class, 'updateEnquiryStatus'])->name('cms.enquiries.update');
    Route::delete('/cms/enquiries/{enquiry}', [AdminCmsController::class, 'destroyEnquiry'])->name('cms.enquiries.destroy');

    // CMS Content & Settings
    Route::get('/cms/content', [AdminCmsController::class, 'content'])->name('cms.content');
    Route::post('/cms/content', [AdminCmsController::class, 'updateContent'])->name('cms.content.update');

    // Module 16: Notifications & Broadcasting
    Route::get('/notifications', [AdminNotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/create', [AdminNotificationController::class, 'create'])->name('notifications.create');
    Route::post('/notifications', [AdminNotificationController::class, 'store'])->name('notifications.store');
    Route::post('/notifications/{notification}/read', [AdminNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [AdminNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [AdminNotificationController::class, 'destroy'])->name('notifications.destroy');

    // Module 17: Reports & Analytics
    Route::get('/reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/students', [AdminReportController::class, 'students'])->name('reports.students');
    Route::get('/reports/students/export', [AdminReportController::class, 'exportStudents'])->name('reports.students.export');
    Route::get('/reports/fees', [AdminReportController::class, 'fees'])->name('reports.fees');
    Route::get('/reports/fees/export', [AdminReportController::class, 'exportFees'])->name('reports.fees.export');
    Route::get('/reports/attendance', [AdminReportController::class, 'attendance'])->name('reports.attendance');
    Route::get('/reports/attendance/export', [AdminReportController::class, 'exportAttendance'])->name('reports.attendance.export');
    Route::get('/reports/exams', [AdminReportController::class, 'exams'])->name('reports.exams');
    Route::get('/reports/exams/export', [AdminReportController::class, 'exportExams'])->name('reports.exams.export');
    Route::get('/reports/expenses', [AdminReportController::class, 'expenses'])->name('reports.expenses');
    Route::get('/reports/expenses/export', [AdminReportController::class, 'exportExpenses'])->name('reports.expenses.export');
    Route::get('/reports/financial', [AdminReportController::class, 'financial'])->name('reports.financial');
    Route::get('/reports/financial/export', [AdminReportController::class, 'exportFinancial'])->name('reports.financial.export');

    // Module 18: User Management
    Route::post('/users/{user}/toggle-status', [AdminUserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('/users/{user}/reset-password', [AdminUserController::class, 'resetPassword'])->name('users.reset-password');
    Route::resource('users', AdminUserController::class);

    // Module 19: System Settings
    Route::get('/settings', [AdminSettingController::class, 'index'])->name('settings.index');
    Route::post('/settings', [AdminSettingController::class, 'update'])->name('settings.update');

    // Module 20: Audit Logs
    Route::get('/audit-logs', [AdminAuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/export', [AdminAuditLogController::class, 'export'])->name('audit-logs.export');
    Route::get('/audit-logs/{auditLog}', [AdminAuditLogController::class, 'show'])->name('audit-logs.show');
});

Route::middleware(['role:super_admin,centre,staff', 'centre.active'])->prefix('centre')->name('centre.')->group(function () {
    Route::get('/dashboard', [CentreDashboardController::class, 'index'])->name('dashboard');

    // Students & Direct Admission
    Route::get('/students', [CentreStudentController::class, 'index'])->name('students.index');
    Route::get('/students/create', [CentreStudentController::class, 'create'])->name('students.create');
    Route::post('/students', [CentreStudentController::class, 'store'])->name('students.store');
    Route::get('/students/{student}', [CentreStudentController::class, 'show'])->name('students.show');
    Route::get('/students/{student}/id-card', [CentreStudentController::class, 'idCard'])->name('students.id-card');
    Route::get('/students/{student}/admission-form', [CentreStudentController::class, 'admissionForm'])->name('students.admission-form');

    // Batches & Attendance
    Route::get('/batches', [CentreBatchController::class, 'index'])->name('batches.index');
    Route::get('/batches/create', [CentreBatchController::class, 'create'])->name('batches.create');
    Route::post('/batches', [CentreBatchController::class, 'store'])->name('batches.store');
    Route::get('/batches/{batch}/edit', [CentreBatchController::class, 'edit'])->name('batches.edit');
    Route::put('/batches/{batch}', [CentreBatchController::class, 'update'])->name('batches.update');
    Route::get('/batches/{batch}/attendance', [CentreBatchController::class, 'attendanceSheet'])->name('batches.attendance');
    Route::post('/batches/{batch}/attendance', [CentreBatchController::class, 'markAttendance'])->name('batches.attendance.mark');
    Route::get('/batches/attendance/report', [CentreBatchController::class, 'attendanceReport'])->name('batches.attendance.report');

    // Exams, Hall Tickets, Marks Entry & Results
    Route::get('/exams', [CentreExamController::class, 'index'])->name('exams.index');
    Route::get('/exams/hall-tickets', [CentreExamController::class, 'hallTicketsIndex'])->name('exams.hall-tickets.index');
    Route::get('/exams/{exam}/hall-tickets', [CentreExamController::class, 'hallTickets'])->name('exams.hall-tickets');
    Route::get('/exams/{exam}/hall-tickets/student/{student}', [CentreExamController::class, 'singleHallTicket'])->name('exams.hall-tickets.single');
    Route::get('/exams/marks', [CentreExamController::class, 'marksIndex'])->name('exams.marks.index');
    Route::get('/exams/{exam}/marks', [CentreExamController::class, 'marksForm'])->name('exams.marks');
    Route::post('/exams/{exam}/marks', [CentreExamController::class, 'storeMarks'])->name('exams.marks.store');
    Route::get('/exams/results', [CentreExamController::class, 'results'])->name('exams.results');
    Route::get('/exams/results/{result}/marksheet', [CentreExamController::class, 'marksheet'])->name('exams.results.marksheet');

    // Fee Collections, Receipts & Dues
    Route::get('/fees', [CentreFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/create', [CentreFeeController::class, 'create'])->name('fees.create');
    Route::post('/fees', [CentreFeeController::class, 'store'])->name('fees.store');
    Route::get('/fees/receipts/{receipt}', [CentreFeeController::class, 'receipt'])->name('fees.receipt');
    Route::get('/fees/dues', [CentreFeeController::class, 'dues'])->name('fees.dues');

    // Wallet & Top-up Requests
    Route::get('/wallet', [CentreWalletController::class, 'index'])->name('wallet.index');
    Route::get('/wallet/topup', [CentreWalletController::class, 'topupForm'])->name('wallet.topup');
    Route::post('/wallet/topup', [CentreWalletController::class, 'storeTopup'])->name('wallet.topup.store');

    // Downloads & Certificates
    Route::get('/downloads', [CentreDownloadController::class, 'index'])->name('downloads.index');
    Route::get('/downloads/student-certificates', [CentreDownloadController::class, 'studentCertificates'])->name('downloads.student-certificates');
    Route::get('/downloads/student-certificates/{certificate}', [CentreDownloadController::class, 'showStudentCertificate'])->name('downloads.student-certificates.show');
    Route::get('/downloads/certificate', [CentreDownloadController::class, 'certificate'])->name('downloads.certificate');
    Route::get('/downloads/{download}/file', [CentreDownloadController::class, 'downloadFile'])->name('downloads.file');

    // Help & Support Desk
    Route::get('/support', [CentreSupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [CentreSupportController::class, 'create'])->name('support.create');
    Route::post('/support', [CentreSupportController::class, 'store'])->name('support.store');
    Route::get('/support/{ticket}', [CentreSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{ticket}/reply', [CentreSupportController::class, 'reply'])->name('support.reply');

    // Profile & Settings
    Route::get('/profile', [CentreProfileController::class, 'show'])->name('profile.show');
    Route::post('/profile', [CentreProfileController::class, 'update'])->name('profile.update');
    Route::post('/profile/change-password', [CentreProfileController::class, 'changePassword'])->name('profile.change-password');
});

Route::middleware(['role:super_admin,student'])->prefix('student')->name('student.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StudentPortalController::class, 'dashboard'])->name('dashboard');

    // Profile & ID
    Route::get('/profile', [StudentPortalController::class, 'profile'])->name('profile');
    Route::post('/profile', [StudentPortalController::class, 'updateProfile'])->name('profile.update');
    Route::get('/admission-form', [StudentPortalController::class, 'admissionForm'])->name('admission-form');
    Route::get('/id-card', [StudentPortalController::class, 'idCard'])->name('id-card');
    Route::get('/course', [StudentPortalController::class, 'course'])->name('course');
    Route::get('/schedule', [StudentPortalController::class, 'schedule'])->name('schedule');
    Route::get('/attendance', [StudentPortalController::class, 'attendance'])->name('attendance');
    Route::get('/change-password', [StudentPortalController::class, 'changePasswordForm'])->name('change-password');
    Route::post('/change-password', [StudentPortalController::class, 'changePassword'])->name('change-password.update');

    // Fees & Payments
    Route::get('/fees', [StudentFeeController::class, 'index'])->name('fees');
    Route::get('/fees/list', [StudentFeeController::class, 'index'])->name('fees.index');
    Route::get('/fees/pay', [StudentFeeController::class, 'payFeeForm'])->name('fees.pay');
    Route::post('/fees/pay', [StudentFeeController::class, 'processPayment'])->name('fees.pay.submit');
    Route::get('/fees/history', [StudentFeeController::class, 'history'])->name('fees.history');
    Route::get('/fees/receipts/{id}', [StudentFeeController::class, 'receipt'])->name('fees.receipt');

    // Academics: Materials, Exams, Results, Certificates
    Route::get('/study-materials', [StudentAcademicController::class, 'studyMaterials'])->name('study-materials');
    Route::get('/study-materials/{id}/download', [StudentAcademicController::class, 'downloadMaterial'])->name('study-materials.download');
    Route::get('/exams', [StudentAcademicController::class, 'exams'])->name('exams');
    Route::get('/exams/{exam_id}/admit-card', [StudentAcademicController::class, 'admitCard'])->name('admit-card');
    Route::get('/results', [StudentAcademicController::class, 'results'])->name('results');
    Route::get('/results/{id}/marksheet', [StudentAcademicController::class, 'marksheet'])->name('marksheet');
    Route::get('/certificates', [StudentAcademicController::class, 'certificates'])->name('certificates');

    // Support Tickets
    Route::get('/support', [StudentSupportController::class, 'index'])->name('support');
    Route::get('/support/list', [StudentSupportController::class, 'index'])->name('support.index');
    Route::get('/support/create', [StudentSupportController::class, 'create'])->name('support.create');
    Route::post('/support', [StudentSupportController::class, 'store'])->name('support.store');
    Route::get('/support/{id}', [StudentSupportController::class, 'show'])->name('support.show');
    Route::post('/support/{id}/reply', [StudentSupportController::class, 'reply'])->name('support.reply');

    // System Notifications
    Route::get('/notifications', [StudentNotificationController::class, 'index'])->name('notifications');
    Route::get('/notifications/list', [StudentNotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [StudentNotificationController::class, 'markRead'])->name('notifications.read');
    Route::post('/notifications/mark-all-read', [StudentNotificationController::class, 'markAllRead'])->name('notifications.mark-all-read');
});

// 9. Geo AJAX & Utility Routes
Route::get('/api/districts/{state_id}', [GeoController::class, 'getDistricts'])->name('api.districts');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
