import 'package:flutter/material.dart';

class AppConstants {
  static String appName = "Reap433";
  static String appVersion = "1.0.0";
  static String baseUrl = "https://reap433.sitestaginglink.com/api/";

  // auth
  static String signIn = "sign-in";
  static String signUp = "sign-up";
  static String socialLogin = "social-login";
  static String forgotPassword = "forgot-password";
  static String forgotPasswordReset = "forgot-password/reset";
  static String logout = "logout";

  // quiz
  static String categories = "categories";
  static String quizTypes = "quiz-types";
  static String quizStart = "quiz/start";
  static String quizAnswer = "quiz/answer";
  static String quizComplete = "quiz/complete";
  
  static String continueQuiz = "categories/continue";

  // profile
  static String profile = "get-profile";
  static String updateProfile = "update-profile";
  static String changePassword = "change-password";

  // progress
  static String progress = "progress";

  // achievements
  static String achievements = "achievements";
  static String achievementsClaim = "achievements/claim";

  // ranking
  static String userRanking = "user-ranking";
  
}

class ColorConstants {
  static Color gradientColor1 = Color(0xFFEDC57C);
  static Color gradientColor2 = Color(0xFFAC823B);
  static Color textfieldFillColor = Color(0xFF0A0A09);
  static Color textfieldBorderColor = Color(0xffECC47C);
  static Color textColor = Color(0xFF856E55);
  static Color blackColor = Color(0xff0A0A09);
  static Color greyTextColor = Color(0xff43464C);
  static Color creamColor = Color(0xffF2E1C7);
  static Color borderColor = Color(0xffECC47C);
}

class ScreenConstants {
  static double verticalPadding = 15;
  static double horizontalPadding = 10;
}
