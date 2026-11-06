import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:reap433/services/fontsize_service.dart';

class CustomText2 extends StatelessWidget {
  final String text;
  final double fontSize;
  final FontWeight? weight;
  final Color? color;
  final int? lines;
  final TextAlign? textAlign;
  final FontStyle? fontStyle;
  final TextOverflow? textOverflow;
  final TextDecoration? decoration;
  final double decorationThickness;
  final TextDecorationStyle? decorationStyle;
  final Color? decorationColor;
  final double? letterSpacing;
  final double? lineSpacing;
  final String? fontFamily;

  const CustomText2({
    super.key,
    required this.text,
    this.fontSize = 15,
    this.weight,
    this.color,
    this.lines,
    this.fontStyle,
    this.textOverflow,
    this.textAlign,
    this.decoration = TextDecoration.none,
    this.decorationThickness = 1.0,
    this.decorationStyle = TextDecorationStyle.solid,
    this.decorationColor,
    this.letterSpacing,
    this.lineSpacing,
    this.fontFamily,
  });

  @override
  Widget build(BuildContext context) {
    return Obx(
      () => Text(
        text,
        maxLines: lines,
        textAlign: textAlign,
        overflow: textOverflow,
        style: TextStyle(
          fontSize: fontSize * FontSizeService.to.scale.value,
          color: color ?? Colors.white,
          fontWeight: weight ?? FontWeight.w500,
          fontStyle: fontStyle,
          fontFamily: fontFamily ?? 'Parastoo',
          decoration: decoration,
          decorationThickness: decorationThickness,
          decorationStyle: decorationStyle ?? TextDecorationStyle.solid,
          decorationColor: decorationColor ?? color,
          letterSpacing: letterSpacing ?? 0,
          height: lineSpacing,
        ),
      ),
    );
  }
}
