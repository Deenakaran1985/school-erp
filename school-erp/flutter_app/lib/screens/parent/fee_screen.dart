import 'package:flutter/material.dart';
import 'package:razorpay_flutter/razorpay_flutter.dart';
import '../../core/services/fee_service.dart';
import '../../core/utils/app_theme.dart';

class FeeScreen extends StatefulWidget {
  const FeeScreen({super.key});

  @override
  State<FeeScreen> createState() => _FeeScreenState();
}

class _FeeScreenState extends State<FeeScreen> with SingleTickerProviderStateMixin {
  final _feeService = FeeService();
  late TabController _tabs;
  late Razorpay _razorpay;

  List<dynamic> _pending = [];
  List<dynamic> _history = [];
  bool _loading = true;
  int? _activePaymentId;

  @override
  void initState() {
    super.initState();
    _tabs = TabController(length: 2, vsync: this);
    _setupRazorpay();
    _load();
  }

  void _setupRazorpay() {
    _razorpay = Razorpay();
    _razorpay.on(Razorpay.EVENT_PAYMENT_SUCCESS, _onPaymentSuccess);
    _razorpay.on(Razorpay.EVENT_PAYMENT_ERROR,   _onPaymentError);
  }

  Future<void> _load() async {
    setState(() => _loading = true);
    try {
      final [p, h] = await Future.wait([
        _feeService.getPendingFees(),
        _feeService.getHistory(),
      ]);
      setState(() {
        _pending = p['data'] ?? [];
        _history = h['data'] ?? [];
      });
    } catch (_) {}
    setState(() => _loading = false);
  }

  Future<void> _payFee(Map<String, dynamic> fee) async {
    try {
      final order = await _feeService.createOrder(
        studentId:      fee['student_id'],
        feeStructureId: fee['fee_structure_id'],
      );

      if (order['success'] != true) {
        _showSnack(order['message'] ?? 'Payment error', isError: true);
        return;
      }

      _activePaymentId = order['payment_id'];

      _razorpay.open({
        'key':         order['key'],
        'amount':      order['amount'],
        'currency':    order['currency'],
        'name':        'School ERP',
        'description': order['description'],
        'order_id':    order['order_id'],
        'prefill':     order['prefill'],
      });
    } catch (_) {
      _showSnack('Failed to initiate payment.', isError: true);
    }
  }

  void _onPaymentSuccess(PaymentSuccessResponse response) async {
    try {
      final result = await _feeService.verifyPayment(
        paymentId:          _activePaymentId!,
        razorpayOrderId:    response.orderId!,
        razorpayPaymentId:  response.paymentId!,
        razorpaySignature:  response.signature!,
      );
      _showSnack(result['message'] ?? 'Payment successful!');
      _load();
    } catch (_) {
      _showSnack('Verification failed. Contact school.', isError: true);
    }
  }

  void _onPaymentError(PaymentFailureResponse response) {
    _showSnack('Payment failed: ${response.message}', isError: true);
  }

  void _showSnack(String msg, {bool isError = false}) {
    ScaffoldMessenger.of(context).showSnackBar(SnackBar(
      content: Text(msg),
      backgroundColor: isError ? AppTheme.error : AppTheme.success,
    ));
  }

  @override
  void dispose() {
    _razorpay.clear();
    _tabs.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Fee Payment'),
        bottom: TabBar(
          controller: _tabs,
          tabs: const [Tab(text: 'Pending'), Tab(text: 'History')],
        ),
      ),
      body: _loading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabs,
              children: [_buildPending(), _buildHistory()],
            ),
    );
  }

  Widget _buildPending() {
    if (_pending.isEmpty) {
      return Center(
        child: Column(mainAxisAlignment: MainAxisAlignment.center, children: [
          const Icon(Icons.check_circle_outline_rounded, size: 60, color: AppTheme.success),
          const SizedBox(height: 12),
          Text('All fees are paid!', style: AppTheme.heading2),
        ]),
      );
    }

    return RefreshIndicator(
      onRefresh: _load,
      child: ListView.separated(
        padding: const EdgeInsets.all(16),
        itemCount: _pending.length,
        separatorBuilder: (_, __) => const SizedBox(height: 8),
        itemBuilder: (_, i) {
          final fee = _pending[i] as Map<String, dynamic>;
          return Card(
            child: ListTile(
              contentPadding: const EdgeInsets.all(16),
              title: Text(fee['fee_head'] ?? '', style: AppTheme.labelBold),
              subtitle: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Text('${fee['student_name']} · Class ${fee['class']}', style: AppTheme.bodySmall),
                  if (fee['due_date'] != null)
                    Text('Due: ${fee['due_date']}', style: AppTheme.bodySmall),
                  if (fee['overdue'] == true)
                    Text('OVERDUE', style: const TextStyle(color: AppTheme.error, fontSize: 11, fontWeight: FontWeight.bold)),
                ],
              ),
              trailing: Column(
                mainAxisAlignment: MainAxisAlignment.center,
                crossAxisAlignment: CrossAxisAlignment.end,
                children: [
                  Text('₹${_fmt(fee['amount'])}',
                      style: AppTheme.heading2.copyWith(color: AppTheme.primary)),
                  const SizedBox(height: 6),
                  ElevatedButton(
                    onPressed: () => _payFee(fee),
                    style: ElevatedButton.styleFrom(
                      backgroundColor: AppTheme.primary,
                      minimumSize: const Size(72, 30),
                      padding: const EdgeInsets.symmetric(horizontal: 12),
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text('Pay', style: TextStyle(color: Colors.white, fontSize: 12)),
                  ),
                ],
              ),
            ),
          );
        },
      ),
    );
  }

  Widget _buildHistory() {
    if (_history.isEmpty) {
      return Center(child: Text('No payment history.', style: AppTheme.bodySmall));
    }

    return ListView.separated(
      padding: const EdgeInsets.all(16),
      itemCount: _history.length,
      separatorBuilder: (_, __) => const SizedBox(height: 8),
      itemBuilder: (_, i) {
        final p = _history[i] as Map<String, dynamic>;
        return Card(
          child: ListTile(
            leading: const CircleAvatar(
              backgroundColor: Color(0xFFD1FAE5),
              child: Icon(Icons.check_rounded, color: AppTheme.success),
            ),
            title: Text(p['fee_head'] ?? '', style: AppTheme.labelBold),
            subtitle: Text('${p['student']} · ${p['date']} · ${p['mode']}', style: AppTheme.bodySmall),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text('₹${_fmt(p['amount'])}', style: const TextStyle(fontWeight: FontWeight.bold, color: AppTheme.success)),
                Text(p['receipt_no'] ?? '', style: AppTheme.bodySmall),
              ],
            ),
          ),
        );
      },
    );
  }

  String _fmt(dynamic amount) {
    if (amount == null) return '0';
    final num = double.tryParse(amount.toString()) ?? 0;
    return num.toStringAsFixed(0);
  }
}
