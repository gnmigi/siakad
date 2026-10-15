import 'package:flutter/material.dart';
import 'package:intl/intl.dart';
import '../../services/api_service.dart';

class TagihanScreen extends StatefulWidget {
  const TagihanScreen({super.key});

  @override
  _TagihanScreenState createState() => _TagihanScreenState();
}

class _TagihanScreenState extends State<TagihanScreen> with SingleTickerProviderStateMixin {
  late TabController _tabController;
  bool _isLoading = true;
  List<dynamic> _unpaidTagihan = [];
  List<dynamic> _paymentHistory = [];
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _tabController = TabController(length: 2, vsync: this);
    _fetchData();
  }

  Future<void> _fetchData() async {
    setState(() => _isLoading = true);
    final resultTagihan = await _apiService.getTagihan();
    final resultHistory = await _apiService.getPaymentHistory();

    setState(() {
      if (resultTagihan['status'] == 'success') _unpaidTagihan = resultTagihan['data'];
      if (resultHistory['status'] == 'success') _paymentHistory = resultHistory['data'];
      _isLoading = false;
    });
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Keuangan & Tagihan", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.red.shade700,
        foregroundColor: Colors.white,
        bottom: TabBar(
          controller: _tabController,
          indicatorColor: Colors.white,
          labelColor: Colors.white,
          unselectedLabelColor: Colors.white70,
          tabs: const [
            Tab(text: "Tagihan Aktif"),
            Tab(text: "Riwayat Bayar"),
          ],
        ),
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : TabBarView(
              controller: _tabController,
              children: [
                _buildUnpaidList(),
                _buildHistoryList(),
              ],
            ),
    );
  }

  Widget _buildUnpaidList() {
    if (_unpaidTagihan.isEmpty) {
      return const Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            Icon(Icons.check_circle_outline, size: 64, color: Colors.green),
            SizedBox(height: 16),
            Text("Tidak ada tagihan tertunggak", style: TextStyle(color: Colors.grey)),
          ],
        ),
      );
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _unpaidTagihan.length,
      itemBuilder: (context, index) {
        final item = _unpaidTagihan[index];
        return Card(
          margin: const EdgeInsets.only(bottom: 16),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(15)),
          elevation: 2,
          child: Column(
            children: [
              ListTile(
                contentPadding: const EdgeInsets.all(16),
                title: Text(item['group']?['name'] ?? 'Tagihan Kuliah',
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 16)),
                subtitle: Text("Jatuh Tempo: ${item['due_date'] != null ? DateFormat('dd MMM yyyy').format(DateTime.parse(item['due_date'])) : '-'}"),
                trailing: Text(
                  _formatCurrency(item['amount']),
                  style: TextStyle(color: Colors.red.shade700, fontWeight: FontWeight.bold, fontSize: 16),
                ),
              ),
              const Divider(height: 1),
              Padding(
                padding: const EdgeInsets.all(12),
                child: SizedBox(
                  width: double.infinity,
                  child: ElevatedButton(
                    onPressed: () {
                      // Future: Link to payment gateway / instructions
                    },
                    style: ElevatedButton.styleFrom(
                      backgroundColor: Colors.red.shade700,
                      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(8)),
                    ),
                    child: const Text("Bayar Sekarang", style: TextStyle(color: Colors.white)),
                  ),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  Widget _buildHistoryList() {
    if (_paymentHistory.isEmpty) {
      return const Center(child: Text("Belum ada riwayat pembayaran", style: TextStyle(color: Colors.grey)));
    }

    return ListView.builder(
      padding: const EdgeInsets.all(16),
      itemCount: _paymentHistory.length,
      itemBuilder: (context, index) {
        final item = _paymentHistory[index];
        final status = item['status'] as String;
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          child: ListTile(
            leading: Icon(
              status == 'verified' ? Icons.verified : Icons.access_time,
              color: status == 'verified' ? Colors.green : Colors.orange,
            ),
            title: Text(item['tagihan']?['group']?['name'] ?? 'Pembayaran',
                style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
            subtitle: Text("Metode: ${item['payment_method']?['name'] ?? '-'}\nTanggal: ${DateFormat('dd/MM/yy HH:mm').format(DateTime.parse(item['created_at']))}"),
            trailing: Column(
              mainAxisAlignment: MainAxisAlignment.center,
              crossAxisAlignment: CrossAxisAlignment.end,
              children: [
                Text(_formatCurrency(item['total_amount']),
                    style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 13)),
                Text(status.toUpperCase(),
                    style: TextStyle(fontSize: 10, color: status == 'verified' ? Colors.green : Colors.orange)),
              ],
            ),
          ),
        );
      },
    );
  }

  String _formatCurrency(dynamic amount) {
    final formatter = NumberFormat.currency(locale: 'id_ID', symbol: 'Rp ', decimalDigits: 0);
    return formatter.format(double.tryParse(amount.toString()) ?? 0);
  }
}
