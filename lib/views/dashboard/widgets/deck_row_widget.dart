import 'package:flutter/material.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';

Widget deckRowCard({
  required String thumbnail,
  required String title,
  required String subtitle,
  double? progress,
  void Function()? ontap,
}) {
  return GestureDetector(
    onTap: ontap,
    child: Container(
      padding: const EdgeInsets.symmetric(horizontal: 12, vertical: 12),
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
            child: Image.network(
              thumbnail,
              width: 81,
              height: 80,
              fit: BoxFit.cover,
              loadingBuilder: (context, child, progress) {
                if (progress == null) return child;
                return Container(
                  width: 81,
                  height: 80,
                  color: const Color(0xff1A1712),
                );
              },
              errorBuilder: (_, __, ___) => Image.asset(
                'assets/png/dummy_deck.png',
                width: 81,
                height: 80,
                fit: BoxFit.cover,
              ),
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
                  color: ColorConstants.creamColor,
                ),
                CustomText(
                  text: subtitle,
                  fontSize: 14,
                  weight: FontWeight.w400,
                  color: ColorConstants.creamColor,
                ),
    
                /// progress bar (continue learning)
                if (progress != null)
                  Row(
                    children: [
                      Expanded(
                        child: ClipRRect(
                          child: LinearProgressIndicator(
                            value: progress,
                            minHeight: 7,
                            backgroundColor: const Color(0xff3F382D),
                            valueColor: const AlwaysStoppedAnimation(
                              Color(0xffBD8638),
                            ),
                          ),
                        ),
                      ),
                      const SizedBox(width: 10),
                      CustomText(
                        text: '${(progress * 100).round()}%',
                        fontSize: 14,
                        weight: FontWeight.w400,
                        color: ColorConstants.creamColor,
                      ),
                    ],
                  ),
              ],
            ),
          ),
          const SizedBox(width: 15),
    
          /// play button
          GestureDetector(onTap: ontap,child: Image.asset('assets/png/play_button.png', width: 40, height: 40)),
        ],
      ),
    ),
  );
}
