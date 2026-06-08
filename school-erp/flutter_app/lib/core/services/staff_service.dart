import '../api/api_client.dart';
import '../api/api_endpoints.dart';

class StaffService {
  final _dio = ApiClient.instance;

  Future<Map<String, dynamic>> getProfile() async {
    final r = await _dio.get(ApiEndpoints.staffProfile);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getPayslips() async {
    final r = await _dio.get(ApiEndpoints.staffPayslips);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getAttendance({String? month}) async {
    final r = await _dio.get(ApiEndpoints.staffAttendance,
        queryParameters: month != null ? {'month': month} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getMyClasses() async {
    final r = await _dio.get(ApiEndpoints.staffMyClasses);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getStudents({required int classId, int? sectionId}) async {
    final r = await _dio.get(ApiEndpoints.staffStudents, queryParameters: {
      'class_id': classId,
      if (sectionId != null) 'section_id': sectionId,
    });
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> markAttendance({
    required String date,
    required List<Map<String, dynamic>> records,
  }) async {
    final r = await _dio.post(ApiEndpoints.staffMarkAttend, data: {
      'date':    date,
      'records': records,
    });
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> createHomework({
    required int subjectId,
    required int classId,
    int? sectionId,
    required String title,
    String? description,
    required String dueDate,
  }) async {
    final r = await _dio.post(ApiEndpoints.staffHomework, data: {
      'subject_id':      subjectId,
      'school_class_id': classId,
      if (sectionId   != null) 'section_id': sectionId,
      'title':           title,
      if (description != null) 'description': description,
      'due_date':        dueDate,
    });
    return r.data as Map<String, dynamic>;
  }
}
