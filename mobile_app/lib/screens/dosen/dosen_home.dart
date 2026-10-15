import 'package:flutter/material.dart';
import '../../services/api_service.dart';
import '../login_screen.dart';

class DosenHomeScreen extends StatefulWidget {
  final Map<String, dynamic> data;
  const DosenHomeScreen({super.key, required this.data});

  @override
  _DosenHomeScreenState createState() => _DosenHomeScreenState();
}

class _DosenHomeScreenState extends State<DosenHomeScreen> {
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

    return Scaffold(
      backgroundColor: const Color(0xFF064E3B), // Dark Green Background
      body: RefreshIndicator(
        onRefresh: _refreshData,
        child: CustomScrollView(
          slivers: [
            _buildSliverAppBar(userInfo),
            SliverToBoxAdapter(
              child: Padding(
                padding: const EdgeInsets.all(16.0),
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    _buildWelcomeCard(userInfo),
                    const SizedBox(height: 20),
                    _buildStatsGrid(stats),
                    const SizedBox(height: 25),
                    _buildSectionTitle("Aksi Cepat"),
                    _buildMenuGrid(),
                    const SizedBox(height: 25),
                    _buildSectionTitle("Jadwal Mengajar Hari Ini"),
                    _buildEmptyState("Tidak ada jadwal mengajar hari ini."),
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

  Widget _buildSliverAppBar(Map<String, dynamic> user) {
    return SliverAppBar(
      expandedHeight: 100.0,
      pinned: true,
      backgroundColor: const Color(0xFF064E3B),
      flexibleSpace: FlexibleSpaceBar(
        title: const Text("Dashboard Ustadz", style: TextStyle(fontWeight: FontWeight.bold, fontSize: 18, color: Color(0xFF10B981))),
        centerTitle: false,
        titlePadding: const EdgeInsets.only(left: 20, bottom: 16),
      ),
      actions: [
        IconButton(
          icon: const Icon(Icons.logout_rounded, color: Colors.white70),
          onPressed: _handleLogout,
        ),
      ],
    );
  }

  Widget _buildWelcomeCard(Map<String, dynamic> user) {
    return Container(
      padding: const EdgeInsets.all(20),
      decoration: BoxDecoration(
        color: const Color(0xFF10B981), // Green Accent
        borderRadius: BorderRadius.circular(20),
      ),
      child: Row(
        children: [
          Container(
            width: 60,
            height: 60,
            decoration: BoxDecoration(
              color: Colors.black26,
              borderRadius: BorderRadius.circular(10),
              image: user['photo'] != null ? DecorationImage(image: NetworkImage(user['photo']), fit: BoxFit.cover) : null,
            ),
            child: user['photo'] == null ? const Icon(Icons.person, color: Colors.white, size: 40) : null,
          ),
          const SizedBox(width: 15),
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const Text("بِسْمِ اللَّهِ الرَّحْمَنِ الرَّحِيمِ", style: TextStyle(color: Colors.white, fontSize: 12, fontWeight: FontWeight.w500)),
                const SizedBox(height: 4),
                Text(user['name'] ?? 'Ustadz', style: const TextStyle(fontSize: 16, fontWeight: FontWeight.bold, color: Colors.white)),
                const Text("Semoga Allah memberikan keberkahan.", style: TextStyle(color: Colors.white70, fontSize: 11)),
              ],
            ),
          ),
        ],
      ),
    );
  }

  Widget _buildStatsGrid(Map<String, dynamic> stats) {
    return Column(
      children: [
        Row(
          children: [
            _buildStatCard("MATA KULIAH DIAMPU", stats['total_mata_kuliah']?.toString() ?? '0', Icons.menu_book, Colors.green),
            const SizedBox(width: 12),
            _buildStatCard("TOTAL MAHASANTRI", stats['total_mahasiswa']?.toString() ?? '0', Icons.people_alt, Colors.blue),
          ],
        ),
        const SizedBox(height: 12),
        Row(
          children: [
            _buildStatCard("NILAI PENDING", stats['tugas_pending']?.toString() ?? '0', Icons.assignment_late, Colors.orange),
            const SizedBox(width: 12),
            _buildStatCard("JADWAL HARI INI", stats['pertemuan_hari_ini']?.toString() ?? '0', Icons.calendar_today, Colors.teal),
          ],
        ),
      ],
    );
  }

  Widget _buildStatCard(String title, String value, IconData icon, Color color) {
    return Expanded(
      child: Container(
        padding: const EdgeInsets.all(16),
        decoration: BoxDecoration(
          color: const Color(0xFF1E293B).withOpacity(0.5),
          borderRadius: BorderRadius.circular(15),
          border: Border.all(color: Colors.white10),
        ),
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Icon(icon, color: Colors.white70, size: 20),
            const SizedBox(height: 12),
            Text(value, style: const TextStyle(fontSize: 24, fontWeight: FontWeight.bold, color: Colors.white)),
            Text(title, style: const TextStyle(fontSize: 10, color: Colors.white54, fontWeight: FontWeight.w600)),
          ],
        ),
      ),
    );
  }

  Widget _buildMenuGrid() {
    return Container(
      padding: const EdgeInsets.all(16),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B).withOpacity(0.5),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Wrap(
        spacing: 15,
        runSpacing: 15,
        children: [
          _buildActionChip("Edit Profile", Colors.blue),
          _buildActionChip("Absensi", Colors.green),
          _buildActionChip("Mata Kuliah", Colors.teal),
          _buildActionChip("Input Nilai", Colors.orange),
          _buildActionChip("Rekap Nilai", Colors.purple),
        ],
      ),
    );
  }

  Widget _buildActionChip(String label, Color color) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 8),
      decoration: BoxDecoration(
        color: color.withOpacity(0.2),
        borderRadius: BorderRadius.circular(10),
        border: Border.all(color: color.withOpacity(0.5)),
      ),
      child: Text(label, style: TextStyle(color: color, fontSize: 12, fontWeight: FontWeight.bold)),
    );
  }

  Widget _buildSectionTitle(String title) {
    return Padding(
      padding: const EdgeInsets.only(bottom: 15),
      child: Row(
        children: [
          const Icon(Icons.settings, color: Colors.white54, size: 18),
          const SizedBox(width: 8),
          Text(title, style: const TextStyle(fontSize: 14, fontWeight: FontWeight.bold, color: Colors.white70)),
        ],
      ),
    );
  }

  Widget _buildEmptyState(String message) {
    return Container(
      width: double.infinity,
      padding: const EdgeInsets.all(30),
      decoration: BoxDecoration(
        color: const Color(0xFF1E293B).withOpacity(0.3),
        borderRadius: BorderRadius.circular(20),
      ),
      child: Text(message, textAlign: TextAlign.center, style: const TextStyle(color: Colors.white38)),
    );
  }

  void _handleLogout() {
    showDialog(
      context: context,
      builder: (context) => AlertDialog(
        backgroundColor: const Color(0xFF1E293B),
        title: const Text("Keluar?", style: TextStyle(color: Colors.white)),
        content: const Text("Anda perlu login kembali.", style: TextStyle(color: Colors.white70)),
        actions: [
          TextButton(onPressed: () => Navigator.pop(context), child: const Text("Batal")),
          ElevatedButton(
            style: ElevatedButton.styleFrom(backgroundColor: Colors.red),
            onPressed: () async {
              await ApiService().logout();
              Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
            },
            child: const Text("Ya, Keluar"),
          )
        ],
      ),
    );
  }
}
