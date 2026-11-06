import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_view.dart';
import 'package:reap433/views/daily_challenge/daily_challenge_view.dart';
import 'package:reap433/views/dashboard/dashboard_controller.dart';
import 'package:reap433/views/dashboard/widgets/daily_challenge_card_widget.dart';
import 'package:reap433/views/dashboard/widgets/daily_reset_timer_widget.dart';
import 'package:reap433/views/dashboard/widgets/dashboard_skeleton_loading.dart';
import 'package:reap433/views/dashboard/widgets/deck_row_widget.dart';
import 'package:reap433/views/dashboard/widgets/section_header_widget.dart';
import 'package:reap433/views/quiz/choose_your_level/choose_your_level_view.dart';
import 'package:reap433/views/quiz/quiz_controller.dart';
import 'package:reap433/views/settings_inner/notification/notification_view.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class DashboardView extends StatelessWidget {
  const DashboardView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(DashboardController());

    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.loadDashboard();
    });

    const verseText =
        '"Your word is a lamp to my feet and a light to my path."';
    const verseRef = 'Psalm 119:105';

    return Scaffold(
      body: SafeArea(
        child: Obx(() {
          if (controller.isLoading.value) {
            return const DashboardSkeletonLoading();
          }

          final u = controller.user.value;

          return SingleChildScrollView(
            child: Padding(
              padding: EdgeInsets.symmetric(
                horizontal: ScreenConstants.horizontalPadding,
                vertical: ScreenConstants.verticalPadding,
              ),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  Row(
                    mainAxisAlignment: MainAxisAlignment.end,
                    children: [
                      GestureDetector(
                        onTap: () {
                          Get.to(NotificationView());
                        },
                        child: SvgPicture.asset(
                          'assets/svg/bell.svg',
                          height: 24,
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 20),

                  /// GREETING + STREAK
                  Row(
                    crossAxisAlignment: CrossAxisAlignment.center,
                    children: [
                      Expanded(
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CustomText(
                              text:
                                  controller.greetingKey.tr, // 👈 local time se
                              fontSize: 14,
                              weight: FontWeight.w400,
                              color: ColorConstants.textColor,
                              lineSpacing: 1,
                            ),
                            GradientText(
                              child: CustomText2(
                                text: u?.name ?? '', // 👈 API se name
                                fontSize: 40,
                                weight: FontWeight.w600,
                                color: Colors.white,
                                lineSpacing: 1,
                              ),
                            ),
                          ],
                        ),
                      ),

                      /// STREAK CARD
                      Container(
                        padding: const EdgeInsets.symmetric(
                          horizontal: 14,
                          vertical: 8,
                        ),
                        decoration: BoxDecoration(
                          borderRadius: BorderRadius.circular(8),
                          border: Border.all(color: ColorConstants.borderColor),
                        ),
                        child: Column(
                          children: [
                            Row(
                              mainAxisSize: MainAxisSize.min,
                              children: [
                                SvgPicture.asset(
                                  'assets/svg/streak.svg',
                                  height: 16,
                                ),
                                const SizedBox(width: 12),
                                CustomText(
                                  text:
                                      '${u?.totalStreak ?? 0}', // 👈 API se streak
                                  fontSize: 16,
                                  weight: FontWeight.w700,
                                  color: const Color(0xffF2E1C7),
                                ),
                              ],
                            ),
                            CustomText(
                              text: 'day_streak'.tr,
                              fontSize: 14,
                              weight: FontWeight.w500,
                              color: ColorConstants.textColor,
                              lineSpacing: 1,
                            ),
                          ],
                        ),
                      ),
                    ],
                  ),

                  const SizedBox(height: 22),

                  /// DAILY VERSE CARD (static)
                  Container(
                    height: 170,
                    clipBehavior: Clip.antiAlias,
                    decoration: BoxDecoration(
                      borderRadius: BorderRadius.circular(8),
                      border: Border.all(
                        color: ColorConstants.borderColor,
                        width: 0.5,
                      ),
                      image: const DecorationImage(
                        image: AssetImage('assets/png/daily_verse.png'),
                        fit: BoxFit.cover,
                      ),
                    ),
                    child: Container(
                      padding: const EdgeInsets.symmetric(
                        horizontal: 18,
                        vertical: 15,
                      ),
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          Row(
                            children: [
                              Container(
                                width: 31,
                                height: 31,
                                decoration: BoxDecoration(
                                  shape: BoxShape.circle,
                                  gradient: LinearGradient(
                                    begin: Alignment.topCenter,
                                    end: Alignment.bottomCenter,
                                    colors: [
                                      ColorConstants.gradientColor1,
                                      ColorConstants.gradientColor2,
                                    ],
                                  ),
                                ),
                                child: Center(
                                  child: SvgPicture.asset(
                                    'assets/svg/book.svg',
                                    height: 12,
                                    width: 13,
                                  ),
                                ),
                              ),
                              const SizedBox(width: 10),
                              CustomText(
                                text: 'daily_verse'.tr,
                                fontSize: 14,
                                weight: FontWeight.w700,
                                color: ColorConstants.creamColor,
                              ),
                            ],
                          ),
                          const SizedBox(height: 14),
                          SizedBox(
                            width: 180,
                            child: CustomText(
                              text: verseText,
                              fontSize: 16,
                              weight: FontWeight.w400,
                              color: ColorConstants.creamColor,
                              lineSpacing: 1.25,
                            ),
                          ),
                          const Spacer(),
                          CustomText(
                            text: verseRef,
                            fontSize: 14,
                            weight: FontWeight.w400,
                            color: ColorConstants.textColor,
                          ),
                        ],
                      ),
                    ),
                  ),

                  const SizedBox(height: 16),

                  /// CONTINUE LEARNING (dynamic — empty ho to poora section hide)
                  if (controller.continueQuizzes.isNotEmpty) ...[
                    sectionHeader(
                      'continue_learning'.tr,
                      ontap: () => Get.offAll(BottomNavigation(index: 2)),
                    ),
                    const SizedBox(height: 6),
                    ...controller.continueQuizzes.map((cq) {
                      final total = cq.totalQuestions ?? 0;
                      final progressPercent = cq.progress?.progressPercent ?? 0;
                      return Padding(
                        padding: const EdgeInsets.only(bottom: 10),
                        child: deckRowCard(
                          thumbnail: cq.imageUrl ?? "",
                          title: cq.title ?? '',
                          subtitle: '$total ${'questions'.tr}',
                          progress: progressPercent / 100,
                          ontap: () {
                            final quizC = Get.find<QuizController>();
                            final match = controller.categories
                                .firstWhereOrNull((c) => c.id == cq.id);
                            if (match != null) {
                              quizC.selectCategory(match);
                              Get.to(() => const ChooseYourLevelView());
                            }
                          },
                        ),
                      );
                    }),
                    const SizedBox(height: 16),
                  ],

                  /// CATEGORIES (dynamic — API se)
                  sectionHeader(
                    'categories'.tr,
                    ontap: () => Get.offAll(BottomNavigation(index: 2)),
                  ),
                  const SizedBox(height: 6),
                  SizedBox(
                    height: 92,
                    child: ListView.separated(
                      scrollDirection: Axis.horizontal,
                      itemCount: controller.categories.length,
                      separatorBuilder: (_, __) => const SizedBox(width: 5),
                      itemBuilder: (_, i) {
                        final cat = controller.categories[i];
                        return GestureDetector(
                          onTap: () {
                            final quizC = Get.find<QuizController>();
                            quizC.selectCategory(cat);
                            Get.to(() => const ChooseYourLevelView());
                          },
                          child: Container(
                            width: 78,
                            padding: const EdgeInsets.symmetric(
                              horizontal: 6,
                              vertical: 12,
                            ),
                            decoration: BoxDecoration(
                              color: const Color(0xff181510),
                              borderRadius: BorderRadius.circular(12),
                            ),
                            child: Column(
                              mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                              children: [
                                /// network image (fallback dummy)
                                ClipRRect(
                                  borderRadius: BorderRadius.circular(6),
                                  child: Image.network(
                                    cat.iconImageUrl ?? '',
                                    height: 26,
                                    width: 26,
                                    fit: BoxFit.contain,
                                    errorBuilder: (_, __, ___) => Image.asset(
                                      'assets/png/dummy_deck.png',
                                      height: 26,
                                      width: 26,
                                      fit: BoxFit.contain,
                                    ),
                                  ),
                                ),
                                const SizedBox(height: 8),
                                CustomText(
                                  text: cat.title ?? '',
                                  fontSize: 11,
                                  weight: FontWeight.w400,
                                  color: ColorConstants.creamColor,
                                  textAlign: TextAlign.center,
                                ),
                              ],
                            ),
                          ),
                        );
                      },
                    ),
                  ),

                  const SizedBox(height: 16),

                  /// DAILY CHALLENGE — sirf agar available aur aaj nahi khela
                  /// DAILY CHALLENGE
                  if (u?.dailyChallenge != null) ...[
                    sectionHeader('Daily Challenge'.tr, showViewAll: false),
                    const SizedBox(height: 6),

                    if (u?.dailyChallenge?.isPlayed == false)
                      /// AVAILABLE — khel sakta hai
                      dailyChallengeCard(
                        title: 'Today’s Challenge',
                        dailyChallengeSubText:
                            'Test your Bible knowledge and earn amazing rewards!',
                        ontap: () {
                          final dc = u!.dailyChallenge!;
                          Get.to(
                            () => DailyChallengeView(
                              categoryId: dc.quizCategoryId ?? 0,
                              quizTypeId: dc.quizTypeId ?? 0,
                              xp: dc.totalXp ?? 0,
                              coins: dc.totalCoins ?? 0,
                              resetTime: dc.challengeReset ?? '00:00:00',
                            ),
                          );
                        },
                      )
                    else
                      /// COMPLETED — aaj khel chuka, timer chale
                      _dailyChallengeCompletedCard(
                        u!.dailyChallenge!.challengeReset ?? '00:00:00',
                      ),

                    const SizedBox(height: 30),
                  ],
                  const SizedBox(height: 30),
                ],
              ),
            ),
          );
        }),
      ),
    );
  }

  Widget _dailyChallengeCompletedCard(String resetTime) {
  return Container(
    padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 16),
    decoration: BoxDecoration(
      borderRadius: BorderRadius.circular(14),
      border: Border.all(color: ColorConstants.borderColor, width: 0.5),
      gradient: const LinearGradient(
        begin: Alignment.topLeft,
        end: Alignment.bottomRight,
        colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
      ),
    ),
    child: Row(
      children: [
        /// calendar icon (thoda dim taaki "done" lage)
        Opacity(
          opacity: 0.6,
          child: Image.asset('assets/png/calendar.png', height: 56),
        ),
        const SizedBox(width: 14),

        /// texts
        Expanded(
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            mainAxisSize: MainAxisSize.min,
            children: [
              Row(
                children: [
                  GradientText(
                    child: CustomText(
                      text: 'challenge_completed'.tr, // "Completed"
                      fontSize: 16,
                      weight: FontWeight.w700,
                    ),
                  ),
                  const SizedBox(width: 6),
                  Container(
                    width: 18,
                    height: 18,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: GradientText.goldGradient,
                    ),
                    child: Center(
                      child: SvgPicture.asset(
                        'assets/svg/check.svg',
                        height: 9,
                        color: Colors.black,
                      ),
                    ),
                  ),
                ],
              ),
              const SizedBox(height: 6),
              /// live timer — next challenge kab
              DailyResetTimer(resetTime: resetTime),
            ],
          ),
        ),
      ],
    ),
  );
}
}