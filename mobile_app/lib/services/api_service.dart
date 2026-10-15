import 'dart:convert';
import 'package:http/http.dart' as http;
import 'package:shared_preferences/shared_preferences.dart';

class ApiService {
  // Ganti dengan IP Local kamu jika menggunakan HP fisik (misal: 192.168.1.5)
  // Atau 10.0.2.2 jika menggunakan Emulator Android
  static const String baseUrl = "http://192.168.1.3:8000/api/v1";
  static const String rootUrl = "http://192.168.1.3:8000/api";

  Future<Map<String, dynamic>> getAppSettings() async {
    try {
      final response = await http.get(
        Uri.parse("$rootUrl/settings"),
        headers: {'Accept': 'application/json'},
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil pengaturan: $e'};
    }
  }

  Future<Map<String, dynamic>> login(String login, String password) async {
    try {
      print("Mencoba login ke: $baseUrl/login"); // Debug log
      final response = await http.post(
        Uri.parse("$baseUrl/login"),
        headers: {
          'Accept': 'application/json',
        },
        body: {
          'login': login,
          'password': password,
        },
      ).timeout(const Duration(seconds: 10));

      print("Status Response: ${response.statusCode}");
      print("Headers: ${response.headers}");

      if (response.headers['content-type']?.contains('application/json') ?? false) {
        final data = jsonDecode(response.body);
        if (response.statusCode == 200) {
          final prefs = await SharedPreferences.getInstance();
          if (data['data'] != null && data['data']['token'] != null) {
            await prefs.setString('token', data['data']['token']);
            await prefs.setString('role', data['data']['user']['role']);
          }
        }
        return data;
      } else {
        return {
          'status': 'error',
          'message': 'Server redirect/Error (Status: ${response.statusCode}). Cek URL Base API kembali.'
        };
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Masalah Koneksi: $e'};
    }
  }

  Future<Map<String, dynamic>> getProfile() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final role = prefs.getString('role');

      String path = (role == 'mahasiswa') ? "/mahasiswa/profile" : "/dosen/profile";

      final response = await http.get(
        Uri.parse("$baseUrl$path"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      if (response.statusCode == 200) {
        return jsonDecode(response.body);
      } else {
        return {'status': 'error', 'message': 'Gagal mengambil profil (${response.statusCode})'};
      }
    } catch (e) {
      return {'status': 'error', 'message': 'Kesalahan: $e'};
    }
  }

  Future<Map<String, dynamic>> getDashboard() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      final role = prefs.getString('role');

      String path = (role == 'mahasiswa') ? "/mahasiswa/dashboard" : "/dosen/dashboard";

      final response = await http.get(
        Uri.parse("$baseUrl$path"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil data dashboard: $e'};
    }
  }

  // --- ACADEMIC RECORDS ---

  Future<Map<String, dynamic>> getKrs({int? semester}) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      String url = "$baseUrl/mahasiswa/krs";
      if (semester != null) url += "?semester=$semester";

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil KRS: $e'};
    }
  }

  Future<Map<String, dynamic>> saveKrs(List<int> courseIds, int semester) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.post(
        Uri.parse("$baseUrl/mahasiswa/krs"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: jsonEncode({
          'mata_kuliah_ids': courseIds,
          'semester': semester,
        }),
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal menyimpan KRS: $e'};
    }
  }

  Future<Map<String, dynamic>> getKhs({int? semester}) async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');
      String url = "$baseUrl/mahasiswa/khs";
      if (semester != null) url += "?semester=$semester";

      final response = await http.get(
        Uri.parse(url),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil KHS: $e'};
    }
  }

  Future<Map<String, dynamic>> getTranskrip() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse("$baseUrl/mahasiswa/transkrip"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil transkrip: $e'};
    }
  }

  // --- FINANCIALS ---

  Future<Map<String, dynamic>> getTagihan() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse("$baseUrl/mahasiswa/tagihan"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil tagihan: $e'};
    }
  }

  Future<Map<String, dynamic>> getPaymentHistory() async {
    try {
      final prefs = await SharedPreferences.getInstance();
      final token = prefs.getString('token');

      final response = await http.get(
        Uri.parse("$baseUrl/mahasiswa/tagihan/history"),
        headers: {
          'Authorization': 'Bearer $token',
          'Accept': 'application/json',
        },
      ).timeout(const Duration(seconds: 10));

      return jsonDecode(response.body);
    } catch (e) {
      return {'status': 'error', 'message': 'Gagal mengambil riwayat pembayaran: $e'};
    }
  }

  Future<void> logout() async {
    final prefs = await SharedPreferences.getInstance();
    final token = prefs.getString('token');

    await http.post(
      Uri.parse("$baseUrl/logout"),
      headers: {
        'Authorization': 'Bearer $token',
        'Accept': 'application/json',
      },
    );
    await prefs.clear();
  }
}
