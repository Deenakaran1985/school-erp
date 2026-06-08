class ApiEndpoints {
  // Auth
  static const String login          = '/auth/login';
  static const String logout         = '/auth/logout';
  static const String me             = '/auth/me';
  static const String changePassword = '/auth/change-password';
  static const String updateFcm      = '/profile/update-fcm';
  static const String updateAvatar   = '/profile/update-avatar';

  // Student / Parent
  static const String profile        = '/profile';
  static String attendance(int studentId, String month) =>
      '/student/$studentId/attendance?month=$month';
  static String homework(int studentId) =>
      '/student/$studentId/homework';

  // Exams & Results
  static const String exams       = '/exams';
  static const String results     = '/results';
  static const String reportCard  = '/results/report-card';

  // Fees
  static const String feesPending = '/fees/pending';
  static const String feesHistory = '/fees/history';
  static const String feesOrder   = '/fees/create-order';
  static const String feesVerify  = '/fees/verify';

  // Notifications
  static const String notifications    = '/notifications';
  static String markRead(int id)       => '/notifications/$id/read';

  // Staff
  static const String staffProfile     = '/staff/profile';
  static const String staffPayslips    = '/staff/payslips';
  static const String staffAttendance  = '/staff/attendance';
  static const String staffMyClasses   = '/staff/my-classes';
  static const String staffStudents    = '/staff/students';
  static const String staffMarkAttend  = '/staff/attendance/mark';
  static const String staffHomework    = '/staff/homework';

  // Correspondent / Principal
  static const String corrDashboard    = '/correspondent/dashboard';
  static const String corrFeeSummary   = '/correspondent/fee-summary';
  static const String corrPayrollSumm  = '/correspondent/payroll-summary';
  static const String corrStaff        = '/correspondent/staff';
  static const String corrExpenses     = '/correspondent/expenses';
  static const String corrSendNotif    = '/correspondent/notifications/send';
}
