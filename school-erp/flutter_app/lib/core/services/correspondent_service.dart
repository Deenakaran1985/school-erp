import '../api/api_client.dart';
import '../api/api_endpoints.dart';

class CorrespondentService {
  final _dio = ApiClient.instance;

  Future<Map<String, dynamic>> getDashboard() async {
    final r = await _dio.get(ApiEndpoints.corrDashboard);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getFeeSummary({String? month}) async {
    final r = await _dio.get(ApiEndpoints.corrFeeSummary,
        queryParameters: month != null ? {'month': month} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getPayrollSummary({String? monthYear}) async {
    final r = await _dio.get(ApiEndpoints.corrPayrollSumm,
        queryParameters: monthYear != null ? {'month_year': monthYear} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getStaff({int? departmentId}) async {
    final r = await _dio.get(ApiEndpoints.corrStaff,
        queryParameters: departmentId != null ? {'department_id': departmentId} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getExpenses({String? status, String? month}) async {
    final r = await _dio.get(ApiEndpoints.corrExpenses, queryParameters: {
      if (status != null) 'status': status,
      if (month  != null) 'month':  month,
    });
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> sendNotification({
    required String title,
    required String body,
    required String targetRole,
    String? type,
  }) async {
    final r = await _dio.post(ApiEndpoints.corrSendNotif, data: {
      'title':       title,
      'body':        body,
      'target_role': targetRole,
      if (type != null) 'type': type,
    });
    return r.data as Map<String, dynamic>;
  }
}
