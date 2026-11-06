import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/onboarding/onboarding_controller.dart';
import 'package:reap433/widgets/common_button.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';
import 'package:reap433/widgets/shine_image.dart';

class OnBoardingView extends StatelessWidget {
  const OnBoardingView({super.key});

  static const Color _gold = Color(0xFFB98E4C);

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(OnBoardingController());
    final h = MediaQuery.of(context).size.height;

    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Column(
          children: [
            const SizedBox(height: 5),

            /// SKIP (top right)
            Align(
              alignment: Alignment.centerRight,
              child: Padding(
                padding: const EdgeInsets.only(right: 15, top: 8),
                child: GestureDetector(
                  onTap: controller.skip,
                  child: GradientText(
                    child: CustomText(
                      text: 'skip'.tr,
                      fontSize: 16,
                      weight: FontWeight.w500,
                    ),
                  ),
                ),
              ),
            ),

            /// SLIDES (swipeable)
            Expanded(
              child: PageView.builder(
                controller: controller.pageController,
                onPageChanged: controller.onPageChanged,
                itemCount: controller.titleKeys.length,
                itemBuilder: (_, i) => _buildSlide(context, controller, i, h),
              ),
            ),

            /// DOTS
            Obx(
              () => Row(
                mainAxisAlignment: MainAxisAlignment.center,
                children: List.generate(controller.titleKeys.length, (i) {
                  final active = controller.currentIndex.value == i;
                  return AnimatedContainer(
                    duration: const Duration(milliseconds: 250),
                    margin: const EdgeInsets.symmetric(horizontal: 7),
                    width: 8,
                    height: 8,
                    decoration: BoxDecoration(
                      shape: BoxShape.circle,
                      gradient: active ? GradientText.goldGradient : null,
                      border: active
                          ? null
                          : Border.all(
                              color: _gold.withValues(alpha: 0.8),
                              width: 1.2,
                            ),
                    ),
                  );
                }),
              ),
            ),

            /// BUTTON
            Padding(
              padding: const EdgeInsets.only(
                left: 15,
                right: 15,
                bottom: 10,
                top: 30,
              ),
              child: Obx(() {
                final isLast =
                    controller.currentIndex.value ==
                    controller.titleKeys.length - 1;
                return CommonButton(
                  title: (isLast ? 'get_started' : 'next').tr,
                  onPressed: controller.next,
                  textColor: Colors.black,
                  trailingIcon: isLast
                      ? null
                      : SvgPicture.asset('assets/svg/forward_arrow.svg'),
                );
              }),
            ),
          ],
        ),
      ),
    );
  }

  Widget _buildSlide(
    BuildContext context,
    OnBoardingController controller,
    int i,
    double h,
  ) {
    return Column(
      mainAxisAlignment: MainAxisAlignment.center,
      children: [
        SizedBox(
          height: h * 0.4,
          child: Stack(
            alignment: Alignment.center,
            children: [
              Image.asset(
                'assets/png/bg_glow.png',
                height: h * 0.4,
                fit: BoxFit.contain,
              ),
              ShineImage(
                image: controller.images[i],
                height: h * 0.35,
                fit: BoxFit.contain,
              ),
            ],
          ),
        ),

        const SizedBox(height: 25),

        GradientText(
          child: CustomText2(
            text: controller.labelKeys[i].tr,
            fontSize: 26,
            weight: FontWeight.w400,
            textAlign: TextAlign.center,
            letterSpacing: 7,
            lineSpacing: 1,
          ),
        ),

        CustomText2(
          text: controller.titleKeys[i].tr,
          fontSize: 50,
          weight: FontWeight.w600,
          color: ColorConstants.creamColor,
          textAlign: TextAlign.center,
          letterSpacing: -3,
        ),

        const SizedBox(height: 5),

        Padding(
          padding: const EdgeInsets.symmetric(horizontal: 32),
          child: CustomText(
            text: controller.descKeys[i].tr,
            fontSize: 16,
            weight: FontWeight.w500,
            color: ColorConstants.greyTextColor,
            textAlign: TextAlign.center,
            letterSpacing: 0,
          ),
        ),
      ],
    );
  }
}
