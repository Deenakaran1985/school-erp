import '../api/api_client.dart';
import '../api/api_endpoints.dart';

class StudentService {
  final _dio = ApiClient.instance;

  Future<Map<String, dynamic>> getProfile() async {
    final r = await _dio.get(ApiEndpoints.profile);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getAttendance(int studentId, String month) async {
    final r = await _dio.get(ApiEndpoints.attendance(studentId, month));
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getHomework(int studentId) async {
    final r = await _dio.get(ApiEndpoints.homework(studentId));
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getExams({int? studentId}) async {
    final r = await _dio.get(ApiEndpoints.exams,
        queryParameters: studentId != null ? {'student_id': studentId} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getResults({int? studentId}) async {
    final r = await _dio.get(ApiEndpoints.results,
        queryParameters: studentId != null ? {'student_id': studentId} : null);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getReportCard({int? studentId, int? examTypeId}) async {
    final r = await _dio.get(ApiEndpoints.reportCard, queryParameters: {
      if (studentId  != null) 'student_id':   studentId,
      if (examTypeId != null) 'exam_type_id': examTypeId,
    });
    return r.data as Map<String, dynamic>;
  }
}
