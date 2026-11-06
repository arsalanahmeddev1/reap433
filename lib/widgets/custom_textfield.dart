import 'package:flutter/material.dart';
import 'package:flutter/services.dart';
import 'package:reap433/utils/constants.dart';

class CustomTextField extends StatelessWidget {
  final TextEditingController? controller;
  final String? hintText;
  final String? labelText;
  final String? initialValue;
  final Widget? prefixIcon;
  final Widget? suffixIcon;
  final Color? fillColor;
  final double? padding;
  final TextInputType? keyboardType;
  final List<TextInputFormatter>? inputFormatters;
  final int? lines;
  final bool forceBorder;
  final double? borderRadius;
  final bool obscureText;
  final bool readOnly;
  final String? Function(String?)? validator;
  final Function(String)? onChanged;
  final Color? borderColor;
  final EdgeInsetsGeometry? contentPadding;
  final TextStyle? hintStyle;
  final double? hintTextPadding;
  final double? leftPadding;
  final double? borderWidth;
  final FocusNode? focusNode;

  const CustomTextField({
    super.key,
    this.controller,
    this.hintText,
    this.labelText,
    this.initialValue,
    this.prefixIcon,
    this.suffixIcon,
    this.fillColor,
    this.padding,
    this.keyboardType,
    this.inputFormatters,
    this.lines = 1,
    this.obscureText = false,
    this.forceBorder = false,
    this.readOnly = false,
    this.validator,
    this.onChanged,
    this.borderRadius,
    this.borderColor,
    this.contentPadding,
    this.hintStyle,
    this.hintTextPadding,
    this.leftPadding,
    this.borderWidth,
    this.focusNode,
  });

  @override
  Widget build(BuildContext context) {
    return TextFormField(
      controller: controller,
      focusNode: focusNode,
      keyboardType: keyboardType,
      inputFormatters: inputFormatters,
      maxLines: lines,
      validator: validator,
      onChanged: onChanged,
      obscureText: obscureText,
      readOnly: readOnly,
      initialValue: initialValue,
      style: TextStyle(
        fontFamily: 'Poppins',
        fontSize: 16,
        fontWeight: FontWeight.w400,
        color: ColorConstants.textColor,
        letterSpacing: -0.5,
      ),
      decoration: InputDecoration(
        hintText: hintText,
        labelText: labelText,
        floatingLabelBehavior: FloatingLabelBehavior.always,
        labelStyle: TextStyle(
          color: ColorConstants.textColor,
          fontFamily: 'Poppins',
          fontWeight: FontWeight.w600,
          fontSize: 14,
          letterSpacing: -0.5,
        ),
        filled: fillColor != null,
        fillColor: fillColor ?? ColorConstants.textfieldFillColor,
        prefixIcon: prefixIcon,
        suffixIcon: suffixIcon,
        contentPadding:
            contentPadding ??
            EdgeInsets.only(
              top: 19,
              bottom: 19,
              left: leftPadding ?? 20,
              right: 20,
            ),
        prefixIconConstraints: const BoxConstraints(
          minWidth: 24,
          minHeight: 24,
        ),
        suffixIconConstraints: const BoxConstraints(
          minWidth: 24,
          minHeight: 24,
        ),
        hintStyle:
            hintStyle ??
            TextStyle(
              color: ColorConstants.textColor,
              fontSize: 16,
              fontFamily: 'Poppins',
              fontWeight: FontWeight.w500,
              letterSpacing: -0.5,
            ),
        enabledBorder: border(),
        focusedBorder: border(),
        errorBorder: errorBorder(),
        focusedErrorBorder: errorBorder(),
      ),
    );
  }

  OutlineInputBorder border() {
    return OutlineInputBorder(
      borderRadius: BorderRadius.circular(borderRadius ?? 9),
      borderSide: BorderSide(
        color: borderColor ?? ColorConstants.textfieldBorderColor,
        width: borderWidth ?? 0.5,
      ),
    );
  }

  OutlineInputBorder errorBorder() {
    return OutlineInputBorder(
      borderRadius: BorderRadius.circular(9),
      borderSide: BorderSide(color: Colors.red[900]!, width: borderWidth ?? 0),
    );
  }
}
