import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class PrivacyPolicyView extends StatelessWidget {
  const PrivacyPolicyView({super.key});

  // ===== DUMMY (API se aayega baad mein) =====
  static const String _dummyContent = '''
Welcome to Reap433. Your privacy is important to us. This Privacy Policy explains how we collect, use, and protect your information when you use our app.

1. Information We Collect
We may collect information such as your name, email address, and profile picture when you create an account. We also collect usage data to improve your experience.

2. How We Use Your Information
Your information is used to provide and improve our services, track your progress, personalize your experience, and send you relevant notifications.

3. Data Security
We take reasonable measures to protect your personal information from unauthorized access, alteration, or disclosure.

4. Third-Party Services
We may use third-party services such as authentication providers. These services have their own privacy policies.

5. Changes to This Policy
We may update this Privacy Policy from time to time. Any changes will be reflected on this page.

6. Contact Us
If you have any questions about this Privacy Policy, please contact our support team.
''';

  @override
  Widget build(BuildContext context) {
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
                        text: 'privacy_policy'.tr,
                        fontSize: 40,
                        weight: FontWeight.w600,
                        color: Colors.white,
                        textAlign: TextAlign.center,
                        letterSpacing: -1,
                      ),
                    ),
                  ],
                ),
              ),

              const SizedBox(height: 5),

              Center(
                child: CustomText(
                  text: 'privacy_policy_sub'.tr,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ),

              const SizedBox(height: 40),

              /// CONTENT (scrollable)
              Expanded(
                child: SingleChildScrollView(
                  child: CustomText(
                    text: _dummyContent,
                    fontSize: 15,
                    weight: FontWeight.w400,
                    color: ColorConstants.textColor,
                    lineSpacing: 1.5,
                  ),
                ),
              ),

              const SizedBox(height: 10),
            ],
          ),
        ),
      ),
    );
  }
}
