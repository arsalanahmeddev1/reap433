// ignore_for_file: use_build_context_synchronously

import 'dart:io';

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:get_storage/get_storage.dart';
import 'package:image_picker/image_picker.dart';
import 'package:reap433/models/signin_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/services/social_auth_service.dart';
import 'package:reap433/services/storage_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';
import 'package:reap433/views/auth/forgot_password/forgot_pass_step_2/forgot_pass_step_2_view.dart';
import 'package:reap433/views/auth/login/login_view.dart';
import 'package:reap433/views/bottom_navigation/bottom_navigation_view.dart';
import 'package:reap433/widgets/custom_text.dart';

class AuthController extends GetxController {
  final emailController = TextEditingController();
  final passwordController = TextEditingController();
  final nameController = TextEditingController();
  final confirmPasswordController = TextEditingController();
  final fcmtoken = GetStorage().read('fcmToken');

  final obscureConfirmPassword = true.obs;
  void toggleObscureConfirm() =>
      obscureConfirmPassword.value = !obscureConfirmPassword.value;

  final profileImagePath = RxnString();

  final obscurePassword = true.obs;
  void toggleObscure() => obscurePassword.value = !obscurePassword.value;

  final isLoading = false.obs;

  final otpController = TextEditingController();
  final newPasswordController = TextEditingController();
  final confirmNewPasswordController = TextEditingController();

  final obscureNewPassword = true.obs;
  void toggleObscureNewPassword() =>
      obscureNewPassword.value = !obscureNewPassword.value;

  final obscureConfirmNewPassword = true.obs;
  void toggleObscureConfirmNewPassword() =>
      obscureConfirmNewPassword.value = !obscureConfirmNewPassword.value;

  String forgotEmail = '';

  final _api = ApiService();
  final _picker = ImagePicker();

  // ================= IMAGE PICK =================
  Future<void> pickProfileImage(BuildContext context) async {
    await showModalBottomSheet(
      context: context,
      backgroundColor: const Color(0xff1A1712),
      shape: const RoundedRectangleBorder(
        borderRadius: BorderRadius.vertical(top: Radius.circular(16)),
      ),
      builder: (ctx) {
        return SafeArea(
          child: Column(
            mainAxisSize: MainAxisSize.min,
            children: [
              ListTile(
                leading: const Icon(Icons.photo_library, color: Colors.white),
                title: const CustomText(text: 'Gallery', color: Colors.white),
                onTap: () {
                  Navigator.pop(ctx);
                  _pickFrom(ImageSource.gallery, context);
                },
              ),
              ListTile(
                leading: const Icon(Icons.camera_alt, color: Colors.white),
                title: const CustomText(text: 'Camera', color: Colors.white),
                onTap: () {
                  Navigator.pop(ctx);
                  _pickFrom(ImageSource.camera, context);
                },
              ),
            ],
          ),
        );
      },
    );
  }

  Future<void> _pickFrom(ImageSource source, BuildContext context) async {
    try {
      final picked = await _picker.pickImage(source: source, imageQuality: 70);
      if (picked != null) {
        profileImagePath.value = picked.path;
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage('Could not pick image', context);
    }
  }

  // ================= LOGIN =================
  Future<void> login(BuildContext context) async {
    final email = emailController.text.trim();
    final password = passwordController.text.trim();

    if (email.isEmpty || password.isEmpty) {
      FlushBarHelper.flushBarErrorMessage(
        'Please enter email and password',
        context,
      );
      return;
    }

    isLoading.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.signIn}',
        {'email': email, 'password': password, 'device_type': Platform.isAndroid ? 'android' : 'ios', 'device_token':fcmtoken??'0000'},
      );

