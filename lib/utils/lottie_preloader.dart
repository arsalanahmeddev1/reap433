import 'package:flutter/services.dart';
import 'package:flutter/widgets.dart';
import 'package:lottie/lottie.dart';

class LottiePreloader {
  static const List<String> _assets = [
    'assets/lotties/lottie_1.json',
    'assets/lotties/lottie_2.json',
    'assets/lotties/lottie_3.json',
    'assets/lotties/lottie_4.json',
    'assets/lotties/lottie_5.json',
  ];

  static bool _done = false;

  /// ek-ek karke background mein load — UI block nahi hoga
  static Future<void> preloadAll(BuildContext context) async {
    if (_done) return;
    _done = true;

    for (final path in _assets) {
      try {
        // AssetLottie load — memory cache mein aa jayegi
        await AssetLottie(path).load();
        // har file ke baad chhota gap — UI ko saans lene do
        await Future.delayed(const Duration(milliseconds: 100));
      } catch (_) {
        // koi file fail ho to skip
      }
    }
  }
}