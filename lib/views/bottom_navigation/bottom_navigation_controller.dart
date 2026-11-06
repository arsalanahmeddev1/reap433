import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/views/dashboard/dashboard_view.dart';
import 'package:reap433/views/my_progress/my_progress_view.dart';
import 'package:reap433/views/profile/profile_view.dart';
import 'package:reap433/views/quiz/categories/deck_categories_view.dart';
import 'package:reap433/views/settings/settings_view.dart';

class BottomNavbarController extends GetxController {
  static BottomNavbarController get to => Get.find<BottomNavbarController>();
  var currentIndex = 0.obs;
  var isMenuOpen = false.obs;
  var isAnimating = false.obs;
  RxBool isLoading = false.obs;

  static const String dailyReminderKey = "daily_reminder_last_shown";

  final List<NavScreen> listOfScreens = [
    NavScreen(index: 0, widget: const DashboardView(), title: 'nav_home', icon: 'assets/svg/bottom_nav_1.svg', fillIcon: 'assets/svg/bottom_nav_1_fill.svg', width: 24, height: 24),
    NavScreen(index: 1, widget: const MyProgressView(), title: 'nav_progress', icon: 'assets/svg/bottom_nav_2.svg', fillIcon: 'assets/svg/bottom_nav_2_fill.svg', width: 24, height: 24),
    NavScreen(index: 2, widget: const DeckCategoriesView(), title: 'nav_quiz', icon: 'assets/svg/bottom_nav_3.svg', fillIcon: 'assets/svg/bottom_nav_3.svg', width: 24, height: 24),
    NavScreen(index: 3, widget: const ProfileView(), title: 'nav_profile', icon: 'assets/svg/bottom_nav_4.svg', fillIcon: 'assets/svg/bottom_nav_4_fill.svg', width: 24, height: 24),
    NavScreen(index: 4, widget: const SettingsView(), title: 'nav_setting', icon: 'assets/svg/bottom_nav_5.svg', fillIcon: 'assets/svg/bottom_nav_5_fill.svg', width: 24, height: 24),
  ];

  void changeIndex(int index) {
    if (currentIndex.value == index) return;
    currentIndex.value = index;
    update(['main']);
  }
}

class NavScreen {
  final int index;
  final Widget widget;
  final String icon;
  final String fillIcon;
  final String title;
  final double width;
  final double height;

  NavScreen({
    required this.index,
    required this.widget,
    required this.icon,
    required this.fillIcon,
    required this.title,
    required this.width,
    required this.height,
  });
}