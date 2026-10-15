import 'package:flutter/material.dart';
import '../services/api_service.dart';
import 'mahasiswa/mahasiswa_home.dart';
import 'dosen/dosen_home.dart';
import 'login_screen.dart';
import 'package:shared_preferences/shared_preferences.dart';

class HomeScreen extends StatefulWidget {
  const HomeScreen({super.key});

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> {
  Map<String, dynamic>? _dashboardData;
  String? _role;
  bool _isLoading = true;

  @override
  void initState() {
    super.initState();
    _checkAuthAndLoad();
  }

  void _checkAuthAndLoad() async {
    final prefs = await SharedPreferences.getInstance();
    _role = prefs.getString('role');

    final result = await ApiService().getDashboard();
    if (result['status'] == 'success') {
      setState(() {
        _dashboardData = result['data'];
        _isLoading = false;
      });
    } else {
      // Jika token expired atau error lainnya, balik ke login
      if (mounted) {
        Navigator.pushReplacement(context, MaterialPageRoute(builder: (context) => const LoginScreen()));
      }
    }
  }

  @override
  Widget build(BuildContext context) {
    if (_isLoading) {
      return const Scaffold(
        body: Center(child: CircularProgressIndicator()),
      );
    }

    // Tampilkan Dashboard sesuai Role yang terdeteksi
    if (_role == 'mahasiswa') {
      return MahasiswaHomeScreen(data: _dashboardData!);
    } else if (_role == 'dosen') {
      return DosenHomeScreen(data: _dashboardData!);
    }

    return const LoginScreen();
  }
}
