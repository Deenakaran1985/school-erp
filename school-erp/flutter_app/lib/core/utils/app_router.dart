import 'package:go_router/go_router.dart';
import '../../screens/auth/login_screen.dart';
import '../../screens/parent/parent_home_screen.dart';
import '../../screens/parent/fee_screen.dart';
import '../../screens/student/student_home_screen.dart';
import '../../screens/staff/staff_home_screen.dart';
import '../../screens/correspondent/correspondent_home_screen.dart';
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
        'parent'        => '/parent/home',
        'student'       => '/student/home',
        'teacher'       => '/staff/home',
        'correspondent' || 'principal' || 'super_admin' => '/correspondent/home',
        _ => '/student/home',
      };
    }
    return null;
  },
  routes: [
    GoRoute(path: '/login',              builder: (_, __) => const LoginScreen()),

    // Parent
    GoRoute(path: '/parent/home',        builder: (_, __) => const ParentHomeScreen()),
    GoRoute(path: '/parent/fees',        builder: (_, __) => const FeeScreen()),

    // Student
    GoRoute(path: '/student/home',       builder: (_, __) => const StudentHomeScreen()),

    // Staff
    GoRoute(path: '/staff/home',         builder: (_, __) => const StaffHomeScreen()),

    // Correspondent
    GoRoute(path: '/correspondent/home', builder: (_, __) => const CorrespondentHomeScreen()),
  ],
);