      if ((response['code'] == 200 || response['code'] == 201)) {
        final data = SignInModel.fromJson(response);
        await StorageService.setToken(data.data?.token ?? '');
        Get.offAll(() => BottomNavigation(index: 0));
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Sign in failed',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isLoading.value = false;
    }
  }

  // ================= SIGN UP =================
  Future<void> signUp(BuildContext context) async {
    final name = nameController.text.trim();
    final email = emailController.text.trim();
    final password = passwordController.text.trim();
    final confirmPassword = confirmPasswordController.text.trim();

    // validation
    if (name.isEmpty || email.isEmpty || password.isEmpty) {
      FlushBarHelper.flushBarErrorMessage('Please fill all fields', context);
      return;
    }
    if (password != confirmPassword) {
      FlushBarHelper.flushBarErrorMessage('Passwords do not match', context);
      return;
    }

    isLoading.value = true;
    try {
      final response = await _api.postMultipart(
        '${AppConstants.baseUrl}${AppConstants.signUp}',
        {
          'name': name,
          'email': email,
          'password': password,
          'password_confirmation': confirmPassword,
          'device_type': Platform.isAndroid ? 'android' : 'ios', 'device_token':fcmtoken??'0000'
        },
        filePath: profileImagePath.value,
        fileFieldName: 'profile_image',
      );

      if ((response['code'] == 200 || response['code'] == 201)) {
        final token = response['data']?['token'] ?? '';
        await StorageService.setToken(token);
        Get.offAll(() => BottomNavigation(index: 0));
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Sign up failed',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isLoading.value = false;
    }
  }

  // GOOGLE
  Future<void> googleLogin(BuildContext context) async {
    isLoading.value = true;
    try {
      final result = await SocialAuthService.signInWithGoogle();
      if (result == null) {
        isLoading.value = false;
        return;
      }
      await _socialBackendLogin(context, provider: 'google', data: result);
    } catch (e) {
      print('Google login error: $e');
      FlushBarHelper.flushBarErrorMessage('Google sign in failed', context);
    } finally {
      isLoading.value = false;
    }
  }

  // FACEBOOK
  Future<void> facebookLogin(BuildContext context) async {
    isLoading.value = true;
    try {
      final result = await SocialAuthService.signInWithFacebook();
      if (result == null) {
        isLoading.value = false;
        return;
      }
      await _socialBackendLogin(context, provider: 'facebook', data: result);
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage('Facebook sign in failed', context);
    } finally {
      isLoading.value = false;
    }
  }

  // backend call — API aane pe yahan bhejenge (document ke hisaab se)
  Future<void> _socialBackendLogin(
    BuildContext context, {
    required String provider,
    required Map<String, String?> data,
  }) async {
    // ===== JAB SOCIAL LOGIN API AA JAYE, YE UNCOMMENT KARO =====
    final response = await _api
        .postApi('${AppConstants.baseUrl}${AppConstants.socialLogin}', {
          'name': data['name'] ?? '',
          'provider': provider,
          'id_token': data['firebase_token'] ?? '',
          'email': data['email'] ?? '',
          'profile_image': data['photo'] ?? '',
        });

    if (response['code'] == 200 || response['code'] == 201) {
      final token = response['data']?['token'] ?? '';
      await StorageService.setToken(token);
      Get.offAll(() => BottomNavigation(index: 0));
    } else {
      FlushBarHelper.flushBarErrorMessage(
        response['message'] ?? 'Login failed',
        context,
      );
    }
  }

  // ================= FORGOT PASSWORD — SEND OTP =================
  Future<void> sendForgotOtp(BuildContext context) async {
    final email = emailController.text.trim();
    if (email.isEmpty) {
      FlushBarHelper.flushBarErrorMessage('Please enter your email', context);
      return;
    }

    isLoading.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.forgotPassword}',
        {'email': email},
      );

      if (response['code'] == 200 || response['code'] == 201) {
        forgotEmail = email;
        FlushBarHelper.flushBarSuccessMessage(
          response['message'] ?? 'OTP sent to your email',
          context,
        );
        Get.to(() => const ForgotPassStep2View());
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Failed to send OTP',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isLoading.value = false;
    }
  }

  // ================= FORGOT PASSWORD — RESET =================
  Future<void> resetPassword(BuildContext context) async {
    final otp = otpController.text.trim();
    final newPass = newPasswordController.text.trim();
    final confirmPass = confirmNewPasswordController.text.trim();

    if (otp.isEmpty || newPass.isEmpty) {
      FlushBarHelper.flushBarErrorMessage('Please fill all fields', context);
      return;
    }
    if (newPass != confirmPass) {
      FlushBarHelper.flushBarErrorMessage('Passwords do not match', context);
      return;
    }

    isLoading.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.forgotPasswordReset}',
        {'email': forgotEmail, 'otp': otp, 'new_password': newPass},
      );

      if (response['code'] == 200 || response['code'] == 201) {
        FlushBarHelper.flushBarSuccessMessage(
          response['message'] ?? 'Password reset successfully',
          context,
        );
        // sab clear karke login pe wapas
        otpController.clear();
        newPasswordController.clear();
        confirmNewPasswordController.clear();
        Get.offAll(() => const LoginView());
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Failed to reset password',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isLoading.value = false;
    }
  }

  // resend OTP (step 2 se)
  Future<void> resendForgotOtp(BuildContext context) async {
    if (forgotEmail.isEmpty) return;
    isLoading.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.forgotPassword}',
        {'email': forgotEmail},
      );
      if (response['code'] == 200 || response['code'] == 201) {
        FlushBarHelper.flushBarSuccessMessage('OTP resent', context);
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Failed to resend',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isLoading.value = false;
    }
  }

  @override
  void onClose() {
    emailController.dispose();
    passwordController.dispose();
    nameController.dispose();
  confirmPasswordController.dispose();
    otpController.dispose();
    newPasswordController.dispose();
    confirmNewPasswordController.dispose();
    super.onClose();
  }
}
