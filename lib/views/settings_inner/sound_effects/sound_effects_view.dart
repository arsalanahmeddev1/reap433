import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/services/sound_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class SoundEffectsView extends StatelessWidget {
  const SoundEffectsView({super.key});

  static const Color _iconBg = Color(0xFF211B14);

  @override
  Widget build(BuildContext context) {
    final sound = SoundService.to;

    return Scaffold(
      body: SafeArea(
        child: Padding(
          padding: EdgeInsets.symmetric(
            horizontal: ScreenConstants.horizontalPadding,
            vertical: ScreenConstants.verticalPadding,
          ),
          child: Column(
            crossAxisAlignment: CrossAxisAlignment.start,
            children: [
              /// TOP BAR
              SizedBox(
                width: double.infinity,
                height: 48,
                child: Stack(
                  alignment: Alignment.center,
                  children: [
                    Align(
                      alignment: Alignment.centerLeft,
                      child: GestureDetector(
                        onTap: () => Get.back(),
                        child: SvgPicture.asset('assets/svg/back_arrow.svg'),
                      ),
                    ),
                    GradientText(
                      child: CustomText2(
                        text: 'sound_effects'.tr,
                        fontSize: 40,
                        weight: FontWeight.w600,
                        color: Colors.white,
                        letterSpacing: -1,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 5),

              Center(
                child: CustomText(
                  text: 'sound_effects_desc'.tr,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ),

              const SizedBox(height: 40),

              /// TOGGLE CARD
              Container(
                padding: const EdgeInsets.symmetric(
                  horizontal: 16,
                  vertical: 18,
                ),
                decoration: BoxDecoration(
                  borderRadius: BorderRadius.circular(12),
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
                child: Row(
                  children: [
                    /// icon
                    Container(
                      width: 34,
                      height: 34,
                      decoration: const BoxDecoration(
                        shape: BoxShape.circle,
                        color: Color(0xff211B14),
                      ),
                      child: Center(
                        child: SvgPicture.asset(
                          'assets/svg/sound.svg',
                          height: 15,
                          color: ColorConstants.gradientColor1,
                        ),
                      ),
                    ),
                    const SizedBox(width: 14),

                    /// label + on/off status
                    Expanded(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        mainAxisSize: MainAxisSize.min,
                        children: [
                          CustomText(
                            text: 'enable_sound'.tr,
                            fontSize: 16,
                            weight: FontWeight.w400,
                            color: Colors.white,
                          ),
                          const SizedBox(height: 2),
                          Obx(
                            () => CustomText(
                              text: sound.soundOn.value
                                  ? 'sound_on'.tr
                                  : 'sound_off'.tr,
                              fontSize: 14,
                              weight: FontWeight.w400,
                              color: ColorConstants.textColor,
                            ),
                          ),
                        ],
                      ),
                    ),

                    /// switch
                    Obx(
                      () => Switch(
                        value: sound.soundOn.value,
                        onChanged: (v) => sound.toggle(v), // 👈 service se save
                        activeThumbColor: ColorConstants.gradientColor1,
                        activeTrackColor: ColorConstants.gradientColor2,
                        inactiveThumbColor: Colors.grey,
                        inactiveTrackColor: _iconBg,
                      ),
                    ),
                  ],
                ),
              ),
            ],
          ),
        ),
      ),
    );
  }
}
