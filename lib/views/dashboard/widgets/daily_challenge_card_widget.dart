
import 'package:flutter/material.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';

Widget dailyChallengeCard({
  required String title,
  required String dailyChallengeSubText,
  void Function()? ontap,
}) {
  return GestureDetector(
    onTap: ontap,
    child: Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 16),
      decoration: BoxDecoration(
        gradient: const LinearGradient(
          begin: Alignment.topLeft,
          end: Alignment.bottomRight,
          colors: [Color(0xFF0A0A09), Color(0xFF221D15)],
        ),
        borderRadius: BorderRadius.circular(9),
        border: Border.all(color: ColorConstants.borderColor, width: 0.5),
      ),
      child: Row(
        children: [
          /// thumbnail
          ClipRRect(
            borderRadius: BorderRadius.circular(8),
            child: Image.asset(
              "assets/png/calendar.png",
              width: 66,
              height: 68,
              fit: BoxFit.cover,
            ),
          ),
          const SizedBox(width: 14),
    
          /// texts
          Expanded(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              mainAxisSize: MainAxisSize.min,
              children: [
                CustomText(
                  text: title,
                  fontSize: 16,
                  weight: FontWeight.w700,
                  color: Colors.white,
                ),
                SizedBox(height: 5),
                CustomText(
                  text: dailyChallengeSubText,
                  fontSize: 10,
                  weight: FontWeight.w400,
                  color: ColorConstants.textColor,
                ),
              ],
            ),
          ),
          const SizedBox(width: 10),
    
          /// play button
          Image.asset('assets/png/forward_button.png', width: 40, height: 40),
        ],
      ),
    ),
  );
}
