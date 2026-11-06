import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_controller.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/gradient_text.dart';

class BottomNavigation extends StatefulWidget {
  final int? index;
  final bool? isGuestMode;
  const BottomNavigation({super.key, this.index, this.isGuestMode});

  @override
  State<BottomNavigation> createState() => _BottomNavigationState();
}

class _BottomNavigationState extends State<BottomNavigation> {
  final BottomNavbarController controller = Get.find<BottomNavbarController>();

  @override
  void initState() {
    super.initState();
    WidgetsBinding.instance.addPostFrameCallback((_) {
      if (widget.index != null) {
        controller.changeIndex(widget.index!);
      }
    });
  }

  @override
  Widget build(BuildContext context) {
    return GetBuilder<BottomNavbarController>(
      id: 'main',
      builder: (c) => PopScope(
        canPop: false,
        onPopInvoked: (didPop) {
          if (c.currentIndex.value == 0) {
            Get.dialog(
              Center(
                child: Container(
                  width: 300,
                  padding: const EdgeInsets.symmetric(
                    horizontal: 20,
                    vertical: 20,
                  ),
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    color: const Color(0xff202020),
                    boxShadow: [
                      BoxShadow(
                        color: Colors.black.withValues(alpha: 0.2),
                        blurRadius: 10,
                        offset: const Offset(0, 4),
                      ),
                    ],
                  ),
                  child: Column(
                    mainAxisSize: MainAxisSize.min,
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SvgPicture.asset(
                            'assets/svg/exit.svg',
                            color: ColorConstants.gradientColor1,
                          ),
                          const SizedBox(width: 10),
                          CustomText(
                            text: 'Exit ',
                            fontSize: 24,
                            weight: FontWeight.w500,
                            color: Colors.white,
                          ),
                        ],
                      ),
                      const SizedBox(height: 10),
                      CustomText(
                        text: 'Are you sure you want to exit?',
                        weight: FontWeight.w500,
                        fontSize: 16,
                        color: const Color(0xff6C6D6E),
                        textAlign: TextAlign.center,
                      ),
                      const SizedBox(height: 30),
                      Row(
                        mainAxisAlignment: MainAxisAlignment.spaceEvenly,
                        children: [
                          GestureDetector(
                            onTap: () => Get.back(),
                            child: Container(
                              width: 110,
                              height: 28,
                              decoration: BoxDecoration(
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(
                                  color: const Color(0xffFFFF00),
                                ),
                              ),
                              child: Center(
                                child: CustomText(
                                  text: 'Cancel',
                                  color: const Color(0xffFFFF00),
                                  fontSize: 12,
                                  weight: FontWeight.w500,
                                ),
                              ),
                            ),
                          ),
                          GestureDetector(
                            onTap: SystemNavigator.pop,
                            child: Container(
                              width: 110,
                              height: 28,
                              decoration: BoxDecoration(
                                color: const Color(0xffFFFF00),
                                borderRadius: BorderRadius.circular(6),
                                border: Border.all(
                                  color: const Color(0xffFFFF00),
                                ),
                              ),
                              child: Center(
                                child: CustomText(
                                  text: 'Yes',
                                  color: Colors.black,
                                  fontSize: 12,
                                  weight: FontWeight.w500,
                                ),
                              ),
                            ),
                          ),
                        ],
                      ),
                    ],
                  ),
                ),
              ),
            );
          } else {
            c.changeIndex(0);
          }
        },
        child: Scaffold(
          floatingActionButtonLocation:
              FloatingActionButtonLocation.centerDocked,

          /// CENTER FAB — gold gradient + book icon + "Quiz" text
          floatingActionButton: SizedBox(
            width: 82,
            height: 82,
            child: FloatingActionButton(
              backgroundColor: Colors.transparent,
              elevation: 0,
              highlightElevation: 0,
              shape: const CircleBorder(),
              onPressed: () => controller.changeIndex(2),
              child: Container(
                width: 72,
                height: 72,
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
                child: Column(
                  mainAxisSize: MainAxisSize.min,
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    SvgPicture.asset(
                      controller.listOfScreens[2].icon,
                      color: const Color(0xff333333),
                      height: 19,
                      width: 21,
                    ),
                    CustomText(
                      text: 'nav_quiz'.tr,
                      fontSize: 12,
                      weight: FontWeight.w600,
                      color: const Color(0xff333333),
                    ),
                  ],
                ),
              ),
            ),
          ),

          body: Obx(() {
            final index = controller.currentIndex.value;
            return controller.listOfScreens[index].widget;
          }),

          /// BOTTOM BAR — solid bg + notch + labels
          bottomNavigationBar: BottomAppBar(
            color: const Color(0xff13110D),
            shape: const CircularNotchedRectangle(),
            notchMargin: 8,
            height: 68,
            padding: EdgeInsets.zero,
            child: Row(
              mainAxisSize: MainAxisSize.max,
              children: controller.listOfScreens.asMap().entries.map((entry) {
                final screen = entry.value;

                /// center (Quiz) — FAB ke liye gap
                if (screen.index == 2) {
                  return const Expanded(child: SizedBox());
                }

                final bool isSelected =
                    controller.currentIndex.value == screen.index;

                return Expanded(
                  child: InkWell(
                    onTap: () => controller.changeIndex(screen.index),
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        /// GLOW behind selected item
                        if (isSelected)
                          Container(
                            width: 110,
                            height: 110,
                            decoration: BoxDecoration(
                              gradient: RadialGradient(
                                colors: [
                                  const Color(0xFFFFDB78).withValues(alpha: 0.3),
                                  const Color(0xFF94601F).withValues(alpha: 0.0),
                                ],
                                stops: const [0.0,  1.0],
                              ),
                            ),
                          ),

                        /// icon + label
                        Column(
                          mainAxisSize: MainAxisSize.min,
                          mainAxisAlignment: MainAxisAlignment.center,
                          children: [
                            SvgPicture.asset(
                              isSelected ? screen.fillIcon : screen.icon,
                              height: 16.66,
                              width: 18.4,
                            ),
                            const SizedBox(height: 2),
                            isSelected
                                ? GradientText(
                                    child: CustomText(
                                      text: screen.title.tr,
                                      fontSize: 12,
                                      weight: FontWeight.w400,
                                    ),
                                  )
                                : CustomText(
                                    text: screen.title.tr,
                                    fontSize: 12,
                                    weight: FontWeight.w400,
                                    color: const Color(0xFF474037),
                                  ),
                          ],
                        ),
                      ],
                    ),
                  ),
                );
              }).toList(),
            ),
          ),
        ),
      ),
    );
  }
}
