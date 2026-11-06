import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/my_progress/my_progress_controller.dart';
import 'package:reap433/views/my_progress/widgets/gradient_circular_progress_widget.dart';
import 'package:reap433/views/my_progress/widgets/myprogress_skeleton_loading.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class MyProgressView extends StatelessWidget {
  const MyProgressView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(MyProgressController());
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.fetchProgress();
    });

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Obx(() {
            if (controller.isLoading.value) {
              return ProgressSkeletonLoading();
            }

            final data = controller.progress.value;

            final overallPercent = data?.overallProgressPercent ?? 0;

            final answered = data?.questionsAnswered?.answered ?? 0;

            final totalQuestions = data?.questionsAnswered?.total ?? 0;

            final decksCompleted = data?.decksComplete?.completed ?? 0;

            final totalDecks = data?.decksComplete?.total ?? 0;

            final averageScore = data?.averageScorePercent ?? 0;

            return SingleChildScrollView(
              child: Column(
                children: [
                  /// TITLE
                  GradientText(
                    child: CustomText2(
                      text: 'my_progress'.tr,
                      fontSize: 40,
                      letterSpacing: -1,
                      weight: FontWeight.w600,
                      color: Colors.white,
                    ),
                  ),

                  const SizedBox(height: 30),

                  /// CIRCULAR PROGRESS
                  GradientCircularProgress(
                    value: overallPercent / 100,
                    size: 245,
                    strokeWidth: 20,
                    child: Column(
                      mainAxisSize: MainAxisSize.min,
                      children: [
                        GradientText(
                          child: CustomText2(
                            text: '$overallPercent%',
                            fontSize: 75,
                            weight: FontWeight.w500,
                            color: Colors.white,
                            letterSpacing: -1,
                            lineSpacing: 1,
                          ),
                        ),
                        CustomText(
                          text: 'overall_progress'.tr,
                          fontSize: 16,
                          weight: FontWeight.w400,
                          color: Colors.white,
                          lineSpacing: 1,
                        ),
                      ],
                    ),
                  ),

                  const SizedBox(height: 34),

                  /// QUESTIONS ANSWERED
                  _statCard(
                    icon: 'assets/svg/check.svg',
                    title: 'questions_answered'.tr,
                    subtitle: 'keep_going'.tr,
                    value: '$answered',
                    suffix: ' / $totalQuestions',
                  ),

                  const SizedBox(height: 10),

                  /// DECKS COMPLETE
                  _statCard(
                    icon: 'assets/svg/book.svg',
                    title: 'decks_complete'.tr,
                    subtitle: 'great_progress'.tr,
                    value: '$decksCompleted',
                    suffix: ' / $totalDecks',
                  ),

                  const SizedBox(height: 10),

                  /// AVERAGE SCORE
                  _statCard(
                    icon: 'assets/svg/best_score.svg',
                    title: 'average_score'.tr,
                    subtitle: 'doing_awesome'.tr,
                    value: '$averageScore%',
                  ),

                  // const SizedBox(height: 10),

                  // /// STUDY TIME
                  // _statCard(
                  //   icon: 'assets/svg/time.svg',
                  //   title: 'study_time'.tr,
                  //   subtitle: 'time_well_spent'.tr,
                  //   value: studyTime,
                  // ),
                  const SizedBox(height: 20),
                ],
              ),
            );
          }),
        ),
      ),
    );
  }

  Widget _statCard({
    required String icon,
    required String title,
    required String subtitle,
    required String value,
    String? suffix,
  }) {
    return Container(
      padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 15),
      decoration: BoxDecoration(
        borderRadius: BorderRadius.circular(12),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
      ),
      child: Row(
        children: [
          /// ICON CIRCLE
          Container(
            width: 42,
            height: 42,
            decoration: const BoxDecoration(
              shape: BoxShape.circle,
              color: Color(0xff211B14),
            ),
            child: Center(
              child: SvgPicture.asset(
                icon,
                height: 15,
                color: ColorConstants.gradientColor1,
              ),
            ),
          ),

          const SizedBox(width: 14),

          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomText2(
                  text: title,
                  fontSize: 22,
                  weight: FontWeight.w400,
                  color: Colors.white,
                  lineSpacing: 1,
                ),
                CustomText(
                  text: subtitle,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ],
            ),
          ),

          RichText(
            text: TextSpan(
              children: [
                TextSpan(
                  text: value,
                  style: TextStyle(
                    fontSize: 18,
                    fontWeight: FontWeight.w500,
                    color: ColorConstants.gradientColor1,
                    fontFamily: 'Poppins',
                  ),
                ),
                if (suffix != null)
                  TextSpan(
                    text: suffix,
                    style: TextStyle(
                      fontSize: 18,
                      fontWeight: FontWeight.w400,
                      color: ColorConstants.greyTextColor,
                      fontFamily: 'Poppins',
                    ),
                  ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
