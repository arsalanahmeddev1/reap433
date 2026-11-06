import 'package:flutter/material.dart';
import 'package:reap433/utils/constants.dart';

class GradientText extends StatelessWidget {
  final Widget child;
  final Gradient? gradient;

  const GradientText({super.key, required this.child, this.gradient});

  static Gradient goldGradient = LinearGradient(
    begin: Alignment.topCenter,
    end: Alignment.bottomCenter,
    colors: [
      ColorConstants.gradientColor1,
      ColorConstants.gradientColor2,
    ],
  );

  @override
  Widget build(BuildContext context) {
    return ShaderMask(
      blendMode: BlendMode.srcIn,
      shaderCallback: (bounds) => (gradient ?? goldGradient).createShader(
        Rect.fromLTWH(0, 0, bounds.width, bounds.height),
      ),
      child: child,
    );
  }
}