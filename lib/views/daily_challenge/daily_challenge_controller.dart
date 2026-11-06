import 'dart:async';
import 'package:get/get.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/quiz/quiz_start/quiz_start_view.dart';

class DailyChallengeController extends GetxController {
  // dashboard se aaye data
  final int categoryId;
  final int quizTypeId;
  final int xp;
  final int coins;
  final String resetTime; // "23:45:45"

  DailyChallengeController({
    required this.categoryId,
    required this.quizTypeId,
    required this.xp,
    required this.coins,
    required this.resetTime,
  });

  final rewardXp = 0.obs;
  final rewardCoins = 0.obs;
  final resetInSeconds = 0.obs;

  Timer? _timer;

  @override
  void onInit() {
    super.onInit();
    rewardXp.value = xp;
    rewardCoins.value = coins;
    resetInSeconds.value = _parseTime(resetTime); // string → seconds
    _startCountdown();
  }

  /// "23:45:45" → total seconds
  int _parseTime(String t) {
    try {
      final parts = t.split(':');
      final h = int.parse(parts[0]);
      final m = int.parse(parts[1]);
      final s = int.parse(parts[2]);
      return h * 3600 + m * 60 + s;
    } catch (_) {
      return 0;
    }
  }

  void _startCountdown() {
    _timer?.cancel();
    _timer = Timer.periodic(const Duration(seconds: 1), (_) {
      if (resetInSeconds.value > 0) {
        resetInSeconds.value--;
      } else {
        _timer?.cancel();
      }
    });
  }

  /// "12:45:30" format (live)
  String get resetTimeFormatted {
    final s = resetInSeconds.value;
    final h = (s ~/ 3600).toString().padLeft(2, '0');
    final m = ((s % 3600) ~/ 60).toString().padLeft(2, '0');
    final sec = (s % 60).toString().padLeft(2, '0');
    return '$h:$m:$sec';
  }

  /// START — daily challenge ki category/type se quiz shuru
  Future<void> startChallenge() async {
    final quizC = Get.find<QuizController>();
    // daily challenge flag on
    quizC.isDailyChallenge.value = true;
    // category/type set (daily challenge se aaye)
    quizC.setDailyChallenge(categoryId, quizTypeId);
    await quizC.startQuiz();
    Get.to(() => const QuizStartView());
  }

  @override
  void onClose() {
    _timer?.cancel();
    super.onClose();
  }
}