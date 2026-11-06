import 'dart:math' as math;
import 'package:flutter/material.dart';
import 'package:reap433/widgets/custom_text2.dart';
import 'package:reap433/widgets/gradient_text.dart';

class GradientCircularProgress extends StatefulWidget {
  final double value; // 0.0 - 1.0
  final double size;
  final double strokeWidth;
  final Color trackColor;
  final List<Color> gradientColors;
  final Widget? child;

  const GradientCircularProgress({
    super.key,
    required this.value,
    this.size = 230,
    this.strokeWidth = 20,
    this.trackColor = const Color(0xff211B14),
    this.gradientColors = const [
      Color(0xFFEDC57C),
      Color(0xFFAC823B),
    ],
    this.child,
  });

  @override
  State<GradientCircularProgress> createState() =>
      _GradientCircularProgressState();
}

class _GradientCircularProgressState
    extends State<GradientCircularProgress>
    with SingleTickerProviderStateMixin {
  late AnimationController _controller;
  late Animation<double> _progressAnimation;

  double _oldValue = 0.0;

  @override
  void initState() {
    super.initState();

    _controller = AnimationController(
      vsync: this,
      duration: const Duration(milliseconds: 1400),
    );

    _progressAnimation = Tween<double>(
      begin: 0.0,
      end: widget.value.clamp(0.0, 1.0),
    ).animate(
      CurvedAnimation(
        parent: _controller,
        curve: Curves.easeOutCubic,
      ),
    );

    _controller.forward();
    _oldValue = widget.value.clamp(0.0, 1.0);
  }

  @override
  void didUpdateWidget(covariant GradientCircularProgress oldWidget) {
    super.didUpdateWidget(oldWidget);

    final newValue = widget.value.clamp(0.0, 1.0);

    if (newValue != _oldValue) {
      _progressAnimation = Tween<double>(
        begin: _oldValue,
        end: newValue,
      ).animate(
        CurvedAnimation(
          parent: _controller,
          curve: Curves.easeOutCubic,
        ),
      );

      _oldValue = newValue;

      _controller
        ..reset()
        ..forward();
    }
  }

  @override
  void dispose() {
    _controller.dispose();
    super.dispose();
  }

  @override
  Widget build(BuildContext context) {
    return AnimatedBuilder(
      animation: _progressAnimation,
      builder: (context, child) {
        final animatedValue = _progressAnimation.value;

        return SizedBox(
          width: widget.size,
          height: widget.size,
          child: CustomPaint(
            painter: _GradientCirclePainter(
              value: animatedValue,
              strokeWidth: widget.strokeWidth,
              trackColor: widget.trackColor,
              gradientColors: widget.gradientColors,
            ),
            child: Center(
              child: _buildAnimatedChild(animatedValue),
            ),
          ),
        );
      },
    );
  }

  Widget? _buildAnimatedChild(double animatedValue) {
    if (widget.child == null) return null;

    final percentage = (animatedValue * 100).round();

    if (widget.child is Column) {
      final column = widget.child as Column;

      return Column(
        mainAxisSize: column.mainAxisSize,
        crossAxisAlignment: column.crossAxisAlignment,
        mainAxisAlignment: column.mainAxisAlignment,
        children: column.children.map((child) {
          if (child is GradientText) {
            return GradientText(
              child: _replacePercentageText(
                child.child,
                '$percentage%',
              ),
            );
          }

          return child;
        }).toList(),
      );
    }

    return widget.child;
  }

  Widget _replacePercentageText(
    Widget child,
    String percentage,
  ) {
    if (child is CustomText2) {
      return CustomText2(
        text: percentage,
        fontSize: child.fontSize,
        letterSpacing: child.letterSpacing,
        weight: child.weight,
        color: child.color,
        lineSpacing: child.lineSpacing,
      );
    }

    return child;
  }
}

class _GradientCirclePainter extends CustomPainter {
  final double value;
  final double strokeWidth;
  final Color trackColor;
  final List<Color> gradientColors;

  _GradientCirclePainter({
    required this.value,
    required this.strokeWidth,
    required this.trackColor,
    required this.gradientColors,
  });

  @override
  void paint(Canvas canvas, Size size) {
    final center = Offset(
      size.width / 2,
      size.height / 2,
    );

    final radius = (size.width - strokeWidth) / 2;

    const startAngle = -math.pi / 2;

    final sweepAngle = 2 * math.pi * value;

    /// TRACK
    final trackPaint = Paint()
      ..color = trackColor
      ..style = PaintingStyle.stroke
      ..strokeWidth = strokeWidth
      ..strokeCap = StrokeCap.round;

    canvas.drawCircle(
      center,
      radius,
      trackPaint,
    );

    if (value <= 0) return;

    final rect = Rect.fromCircle(
      center: center,
      radius: radius,
    );

    /// PROGRESS ARC
    final progressPaint = Paint()
      ..shader = SweepGradient(
        startAngle: 0,
        endAngle: sweepAngle,
        colors: gradientColors,
        tileMode: TileMode.clamp,
        transform: const GradientRotation(startAngle),
      ).createShader(rect)
      ..style = PaintingStyle.stroke
      ..strokeWidth = strokeWidth
      ..strokeCap = StrokeCap.round;

    canvas.drawArc(
      rect,
      startAngle,
      sweepAngle,
      false,
      progressPaint,
    );

    /// START CAP
    final startPoint = Offset(
      center.dx + radius * math.cos(startAngle),
      center.dy + radius * math.sin(startAngle),
    );

    final capPaint = Paint()
      ..color = gradientColors.first
      ..style = PaintingStyle.fill;

    canvas.drawCircle(
      startPoint,
      strokeWidth / 2,
      capPaint,
    );
  }

  @override
  bool shouldRepaint(
    _GradientCirclePainter old,
  ) {
    return old.value != value ||
        old.trackColor != trackColor ||
        old.gradientColors != gradientColors;
  }
}