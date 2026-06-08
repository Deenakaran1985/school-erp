import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import '../api/api_client.dart';
import '../api/api_endpoints.dart';
import '../models/user_model.dart';

class AuthService {
  final _dio     = ApiClient.instance;
  final _storage = const FlutterSecureStorage();

  Future<Map<String, dynamic>> login({
    required String phone,
    required String password,
    String? fcmToken,
  }) async {
    final response = await _dio.post(
      ApiEndpoints.login,
      data: {
        'phone':     phone,
        'password':  password,
        if (fcmToken != null) 'fcm_token': fcmToken,
      },
    );
    final data = response.data as Map<String, dynamic>;

    if (data['success'] == true) {
      await _storage.write(key: 'auth_token', value: data['token']);
      await _storage.write(key: 'user_role',  value: data['role']);
    }
    return data;
  }

  Future<void> logout() async {
    try {
      await _dio.post(ApiEndpoints.logout);
    } catch (_) {}
    await _storage.deleteAll();
  }

  Future<String?> getToken() => _storage.read(key: 'auth_token');
  Future<String?> getRole()  => _storage.read(key: 'user_role');

  Future<bool> isLoggedIn() async {
    final token = await getToken();
    return token != null && token.isNotEmpty;
  }

  Future<Map<String, dynamic>> getMe() async {
    final response = await _dio.get(ApiEndpoints.me);
    return response.data as Map<String, dynamic>;
  }

  Future<void> changePassword({
    required String current,
    required String newPass,
  }) async {
    await _dio.post(ApiEndpoints.changePassword, data: {
      'current_password':      current,
      'new_password':          newPass,
      'new_password_confirmation': newPass,
    });
    await logout();
  }

  Future<void> updateFcmToken(String token) async {
    await _dio.post(ApiEndpoints.updateFcm, data: {'fcm_token': token});
  }
}
