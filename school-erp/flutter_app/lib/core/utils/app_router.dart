import 'package:go_router/go_router.dart';
import '../../screens/auth/login_screen.dart';

// Parent
import '../../screens/parent/parent_home_screen.dart';
import '../../screens/parent/fee_screen.dart';
import '../../screens/parent/results_screen.dart' as parent_results;

// Student
import '../../screens/student/student_home_screen.dart';
import '../../screens/student/results_screen.dart';
import '../../screens/student/attendance_screen.dart';
import '../../screens/student/homework_screen.dart';

// Staff
import '../../screens/staff/staff_home_screen.dart';
import '../../screens/staff/mark_attendance_screen.dart';
import '../../screens/staff/payslips_screen.dart';
import '../../screens/staff/create_homework_screen.dart';

// Correspondent
import '../../screens/correspondent/correspondent_home_screen.dart';
import '../../screens/correspondent/fee_summary_screen.dart';
import '../../screens/correspondent/payroll_summary_screen.dart';
import '../../screens/correspondent/staff_list_screen.dart';
import '../../screens/correspondent/expenses_screen.dart';
import '../../screens/correspondent/send_notification_screen.dart';

// Shared
import '../../screens/shared/notifications_screen.dart';

import '../services/auth_service.dart';

final appRouter = GoRouter(
  initialLocation: '/login',
  redirect: (context, state) async {
    final loggedIn = await AuthService().isLoggedIn();
    final onLogin  = state.matchedLocation == '/login';
    if (!loggedIn && !onLogin) return '/login';
    if (loggedIn  &&  onLogin) {
      final role = await AuthService().getRole();
      return switch (role) {
        'parent'                                         => '/parent/home',
        'student'                                        => '/student/home',
        'teacher'                                        => '/staff/home',
        'correspondent' || 'principal' || 'super_admin' => '/correspondent/home',
        _                                                => '/student/home',
      };
    }
    return null;
  },
  routes: [
    GoRoute(path: '/login', builder: (_, __) => const LoginScreen()),

    // ── Shared ─────────────────────────────────────────────────────────────
    GoRoute(
      path: '/notifications',
      builder: (_, __) => const NotificationsScreen(),
    ),

    // ── Parent ─────────────────────────────────────────────────────────────
    GoRoute(path: '/parent/home',    builder: (_, __) => const ParentHomeScreen()),
    GoRoute(path: '/parent/fees',    builder: (_, __) => const FeeScreen()),
    GoRoute(
      path: '/parent/results/:studentId',
      builder: (_, state) {
        final id = int.parse(state.pathParameters['studentId']!);
        return parent_results.ParentResultsScreen(studentId: id);
      },
    ),

    // ── Student ────────────────────────────────────────────────────────────
    GoRoute(path: '/student/home',       builder: (_, __) => const StudentHomeScreen()),
    GoRoute(path: '/student/results',    builder: (_, __) => const ResultsScreen()),
    GoRoute(path: '/student/attendance', builder: (_, __) => const AttendanceScreen()),
    GoRoute(path: '/student/homework',   builder: (_, __) => const HomeworkScreen()),

    // ── Staff ──────────────────────────────────────────────────────────────
    GoRoute(path: '/staff/home',           builder: (_, __) => const StaffHomeScreen()),
    GoRoute(path: '/staff/payslips',       builder: (_, __) => const PayslipsScreen()),
    GoRoute(path: '/staff/mark-attendance',builder: (_, __) => const MarkAttendanceScreen()),
    GoRoute(path: '/staff/homework/create',builder: (_, __) => const CreateHomeworkScreen()),

    // ── Correspondent ──────────────────────────────────────────────────────
    GoRoute(path: '/correspondent/home',          builder: (_, __) => const CorrespondentHomeScreen()),
    GoRoute(path: '/correspondent/fee-summary',   builder: (_, __) => const FeeSummaryScreen()),
    GoRoute(path: '/correspondent/payroll',       builder: (_, __) => const PayrollSummaryScreen()),
    GoRoute(path: '/correspondent/staff',         builder: (_, __) => const StaffListScreen()),
    GoRoute(path: '/correspondent/expenses',      builder: (_, __) => const ExpensesScreen()),
    GoRoute(path: '/correspondent/notifications', builder: (_, __) => const SendNotificationScreen()),
  ],
);
