import 'package:flutter/material.dart';
import 'screens/login_screen.dart';

void main() {
  runApp(SiakadApp());
}

class SiakadApp extends StatelessWidget {
  @override
  Widget build(BuildContext context) {
    return MaterialApp(
      title: 'Siakad Mobile Native',
      debugShowCheckedModeBanner: false,
      theme: ThemeData(
        primarySwatch: Colors.blue,
        useMaterial3: true,
      ),
      home: LoginScreen(),
    );
  }
}
