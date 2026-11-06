import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/services/fontsize_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/views/settings/setting_controller.dart';
import 'package:reap433/views/settings_inner/about_reap_threads/about_reap_threads_view.dart';
import 'package:reap433/views/settings_inner/fontsize/fontsize_view.dart';
import 'package:reap433/views/settings_inner/help_support/help_support_view.dart';
import 'package:reap433/views/settings_inner/language/language_view.dart';
import 'package:reap433/views/settings_inner/notification/notification_view.dart';
import 'package:reap433/views/settings_inner/privacy_policy/privacy_policy_view.dart';
import 'package:reap433/views/settings_inner/ranking/ranking_view.dart';
import 'package:reap433/views/settings_inner/sound_effects/sound_effects_view.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class SettingsView extends StatelessWidget {
  const SettingsView({super.key});

  static final RxBool soundOn = true.obs;

  @override
  Widget build(BuildContext context) {
    final controller = Get.put(SettingController());

    // har baar screen aane pe preferences refresh
    WidgetsBinding.instance.addPostFrameCallback((_) {
      controller.loadPreferences();
    });

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: SingleChildScrollView(
            child: Column(
              children: [
                GradientText(
                  child: CustomText2(
                    text: 'setting'.tr,
                    fontSize: 40,
                    weight: FontWeight.w600,
                    color: Colors.white,
                    letterSpacing: -1,
                  ),
                ),

                const SizedBox(height: 20),

                /// MAIN GROUP CARD
                Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: ColorConstants.borderColor,
                      width: 0.5,
                    ),
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                    ),
                  ),
                  child: Column(
                    children: [
                      _item(
                        icon: 'assets/svg/ranking.svg',
                        title: 'ranking'.tr,
                        subtitle: 'ranking_sub'.tr,
                        onTap: () => Get.to(RankingView()),
                      ),
                      _line(),
                      _item(
                        icon: 'assets/svg/gradient_bell.svg',
                        title: 'notifications'.tr,
                        subtitle: 'notifications_sub'.tr,
                        onTap: () => Get.to(NotificationView()),
                      ),
                      _line(),

                      /// LANGUAGE — dynamic (jo select ki)
                      Obx(
                        () => _item(
                          icon: 'assets/svg/language.svg',
                          title: 'language'.tr,
                          subtitle: 'language_sub'.tr,
                          trailingText: controller.currentLanguage.value,
                          onTap: () async {
                            await Get.to(() => LanguageView());
                            controller
                                .loadPreferences(); // wapas aane pe refresh
                          },
                        ),
                      ),
                      _line(),

                      /// FONT SIZE — dynamic (jo select ki)
                      Obx(
                        () => _item(
                          icon: 'assets/svg/fontsize.svg',
                          title: 'font_size'.tr,
                          subtitle: 'font_size_sub'.tr,
                          trailingText: FontSizeService.to.currentKey.tr,
                          onTap: () async {
                            await Get.to(() => FontSizeView());
                            controller.loadPreferences();
                          },
                        ),
                      ),
                      _line(),

                      _item(
                        icon: 'assets/svg/sound.svg',
                        title: 'sound_effects'.tr,
                        subtitle: 'sound_effects_sub'.tr,
                        onTap: () => Get.to(SoundEffectsView()),
                      ),
                      _line(),
                      _item(
                        icon: 'assets/svg/privacy_policy.svg',
                        title: 'privacy_policy'.tr,
                        subtitle: 'privacy_policy_sub'.tr,
                        onTap: () => Get.to(PrivacyPolicyView()),
                      ),
                      _line(),
                      _item(
                        icon: 'assets/svg/support.svg',
                        title: 'help_support'.tr,
                        subtitle: 'help_support_sub'.tr,
                        onTap: () => Get.to(HelpSupportView()),
                      ),
                      _line(),
                      _item(
                        icon: 'assets/svg/reap.svg',
                        title: 'about_app'.tr,
                        subtitle: 'about_app_sub'.tr,
                        onTap: () => Get.to(AboutReapThreadsView()),
                      ),
                    ],
                  ),
                ),

                const SizedBox(height: 12),

                /// LOGOUT — API call
                Container(
                  decoration: BoxDecoration(
                    borderRadius: BorderRadius.circular(16),
                    border: Border.all(
                      color: ColorConstants.borderColor,
                      width: 0.5,
                    ),
                    gradient: const LinearGradient(
                      begin: Alignment.topLeft,
                      end: Alignment.bottomRight,
                      colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
                    ),
                  ),
                  child: Obx(
                    () => _item(
                      icon: 'assets/svg/logout.svg',
                      title: 'logout'.tr,
                      subtitle: 'logout_sub'.tr,
                      titleColor: ColorConstants.gradientColor1,
                      iconColor: ColorConstants.gradientColor1,
                      trailingText: controller.isLoggingOut.value
                          ? '...'
                          : null,
                      onTap: () {
                        if (!controller.isLoggingOut.value) {
                          controller.logout(context);
                        }
                      },
                    ),
                  ),
                ),

                const SizedBox(height: 20),
              ],
            ),
          ),
        ),
      ),
    );
  }

  Widget _line() => Container(
    height: 0.5,
    color: ColorConstants.borderColor,
    margin: const EdgeInsets.symmetric(horizontal: 14),
  );

  Widget _item({
    required String icon,
    required String title,
    required String subtitle,
    VoidCallback? onTap,
    String? trailingText,
    Color? titleColor,
    Color? iconColor,
  }) {
    return GestureDetector(
      onTap: onTap,
      behavior: HitTestBehavior.opaque,
      child: Padding(
        padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
        child: Row(
          children: [
            Container(
              width: 34,
              height: 34,
              decoration: const BoxDecoration(
                shape: BoxShape.circle,
                color: Color(0xff211B14),
              ),
              child: Center(child: SvgPicture.asset(icon, height: 16)),
            ),
            const SizedBox(width: 14),
            Expanded(
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                mainAxisSize: MainAxisSize.min,
                children: [
                  CustomText(
                    text: title,
                    fontSize: 16,
                    weight: FontWeight.w500,
                    color: titleColor ?? Colors.white,
                  ),
                  CustomText(
                    text: subtitle,
                    fontSize: 12,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                  ),
                ],
              ),
            ),
            if (trailingText != null) ...[
              GradientText(
                child: CustomText(
                  text: trailingText,
                  fontSize: 12,
                  weight: FontWeight.w400,
                ),
              ),
              const SizedBox(width: 10),
            ],
            SvgPicture.asset('assets/svg/deck_arrow.svg', height: 10),
          ],
        ),
      ),
    );
  }
}

