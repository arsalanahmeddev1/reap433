import 'package:flutter/material.dart';

/// Image ke upar ek chalti hui golden shine line (glow sweep).
/// Lottie ki jagah — halka, fast, koi file size nahi, koi lag nahi.
///
/// Use: ShineImage(image: 'assets/png/onboarding_1.png', height: ...)
class ShineImage extends StatefulWidget {
  final String image;
  final double? height;
  final double? width;
  final BoxFit fit;

  /// ek sweep kitni der me (kam = tez line)
  final Duration duration;

  /// sweeps ke beech ka gap (line off-screen rukti hai)
  final Duration pause;

  /// shine line ka color
  final Color shineColor;

  /// line kitni bright (0..1)
  final double intensity;

  /// line ki motai (chhoti value = patli line)
  final double bandWidth;

  /// sweep direction (default top-left se bottom-right)
  final Alignment begin;
  final Alignment end;

  const ShineImage({
    super.key,
    required this.image,
    this.height,
    this.width,
    this.fit = BoxFit.contain,
    this.duration = const Duration(milliseconds: 4000),
    this.pause = const Duration(milliseconds: 1600),
    this.shineColor = const Color(0xFFFFF4D6),
    this.intensity = 0.55,
    this.bandWidth = 0.12,
    this.begin = Alignment.topLeft,
    this.end = Alignment.bottomRight,
  });

  @override
  State<ShineImage> createState() => _ShineImageState();
}

class _ShineImageState extends State<ShineImage>
    with SingleTickerProviderStateMixin {
  late final AnimationController _controller;
  late final Animation<double> _sweep;

  @override
  void initState() {
    super.initState();
    final total = widget.duration + widget.pause;
    _controller = AnimationController(vsync: this, duration: total)..repeat();

    // line sirf `duration` tak chale, phir `pause` tak off-screen rukey
    final activeFraction = widget.duration.inMilliseconds / total.inMilliseconds;
    _sweep = Tween<double>(begin: 0.0, end: 1.0).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Interval(0.0, activeFraction, curve: Curves.easeInOut),
      ),
    );
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    final img = Image.asset(
      widget.image,
      height: widget.height,
      width: widget.width,
      fit: widget.fit,
    );

    return AnimatedBuilder(
      animation: _sweep,
      builder: (context, child) {
        return ShaderMask(
          // srcATop = shine sirf image ke visible pixels pe (shape se bahar nahi)
          blendMode: BlendMode.srcATop,
          shaderCallback: (bounds) {
            // band ka center -0.2 se 1.2 tak (image ke bahar se aake bahar jaye)
            final center = _sweep.value * 1.4 - 0.2;
            final b = widget.bandWidth;

            double s0 = (center - b).clamp(0.0, 1.0);
            double s1 = center.clamp(0.0, 1.0);
            double s2 = (center + b).clamp(0.0, 1.0);

            // stops hamesha ascending hone chahiye (warna crash)
            const e = 0.0001;
            if (s1 <= s0) s1 = (s0 + e).clamp(0.0, 1.0);
            if (s2 <= s1) s2 = (s1 + e).clamp(0.0, 1.0);

            return LinearGradient(
              begin: widget.begin,
              end: widget.end,
              colors: [
                Colors.transparent,
                widget.shineColor.withValues(alpha: widget.intensity),
                Colors.transparent,
              ],
              stops: [s0, s1, s2],
            ).createShader(bounds);
          },
          child: child,
        );
      },
      child: img,
    );
  }
}