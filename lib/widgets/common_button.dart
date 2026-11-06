import 'package:flutter/material.dart';
import 'package:reap433/utils/constants.dart';

class CommonButton extends StatelessWidget {
  final String title;
  final Function()? onPressed;
  final bool isTransparent;
  final bool isEnabled;
  final Color? bgColor;
  final Color? borderColor;
  final Color textColor;
  final Widget? icon;
  final Widget? trailingIcon;
  final double fontSize;
  final bool isLoading;
  final double horizontalPadding, verticalPadding;
  final double borderRadius;
  final FontWeight textWeight;
  final Color? buttonShade;
  final Color? shadowColor;
  final String? fontFamily;
  final bool applyGradient;
  final Gradient? gradient;

  const CommonButton({
    super.key,
    required this.title,
    this.onPressed,
    this.isTransparent = false,
    this.bgColor,
    this.borderColor,
    this.textColor = Colors.white,
    this.icon,
    this.trailingIcon,
    this.horizontalPadding = 0,
    this.verticalPadding = 19,
    this.fontSize = 16,
    this.isEnabled = true,
    this.isLoading = false,
    this.borderRadius = 9,
    this.textWeight = FontWeight.w500,
    this.buttonShade,
    this.shadowColor,
    this.fontFamily,
    this.applyGradient = true,
    this.gradient,
  });

  @override
  Widget build(BuildContext context) {
    /// ✅ Default background & border logic
    final Color defaultButtonColor = ColorConstants.gradientColor1;
    final Color buttonBgColor = bgColor ?? defaultButtonColor;

    /// ✅ Shadow only applied if shadowColor is passed
    final bool applyShadow = shadowColor != null;

    /// ✅ Gradient logic
    final bool useGradient =
        applyGradient && isEnabled && !isTransparent && bgColor == null;

    final Gradient defaultGradient = LinearGradient(
      begin: Alignment.topCenter,
      end: Alignment.bottomCenter,
      colors: [ColorConstants.gradientColor1, ColorConstants.gradientColor2],
    );

    return GestureDetector(
      onTap: isLoading || !isEnabled ? null : onPressed,
      child: Container(
        width: horizontalPadding > 0 ? null : double.infinity,
        padding: EdgeInsets.symmetric(
          vertical: verticalPadding,
          horizontal: horizontalPadding,
        ),
        decoration: BoxDecoration(
          /// ✅ Agar gradient use ho raha hai to color null, warna solid color
          color: useGradient
              ? null
              : (!isEnabled
                    ? Colors.grey[300]
                    : isTransparent
                    ? Colors.transparent
                    : buttonBgColor),
          gradient: useGradient ? (gradient ?? defaultGradient) : null,
          borderRadius: BorderRadius.circular(borderRadius),

          /// ✅ Border sirf tab jab borderColor pass ho — warna koi border nahi
          border: borderColor != null
              ? Border.all(color: borderColor!, width: 0.5)
              : null,

          boxShadow: applyShadow
              ? [
                  BoxShadow(
                    color: shadowColor!.withValues(alpha: 0.4),
                    offset: const Offset(0, 4),
                    blurRadius: 6,
                    spreadRadius: 1,
                  ),
                ]
              : [],
        ),
        child: isLoading
            ? const Center(
                child: SizedBox(
                  width: 21,
                  height: 21,
                  child: CircularProgressIndicator(
                    color: Colors.black,
                    strokeWidth: 2,
                  ),
                ),
              )
            : Row(
                mainAxisAlignment: MainAxisAlignment.center,
                mainAxisSize: MainAxisSize.min,
                children: [
                  if (icon != null) ...[icon!, const SizedBox(width: 5)],
                  FittedBox(
                    fit: BoxFit.scaleDown,
                    child: Text(
                      title,
                      style: TextStyle(
                        fontSize: fontSize,
                        color: isTransparent ? textColor : textColor,
                        fontWeight: textWeight,
                        fontFamily: fontFamily ?? 'Poppins',
                        letterSpacing: -0.5,
                      ),
                    ),
                  ),
                  if (trailingIcon != null) ...[
                    const SizedBox(width: 5),
                    trailingIcon!,
                  ],
                ],
              ),
      ),
    );
  }
}