Widget _line() => Container(
  height: 0.5,
  color: ColorConstants.borderColor,
  margin: const EdgeInsets.symmetric(horizontal: 14),
);

Widget _item({
  required String icon,
  required String title,
  required String subtitle,
  VoidCallback? onTap,
  String? trailingText,
  Color? titleColor,
  Color? iconColor,
}) {
  return GestureDetector(
    onTap: onTap,
    behavior: HitTestBehavior.opaque,
    child: Padding(
      padding: const EdgeInsets.symmetric(horizontal: 14, vertical: 14),
      child: Row(
        children: [
          /// icon circle
          Container(
            width: 34,
            height: 34,
            decoration: const BoxDecoration(
              shape: BoxShape.circle,
              color: Color(0xff211B14),
            ),
            child: Center(child: SvgPicture.asset(icon, height: 16)),
          ),
          const SizedBox(width: 14),

          /// title + subtitle
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomText(
                  text: title,
                  fontSize: 16,
                  weight: FontWeight.w500,
                  color: titleColor ?? Colors.white,
                ),
                CustomText(
                  text: subtitle,
                  fontSize: 12,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ],
            ),
          ),
          if (trailingText != null) ...[
            GradientText(
              child: CustomText(
                text: trailingText,
                fontSize: 12,
                weight: FontWeight.w400,
              ),
            ),
            const SizedBox(width: 10),
          ],
          SvgPicture.asset('assets/svg/deck_arrow.svg', height: 10),
        ],
      ),
    ),
  );
}
