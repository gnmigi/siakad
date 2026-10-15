import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class TranskripScreen extends StatefulWidget {
  const TranskripScreen({super.key});

  @override
  _TranskripScreenState createState() => _TranskripScreenState();
}

class _TranskripScreenState extends State<TranskripScreen> {
  bool _isLoading = true;
  Map<String, dynamic>? _transkripData;
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _fetchTranskrip();
  }

  Future<void> _fetchTranskrip() async {
    final result = await _apiService.getTranskrip();
    if (result['status'] == 'success') {
      setState(() {
        _transkripData = result['data'];
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal memuat data Transkrip')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Transkrip Nilai", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.orange.shade700,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                _buildSummaryCard(),
                const Padding(
                  padding: EdgeInsets.all(16.0),
                  child: Row(
                    children: [
                      Icon(Icons.list_alt, color: Colors.grey),
                      SizedBox(width: 8),
                      Text("Rekapitulasi Nilai", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
                    ],
                  ),
                ),
                Expanded(child: _buildTranskripList()),
              ],
            ),
    );
  }

  Widget _buildSummaryCard() {
    final cgpa = _transkripData?['cumulative_gpa'] ?? 0.0;
    final grades = _transkripData?['grades'] as List? ?? [];
    int totalSks = 0;
    for (var g in grades) {
      totalSks += (g['mata_kuliah']?['bsks'] as int? ?? 0);
    }

    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceAround,
        children: [
          _buildSummaryItem("IPK", cgpa.toStringAsFixed(2), Colors.orange.shade700),
          Container(width: 1, height: 40, color: Colors.grey.shade200),
          _buildSummaryItem("Total SKS", totalSks.toString(), Colors.blue.shade700),
          Container(width: 1, height: 40, color: Colors.grey.shade200),
          _buildSummaryItem("Mata Kuliah", grades.length.toString(), Colors.green.shade700),
        ],
      ),
    );
  }

  Widget _buildSummaryItem(String label, String value, Color color) {
    return Column(
      children: [
        Text(value, style: TextStyle(fontSize: 20, fontWeight: FontWeight.bold, color: color)),
        const SizedBox(height: 4),
        Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
      ],
    );
  }

  Widget _buildTranskripList() {
    final grades = _transkripData?['grades'] as List? ?? [];
    if (grades.isEmpty) {
      return const Center(child: Text("Belum ada data transkrip", style: TextStyle(color: Colors.grey)));
    }

    return ListView.separated(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      itemCount: grades.length,
      separatorBuilder: (context, index) => const Divider(height: 1),
      itemBuilder: (context, index) {
        final item = grades[index];
        final mk = item['mata_kuliah'];
        return Padding(
          padding: const EdgeInsets.symmetric(vertical: 12),
          child: Row(
            children: [
              Expanded(
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    Text(mk['name'] ?? '-', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
                    const SizedBox(height: 4),
                    Text("Kode: ${mk['code'] ?? '-'} | SKS: ${mk['bsks'] ?? 0}",
                        style: const TextStyle(fontSize: 12, color: Colors.grey)),
                  ],
                ),
              ),
              Container(
                width: 40,
                height: 40,
                alignment: Alignment.center,
                decoration: BoxDecoration(color: Colors.orange.shade50, shape: BoxShape.circle),
                child: Text(
                  _getLetterGrade(item['nilai_akhir']),
                  style: TextStyle(color: Colors.orange.shade800, fontWeight: FontWeight.bold),
                ),
              ),
            ],
          ),
        );
      },
    );
  }

  String _getLetterGrade(dynamic score) {
    double s = double.tryParse(score.toString()) ?? 0.0;
    if (s >= 85) return 'A';
    if (s >= 80) return 'A-';
    if (s >= 75) return 'B+';
    if (s >= 70) return 'B';
    if (s >= 65) return 'B-';
    if (s >= 60) return 'C+';
    if (s >= 55) return 'C';
    if (s >= 50) return 'C-';
    if (s >= 45) return 'D+';
    if (s >= 40) return 'D';
    return 'E';
  }
}
