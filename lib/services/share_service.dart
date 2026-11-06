import 'package:share_plus/share_plus.dart';

class ShareService {
  // Store links (download next update pe live hojayegi)
  static const String iosLink =
      'https://apps.apple.com/us/app/reap433/id6827326898';
  static const String androidLink =
      'https://play.google.com/store/apps/details?id=com.reap433.app';

  /// Quiz result share — score/accuracy/rewards ke saath
  static Future<void> shareQuizResult({
    required int score,
    required int total,
    required int accuracy,
    required int xp,
    required int coins,
  }) async {
    final text =
        '''🎉 I just scored $score/$total on Reap433 — the Bible Trivia Challenge! 📖✨

🎯 Accuracy: $accuracy%
⚡ XP Earned: $xp
🪙 Coins: +$coins

Think you know the Bible better than me? Take the challenge and find out! 🙌

📲 Download Reap433:
🍎 iPhone: $iosLink
🤖 Android: $androidLink

#Reap433 #BibleTrivia #FaithAndFun''';

    await _share(text, subject: 'My Reap433 Quiz Result 🏆');
  }

  /// General app share — plain "share the app" button ke liye
  static Future<void> shareApp() async {
    final text =
        '''📖 Discover Reap433 — the fun way to test and grow your Bible knowledge! ✨

Play exciting trivia, earn XP & coins, keep your daily streak alive, and challenge your friends. 🙌

📲 Download now:
🍎 iPhone: $iosLink
🤖 Android: $androidLink

#Reap433 #BibleTrivia #FaithAndFun''';

    await _share(text, subject: 'Check out Reap433 📖');
  }

  static Future<void> _share(String text, {String? subject}) async {
    await SharePlus.instance.share(
      ShareParams(text: text, subject: subject),
    );
  }
}