import '../api/api_client.dart';
import '../api/api_endpoints.dart';

class FeeService {
  final _dio = ApiClient.instance;

  Future<Map<String, dynamic>> getPendingFees() async {
    final r = await _dio.get(ApiEndpoints.feesPending);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> getHistory() async {
    final r = await _dio.get(ApiEndpoints.feesHistory);
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> createOrder({
    required int studentId,
    required int feeStructureId,
  }) async {
    final r = await _dio.post(ApiEndpoints.feesOrder, data: {
      'student_id':       studentId,
      'fee_structure_id': feeStructureId,
    });
    return r.data as Map<String, dynamic>;
  }

  Future<Map<String, dynamic>> verifyPayment({
    required int paymentId,
    required String razorpayOrderId,
    required String razorpayPaymentId,
    required String razorpaySignature,
  }) async {
    final r = await _dio.post(ApiEndpoints.feesVerify, data: {
      'payment_id':           paymentId,
      'razorpay_order_id':    razorpayOrderId,
      'razorpay_payment_id':  razorpayPaymentId,
      'razorpay_signature':   razorpaySignature,
    });
    return r.data as Map<String, dynamic>;
  }
}
