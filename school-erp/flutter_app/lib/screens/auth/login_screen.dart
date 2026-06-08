import 'package:flutter/material.dart';
import 'package:flutter_riverpod/flutter_riverpod.dart';
import 'package:go_router/go_router.dart';
import '../../core/services/auth_service.dart';
import '../../core/utils/app_theme.dart';

class LoginScreen extends ConsumerStatefulWidget {
  const LoginScreen({super.key});

  @override
  ConsumerState<LoginScreen> createState() => _LoginScreenState();
}

class _LoginScreenState extends ConsumerState<LoginScreen> {
  final _form     = GlobalKey<FormState>();
  final _phone    = TextEditingController();
  final _password = TextEditingController();
  final _auth     = AuthService();

  bool _loading     = false;
  bool _showPass    = false;
  String? _error;

  @override
  void dispose() {
    _phone.dispose();
    _password.dispose();
    super.dispose();
  }

  Future<void> _login() async {
    if (!_form.currentState!.validate()) return;
    setState(() { _loading = true; _error = null; });

    try {
      final result = await _auth.login(
        phone:    _phone.text.trim(),
        password: _password.text,
      );

      if (!mounted) return;

      if (result['success'] == true) {
        final role = result['role'] as String;
        _navigateByRole(role);
      } else {
        setState(() => _error = result['message'] ?? 'Login failed.');
      }
    } catch (e) {
      setState(() => _error = 'Connection error. Check your network.');
    } finally {
      if (mounted) setState(() => _loading = false);
    }
  }

  void _navigateByRole(String role) {
    switch (role) {
      case 'parent':        context.go('/parent/home');       break;
      case 'student':       context.go('/student/home');      break;
      case 'teacher':       context.go('/staff/home');        break;
      case 'correspondent':
      case 'principal':
      case 'super_admin':   context.go('/correspondent/home'); break;
      default:              context.go('/student/home');
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppTheme.background,
      body: SafeArea(
        child: Center(
          child: SingleChildScrollView(
            padding: const EdgeInsets.all(24),
            child: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                // Logo / Branding
                Container(
                  width: 80, height: 80,
                  decoration: BoxDecoration(
                    color: AppTheme.primary,
                    borderRadius: BorderRadius.circular(20),
                  ),
                  child: const Icon(Icons.school_rounded, size: 44, color: Colors.white),
                ),
                const SizedBox(height: 24),
                Text('School ERP', style: AppTheme.heading1),
                const SizedBox(height: 6),
                Text('Sign in to continue', style: AppTheme.bodySmall),
                const SizedBox(height: 40),

                // Form Card
                Container(
                  padding: const EdgeInsets.all(24),
                  decoration: BoxDecoration(
                    color: Colors.white,
                    borderRadius: BorderRadius.circular(20),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withOpacity(0.06),
                        blurRadius: 20,
                        offset: const Offset(0, 8),
                      ),
                    ],
                  ),
                  child: Form(
                    key: _form,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.stretch,
                      children: [
                        if (_error != null) ...[
                          Container(
                            padding: const EdgeInsets.all(12),
                            decoration: BoxDecoration(
                              color: Colors.red.shade50,
                              borderRadius: BorderRadius.circular(12),
                              border: Border.all(color: Colors.red.shade200),
                            ),
                            child: Text(_error!, style: const TextStyle(color: Colors.red, fontSize: 13)),
                          ),
                          const SizedBox(height: 16),
                        ],
                        TextFormField(
                          controller: _phone,
                          keyboardType: TextInputType.phone,
                          decoration: AppTheme.inputDecoration(
                            label: 'Phone / Email',
                            icon: Icons.phone_android_rounded,
                          ),
                          validator: (v) => v == null || v.isEmpty ? 'Required' : null,
                        ),
                        const SizedBox(height: 16),
                        TextFormField(
                          controller: _password,
                          obscureText: !_showPass,
                          decoration: AppTheme.inputDecoration(
                            label: 'Password',
                            icon: Icons.lock_rounded,
                          ).copyWith(
                            suffixIcon: IconButton(
                              icon: Icon(_showPass ? Icons.visibility_off : Icons.visibility,
                                  size: 20, color: Colors.grey),
                              onPressed: () => setState(() => _showPass = !_showPass),
                            ),
                          ),
                          validator: (v) => v == null || v.isEmpty ? 'Required' : null,
                        ),
                        const SizedBox(height: 24),
                        SizedBox(
                          height: 48,
                          child: ElevatedButton(
                            onPressed: _loading ? null : _login,
                            style: ElevatedButton.styleFrom(
                              backgroundColor: AppTheme.primary,
                              shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(14)),
                            ),
                            child: _loading
                                ? const SizedBox(width: 20, height: 20,
                                    child: CircularProgressIndicator(strokeWidth: 2, color: Colors.white))
                                : const Text('Login', style: TextStyle(fontSize: 16, fontWeight: FontWeight.w600, color: Colors.white)),
                          ),
                        ),
                      ],
                    ),
                  ),
                ),
                const SizedBox(height: 20),
                Text('Contact school admin if you forgot your password.',
                  style: AppTheme.bodySmall, textAlign: TextAlign.center),
              ],
            ),
          ),
        ),
      ),
    );
  }
}
