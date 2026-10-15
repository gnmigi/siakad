import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../login_screen.dart';
import 'krs_screen.dart';
import 'khs_screen.dart';
import 'transkrip_screen.dart';
import 'tagihan_screen.dart';

class MahasiswaHomeScreen extends StatefulWidget {
  final Map<String, dynamic> data;
  const MahasiswaHomeScreen({super.key, required this.data});

  @override
  _MahasiswaHomeScreenState createState() => _MahasiswaHomeScreenState();
}

class _MahasiswaHomeScreenState extends State<MahasiswaHomeScreen> {
  late Map<String, dynamic> _data;

  @override
  void initState() {
    super.initState();
    _data = widget.data;
  }

  Future<void> _refreshData() async {
    final result = await ApiService().getDashboard();
    if (result['status'] == 'success') {
      setState(() => _data = result['data']);
    }
  }

  @override
  Widget build(BuildContext context) {
    final userInfo = _data['user_info'];
    final stats = _data['stats'];
    final dosen = _data['dosen_penasehat'];

    return Scaffold(
      backgroundColor: const Color(0xFFF3F4F6),
      body: RefreshIndicator(
        onRefresh: _refreshData,
        child: CustomScrollView(
          slivers: [
            _buildSliverAppBar(),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildWelcomeCard(userInfo),
                    const SizedBox(height: 20),
                    _buildStatsGrid(stats),
                    const SizedBox(height: 20),
                    _buildSectionTitle("Informasi Akademik"),
                    _buildAcademicInfo(userInfo),
                    const SizedBox(height: 20),
                    _buildSectionTitle("Menu Utama"),
                    _buildMenuGrid(),
                    const SizedBox(height: 20),
                    _buildSectionTitle("Dosen Penasehat"),
                    _buildDosenCard(dosen),
                    const SizedBox(height: 30),
                  ],
                ),
              ),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSliverAppBar() {
    return SliverAppBar(
      expandedHeight: 120.0,
      pinned: true,
      backgroundColor: const Color(0xFF1E3A8A),
      flexibleSpace: FlexibleSpaceBar(
        title: const Text("Siakad Mobile", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18)),
        background: Container(
          decoration: const BoxDecoration(
            gradient: LinearGradient(
              colors: [Color(0xFF1E3A8A), Color(0xFF3B82F6)],
              begin: Alignment.topLeft,
              end: Alignment.bottomRight,
            ),
            borderRadius: BorderRadius.only(bottomLeft: Radius.circular(30), bottomRight: Radius.circular(30)),
          ),
        ),
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.logout_rounded, color: Colors.white),
          onPressed: _handleLogout,
        ),
      ],
    );
  }

  Widget _buildWelcomeCard(Map<String, dynamic> user) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(20),
        boxShadow: [BoxShadow(color: Colors.black.withOpacity(0.05), blurRadius: 10)],
      ),
      child: Row(
        children: [
          CircleAvatar(
            radius: 30,
            backgroundColor: Colors.blue.shade50,
            backgroundImage: user['photo'] != null ? NetworkImage(user['photo']) : null,
            child: user['photo'] == null ? const Icon(Icons.person, color: Color(0xFF1E3A8A), size: 30) : null,
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text("Selamat Datang,", style: TextStyle(color: Colors.grey.shade600, fontSize: 14)),
                Text(user['name'] ?? 'Mahasantri',
                    style: const TextStyle(fontSize: 18, fontWeight: FontWeight.bold, color: Color(0xFF1E293B))),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsGrid(Map<String, dynamic> stats) {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 2,
      crossAxisSpacing: 12,
      mainAxisSpacing: 12,
      childAspectRatio: 2.2,
      children: [
        _buildStatItem("Semester", stats['semester_aktif']?.toString() ?? '0', Colors.blue),
        _buildStatItem("Tagihan", stats['tagihan_pending'] ?? 'Rp 0', Colors.red),
        _buildStatItem("Tugas", stats['tugas_pending']?.toString() ?? '0', Colors.orange),
        _buildStatItem("Kehadiran", stats['kehadiran'] ?? '0%', Colors.green),
      ],
    );
  }

  Widget _buildStatItem(String label, String value, Color color) {
    return Container(
      padding: const EdgeInsets.all(12),
      decoration: BoxDecoration(
        color: Colors.white,
        borderRadius: BorderRadius.circular(15),
        border: Border(left: BorderSide(color: color, width: 4)),
      ),
      child: Column(
        crossAxisAlignment: CrossAxisAlignment.start,
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Text(label, style: const TextStyle(fontSize: 12, color: Colors.grey)),
          Text(value, style: TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: color)),
        ],
      ),
    );
  }

  Widget _buildAcademicInfo(Map<String, dynamic> info) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: Column(
        children: [
          _buildInfoRow("NIM", info['nim'] ?? '-'),
          const Divider(),
          _buildInfoRow("Program Studi", info['prodi'] ?? '-'),
          const Divider(),
          _buildInfoRow("Kelas", info['kelas'] ?? '-'),
          const Divider(),
          _buildInfoRow("Status", info['status'] ?? '-'),
        ],
      ),
    );
  }

  Widget _buildInfoRow(String label, String value) {
    return Padding(
      padding: const EdgeInsets.symmetric(vertical: 4),
      child: Row(
        mainAxisAlignment: MainAxisAlignment.spaceBetween,
        children: [
          Text(label, style: const TextStyle(color: Colors.grey)),
          Text(value, style: const TextStyle(fontWeight: FontWeight.w600, fontSize: 13)),
        ],
      ),
    );
  }

  Widget _buildMenuGrid() {
    return GridView.count(
      shrinkWrap: true,
      physics: const NeverScrollableScrollPhysics(),
      crossAxisCount: 4,
      mainAxisSpacing: 20,
      children: [
        _buildMenuItem(Icons.edit_document, "KRS", Colors.blue, () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const KrsScreen()));
        }),
        _buildMenuItem(Icons.assignment_turned_in, "KHS", Colors.green, () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const KhsScreen()));
        }),
        _buildMenuItem(Icons.description, "Transkrip", Colors.orange, () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const TranskripScreen()));
        }),
        _buildMenuItem(Icons.account_balance_wallet, "Tagihan", Colors.red, () {
          Navigator.push(context, MaterialPageRoute(builder: (context) => const TagihanScreen()));
        }),
      ],
    );
  }

  Widget _buildMenuItem(IconData icon, String label, Color color, VoidCallback onTap) {
    return InkWell(
      onTap: onTap,
      borderRadius: BorderRadius.circular(15),
      child: Column(
        children: [
          Container(
            padding: const EdgeInsets.all(12),
            decoration: BoxDecoration(color: color.withOpacity(0.1), shape: BoxShape.circle),
            child: Icon(icon, color: color, size: 28),
          ),
          const SizedBox(height: 8),
          Text(label, style: const TextStyle(fontSize: 11, fontWeight: FontWeight.w500)),
        ],
      ),
    );
  }

  Widget _buildDosenCard(Map<String, dynamic> dosen) {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(color: Colors.white, borderRadius: BorderRadius.circular(20)),
      child: Row(
        children: [
          CircleAvatar(
            backgroundColor: Colors.grey.shade200,
            backgroundImage: dosen['photo'] != null ? NetworkImage(dosen['photo']) : null,
            child: dosen['photo'] == null ? const Icon(Icons.person, color: Colors.grey) : null,
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Text(dosen['name'] ?? 'Belum ada Dosen Penasehat', style: const TextStyle(fontWeight: FontWeight.bold)),
                Text(dosen['email'] ?? '-', style: const TextStyle(color: Colors.grey, fontSize: 12)),
              ],
            ),
          ),
          const Icon(Icons.chevron_right, color: Colors.grey),
        ],
      ),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 12),
      child: Text(title, style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Color(0xFF1E293B))),
    );
  }

  void _handleLogout() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        shape: RoundedRectangleBorder(borderRadius: BorderRadius.circular(20)),
        title: const Text("Keluar?"),
        content: const Text("Anda perlu login kembali untuk mengakses data."),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              await ApiService().logout();
              Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
            },
            child: const Text("Ya, Keluar", style: TextStyle(color: Colors.white)),
          )
        ],
      ),
    );
  }
}
