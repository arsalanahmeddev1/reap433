import 'package:get/get.dart';

class AppNotification {
  final String id;
  final String title;
  final String body;
  final String time;
  final RxBool isRead;

  AppNotification({
    required this.id,
    required this.title,
    required this.body,
    required this.time,
    required bool read,
  }) : isRead = read.obs;
}

class NotificationController extends GetxController {
  // 0 = All, 1 = Unread
  var selectedTab = 0.obs;
  RxBool isLoading = false.obs;
  void selectTab(int i) => selectedTab.value = i;

  // ===== DUMMY (GET /api/notifications se aayega) =====
  final all = <AppNotification>[
    AppNotification(id: 'n1', title: 'Quiz Completed!', body: 'Great job! You scored 9/12 in "The Cross" and earned 150 XP and 75 Coins.', time: '09:32', read: false),
    AppNotification(id: 'n2', title: '7 Day Streak!', body: 'Amazing! You maintained your 7 day learning streak.', time: '08:40', read: false),
    AppNotification(id: 'n3', title: 'Daily Verse', body: 'Your daily verse is ready. Tap to read and be inspired.', time: '10:56', read: false),
    AppNotification(id: 'n4', title: 'Achievement Unlocked!', body: 'You unlocked "Dedicated Learner". Keep up the great work!', time: '03:52', read: true),
    AppNotification(id: 'n5', title: 'Level Up!', body: 'Congratulations! You reached Level 12. Keep learning and growing.', time: '06:12', read: true),
    AppNotification(id: 'n6', title: 'Weekly Challenge Reward', body: "You completed this week's challenge. Here is your reward!", time: '09:32', read: false),
  ].obs;
  // ===================================================

  List<AppNotification> get filtered {
    if (selectedTab.value == 1) {
      return all.where((n) => !n.isRead.value).toList();
    }
    return all;
  }

  /// MARK ALL AS READ — real: POST /api/notifications/mark-read
  void markAllAsRead() {
    for (final n in all) {
      n.isRead.value = true;
    }
    // API call
  }

  /// ek notification tap → read + uski screen kholo (deep link)
  void openNotification(AppNotification n) {
    n.isRead.value = true;
    // type ke hisaab se navigate (deep link)
  }
}