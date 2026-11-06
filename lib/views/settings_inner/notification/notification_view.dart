import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/settings_inner/notification/notification_controller.dart';
import 'package:reap433/views/settings_inner/notification/widgets/notification_skeleton_loading.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/gradient_text.dart';

class NotificationView extends StatelessWidget {
  const NotificationView({super.key});

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(NotificationController());

    return Scaffold(
      backgroundColor: Colors.black,
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Obx(() {
            if (controller.isLoading.value) {
              return const NotificationSkeletonLoading();
            }
            return Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Row(
                  children: [
                    GestureDetector(
                      onTap: () => Get.back(),
                      child: SvgPicture.asset('assets/svg/back_arrow.svg'),
                    ),
                    const SizedBox(width: 12),
                    Expanded(
                      child: Obx(
                        () => Row(
                          children: [
                            _tab(controller, 0, 'all'.tr),
                            const SizedBox(width: 5),
                            _tab(controller, 1, 'unread'.tr),
                            const SizedBox(width: 5),
                            Spacer(),
                            GestureDetector(
                              onTap: controller.markAllAsRead,
                              child: Container(
                                padding: const EdgeInsets.symmetric(
                                  horizontal: 16,
                                  vertical: 8,
                                ),
                                decoration: BoxDecoration(
                                  gradient: LinearGradient(
                                    begin: Alignment.topLeft,
                                    end: Alignment.bottomRight,
                                    colors: [
                                      Color(0xFF0A0A09),
                                      Color(0xFF221D15),
                                    ],
                                  ),
                                  borderRadius: BorderRadius.circular(10),
                                  border: Border.all(
                                    color: ColorConstants.borderColor,
                                    width: 0.5,
                                  ),
                                ),
                                child: CustomText(
                                  text: 'mark_as_read'.tr,
                                  fontSize: 14,
                                  weight: FontWeight.w500,
                                  color: ColorConstants.gradientColor1,
                                ),
                              ),
                            ),
                          ],
                        ),
                      ),
                    ),
                  ],
                ),

                const SizedBox(height: 22),

                /// LIST
                Expanded(
                  child: Obx(() {
                    if (controller.filtered.isNotEmpty) {
                      return Center(
                        child: CustomText(text: 'No Notification Found.', color: ColorConstants.textColor),
                      );
                    }
                    final list = controller.filtered;
                    return ListView.separated(
                      itemCount: list.length,
                      separatorBuilder: (_, _) => const SizedBox(height: 16),
                      itemBuilder: (_, i) => _card(controller, list[i]),
                    );
                  }),
                ),
              ],
            );
          }),
        ),
      ),
    );
  }

  /// TAB (All / Unread)
  Widget _tab(NotificationController c, int index, String label) {
    final selected = c.selectedTab.value == index;
    return GestureDetector(
      onTap: () => c.selectTab(index),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 25, vertical: 8),
        decoration: BoxDecoration(
          borderRadius: BorderRadius.circular(10),
          gradient: selected
              ? GradientText.goldGradient
              : LinearGradient(
                  begin: Alignment.topLeft,
                  end: Alignment.bottomRight,
                  colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                ),
          border: selected
              ? null
              : Border.all(color: ColorConstants.borderColor, width: 0.5),
        ),
        child: CustomText(
          text: label,
          fontSize: 14,
          weight: FontWeight.w400,
          color: selected ? Colors.black : ColorConstants.gradientColor1,
        ),
      ),
    );
  }

  /// notification card
  Widget _card(NotificationController c, AppNotification n) {
    return GestureDetector(
      onTap: () => c.openNotification(n),
      child: Container(
        padding: const EdgeInsets.symmetric(horizontal: 16, vertical: 12),
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
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Padding(
              padding: const EdgeInsets.only(top: 6),
              child: Obx(
                () => Container(
                  width: 9,
                  height: 9,
                  decoration: BoxDecoration(
                    shape: BoxShape.circle,
                    gradient: n.isRead.value ? null : GradientText.goldGradient,
                  ),
                ),
              ),
            ),
            const SizedBox(width: 12),

            /// title + body
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  CustomText(
                    text: n.title,
                    fontSize: 16,
                    weight: FontWeight.w500,
                    color: Colors.white,
                  ),
                  const SizedBox(height: 6),
                  CustomText(
                    text: n.body,
                    fontSize: 12,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                    lineSpacing: 1.35,
                  ),
                ],
              ),
            ),
            const SizedBox(width: 12),
            CustomText(
              text: n.time,
              fontSize: 14,
              weight: FontWeight.w400,
              color: Color(0xff514C47),
            ),
          ],
        ),
      ),
    );
  }
}
