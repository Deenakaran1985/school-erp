import 'package:dio/dio.dart';
import 'package:flutter_secure_storage/flutter_secure_storage.dart';
import 'package:pretty_dio_logger/pretty_dio_logger.dart';

class ApiClient {
  static const String baseUrl = 'http://your-server.com/api'; // Change to your server URL
  static const _storage = FlutterSecureStorage();

  static Dio get _dio => _instance;
  static final Dio _instance = _build();

  static Dio _build() {
    final dio = Dio(BaseOptions(
      baseUrl: baseUrl,
      connectTimeout: const Duration(seconds: 15),
      receiveTimeout: const Duration(seconds: 30),
      headers: {'Accept': 'application/json'},
    ));

    dio.interceptors.addAll([
      // Attach Bearer token from secure storage
      InterceptorsWrapper(
        onRequest: (options, handler) async {
          final token = await _storage.read(key: 'auth_token');
          if (token != null) {
            options.headers['Authorization'] = 'Bearer $token';
          }
          handler.next(options);
        },
        onError: (error, handler) {
          if (error.response?.statusCode == 401) {
            // Token expired — clear & navigate to login handled by app router
            _storage.delete(key: 'auth_token');
          }
          handler.next(error);
        },
      ),
      PrettyDioLogger(
        requestHeader: false,
        requestBody: true,
        responseBody: true,
        error: true,
        compact: true,
      ),
    ]);

    return dio;
  }

  static Dio get instance => _dio;
}
