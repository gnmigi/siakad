import 'package:flutter/material.dart';
import '../../services/api_service.dart';

class KrsScreen extends StatefulWidget {
  const KrsScreen({super.key});

  @override
  _KrsScreenState createState() => _KrsScreenState();
}

class _KrsScreenState extends State<KrsScreen> {
  bool _isLoading = true;
  Map<String, dynamic>? _krsData;
  int _selectedSemester = 1;
  final ApiService _apiService = ApiService();

  @override
  void initState() {
    super.initState();
    _fetchKrs();
  }

  Future<void> _fetchKrs({int? semester}) async {
    setState(() => _isLoading = true);
    final result = await _apiService.getKrs(semester: semester);
    if (result['status'] == 'success') {
      setState(() {
        _krsData = result['data'];
        _selectedSemester = _krsData?['selected_semester'] ?? 1;
        _isLoading = false;
      });
    } else {
      setState(() => _isLoading = false);
      ScaffoldMessenger.of(context).showSnackBar(
        SnackBar(content: Text(result['message'] ?? 'Gagal memuat data KRS')),
      );
    }
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text("Kartu Rencana Studi (KRS)", style: TextStyle(fontSize: 18, fontWeight: FontWeight.bold)),
        backgroundColor: const Color(0xFF1E3A8A),
        foregroundColor: Colors.white,
        actions: [
          IconButton(
            icon: const Icon(Icons.refresh),
            onPressed: () => _fetchKrs(semester: _selectedSemester),
          )
        ],
      ),
      body: _isLoading
          ? const Center(child: CircularProgressIndicator())
          : Column(
              children: [
                _buildSemesterPicker(),
                Expanded(child: _buildKrsContent()),
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
              if (val != null) _fetchKrs(semester: val);
            },
          ),
        ],
      ),
    );
  }

  Widget _buildKrsContent() {
    final krs = _krsData?['current_krs'];
    if (krs == null) {
      return Center(
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            const Icon(Icons.info_outline, size: 64, color: Colors.grey),
            const SizedBox(height: 16),
            const Text("KRS belum diisi untuk semester ini", style: TextStyle(color: Colors.grey)),
            const SizedBox(height: 24),
            ElevatedButton(
              onPressed: () {
                // Future: Implement course selection UI
              },
              style: ElevatedButton.styleFrom(backgroundColor: const Color(0xFF1E3A8A)),
              child: const Text("Isi KRS Sekarang", style: TextStyle(color: Colors.white)),
            ),
          ],
        ),
      );
    }

    final details = krs['details'] as List;
    return ListView(
      padding: const EdgeInsets.all(16),
      children: [
        _buildInfoCard(krs),
        const SizedBox(height: 16),
        const Text("Daftar Mata Kuliah", style: TextStyle(fontSize: 16, fontWeight: FontWeight.bold)),
        const SizedBox(height: 8),
        ...details.map((d) => _buildCourseItem(d)).toList(),
        const SizedBox(height: 20),
      ],
    );
  }

  Widget _buildInfoCard(Map<String, dynamic> krs) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: Colors.blue.shade50,
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: Colors.blue.shade200),
      ),
      child: Column(
        children: [
          _buildInfoRow("Tahun Akademik", krs['tahun_akademik']?['name'] ?? '-'),
          _buildInfoRow("Total SKS", (krs['total_sks'] ?? 0).toString()),
          _buildInfoRow("Status", (krs['status'] ?? 'Draft').toUpperCase(), isStatus: true),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value, {bool isStatus = false}) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.black54)),
          Text(
            value,
            style: TextStyle(
              fontWeight: FontWeight.bold,
              color: isStatus ? (value == 'APPROVED' ? Colors.green : Colors.orange) : Colors.black87,
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildCourseItem(Map<String, dynamic> detail) {
    final mk = detail['mata_kuliah'];
    return Card(
      margin: const EdgeInsets.only(bottom: 12),
      shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(10)),
      child: ListTile(
        title: Text(mk['name'] ?? '-', style: const TextStyle(fontWeight: FontWeight.bold, fontSize: 14)),
        subtitle: Text("Dosen: ${mk['dosen1']?['name'] ?? '-'}\nSKS: ${detail['sks'] ?? 0}"),
        trailing: const Icon(Icons.check_circle, color: Colors.green, size: 20),
        isThreeLine: true,
      ),
    );
  }
}
