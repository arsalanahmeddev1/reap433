import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/widgets/custom_text.dart';

Widget sectionHeader(String title, {bool showViewAll = true, void Function()? ontap}) {
  return Row(
    mainAxisAlignment: MainAxisAlignment.spaceBetween,
    children: [
      CustomText(
        text: title,
        fontSize: 16,
        weight: FontWeight.w600,
        color: ColorConstants.creamColor,
      ),
     if(showViewAll) GestureDetector(
        onTap: ontap,
        child: CustomText(
          text: 'view_all'.tr,
          fontSize: 14,
          weight: FontWeight.w400,
          color: ColorConstants.textColor,
        ),
      ),
    ],
  );
}
