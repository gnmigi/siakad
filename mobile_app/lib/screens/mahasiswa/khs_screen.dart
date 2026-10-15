import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class KhsScreen extends StatefulWidget {
  const KhsScreen({super.key});

  @override
  _KhsScreenState createState() => _KhsScreenState();
}

class _KhsScreenState extends State<KhsScreen> {
  bool _isLoading = true;
  Map<String, dynamic>? _khsData;
  int _selectedSemester = 1;
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _fetchKhs();
  }

  Future<void> _fetchKhs({int? semester}) async {
    setState(() => _isLoading = true);
    final result = await _apiService.getKhs(semester: semester);
    if (result['status'] == 'success') {
      setState(() {
        _khsData = result['data'];
        _selectedSemester = _khsData?['selected_semester'] ?? 1;
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal memuat data KHS')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Kartu Hasil Studi (KHS)", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        backgroundColor: Colors.green.shade700,
        foregroundColor: Colors.white,
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                _buildSemesterPicker(),
                _buildGpaCard(),
                Expanded(child: _buildKhsList()),
              ],
            ),
    );
  }

  Widget _buildSemesterPicker() {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
      color: Colors.white,
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Text("Pilih Semester:", style: TextStyle(fontWeight: FontWeight.w600)),
          DropdownButton<int>(
            value: _selectedSemester,
            items: List.generate(8, (index) => index + 1)
                .map((s) => DropdownMenuItem(value: s, child: Text("Semester $s")))
                .toList(),
            onChanged: (val) {
              if (val != null) _fetchKhs(semester: val);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildGpaCard() {
    final gpa = _khsData?['semester_gpa'] ?? 0.0;
    return Container(
      margin: const EdgeInsets.all(16),
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        gradient: LinearGradient(colors: [Colors.green.shade600, Colors.green.shade400]),
        borderRadius: BorderRadius.circular(15),
        boxShadow: [BoxShadow(color: Colors.green.withOpacity(0.3), blurRadius: 10, offset: const Offset(0, 4))],
      ),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          const Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              Text("IP Semester Ini", style: TextStyle(color: Colors.white, fontSize: 14)),
              SizedBox(height: 4),
              Text("Good Job!", style: TextStyle(color: Colors.white70, fontSize: 12)),
            ],
          ),
          Text(
            gpa.toStringAsFixed(2),
            style: const TextStyle(color: Colors.white, fontSize: 32, fontWeight: FontWeight.bold),
          ),
        ],
      ),
    );
  }

  Widget _buildKhsList() {
    final grades = _khsData?['grades'] as List? ?? [];
    if (grades.isEmpty) {
      return const Center(child: Text("Belum ada nilai untuk semester ini", style: TextStyle(color: Colors.grey)));
    }

    return ListView.builder(
      padding: const EdgeInsets.symmetric(horizontal: 16),
      itemCount: grades.length,
      itemBuilder: (context, index) {
        final item = grades[index];
        final mk = item['mata_kuliah'];
        return Card(
          margin: const EdgeInsets.only(bottom: 12),
          shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
          child: ListTile(
            title: Text(mk['name'] ?? '-', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
            subtitle: Text("SKS: ${mk['bsks'] ?? 0} | Nilai: ${item['nilai_akhir'] ?? 0}"),
            trailing: Container(
              padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 6),
              decoration: BoxDecoration(color: Colors.green.shade50, borderRadius: BorderRadius.circular(20)),
              child: Text(
                _getLetterGrade(item['nilai_akhir']),
                style: TextStyle(color: Colors.green.shade700, fontWeight: FontWeight.bold),
              ),
            ),
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
