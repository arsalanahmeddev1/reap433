// ignore_for_file: use_build_context_synchronously

import 'package:flutter/material.dart';
import 'package:get/get.dart';
import 'package:image_picker/image_picker.dart';
import 'package:reap433/models/profile_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/services/storage_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';
import 'package:reap433/views/auth/login/login_view.dart';

class ProfileController extends GetxController {
  final _api = ApiService();

  final isLoading = false.obs;
  final isDeleting = false.obs;
  final Rxn<User> user = Rxn<User>();

  final nameController = TextEditingController();
  final newProfileImagePath = RxnString();
  final isUpdating = false.obs;
  final _picker = ImagePicker();

  final oldPasswordController = TextEditingController();
  final changeNewPasswordController = TextEditingController();
  final changeConfirmPasswordController = TextEditingController();

  final obscureOld = true.obs;
  final obscureNew = true.obs;
  final obscureConfirm2 = true.obs;
  void toggleOld() => obscureOld.value = !obscureOld.value;
  void toggleNew() => obscureNew.value = !obscureNew.value;
  void toggleConfirm2() => obscureConfirm2.value = !obscureConfirm2.value;

  final isChangingPassword = false.obs;

  Future<void> fetchProfile() async {
    isLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.profile}',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = ProfileModel.fromJson(response);
        user.value = model.data?.user;
      } else {
        _showError(response['message'] ?? 'Failed to load profile');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isLoading.value = false;
    }
  }

  Future<void> deleteAccount(BuildContext context) async {
    isDeleting.value = true;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.logout}?is_delete=1',
        {},
      );

      if (response['code'] == 200 || response['code'] == 201) {
        await StorageService.removeToken();
        Get.offAll(() => const LoginView());
      } else {
        FlushBarHelper.flushBarErrorMessage(response['message'], context);
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isDeleting.value = false;
    }
  }

  void prefillEditProfile() {
    nameController.text = user.value?.name ?? '';
    newProfileImagePath.value = null;
  }

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
                title: const Text(
                  'Gallery',
                  style: TextStyle(color: Colors.white),
                ),
                onTap: () {
                  Navigator.pop(ctx);
                  _pickFrom(ImageSource.gallery, context);
                },
              ),
              ListTile(
                leading: const Icon(Icons.camera_alt, color: Colors.white),
                title: const Text(
                  'Camera',
                  style: TextStyle(color: Colors.white),
                ),
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
        newProfileImagePath.value = picked.path;
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage('Could not pick image', context);
    }
  }

  // ================= UPDATE PROFILE =================
  Future<void> updateProfile(BuildContext context) async {
    final name = nameController.text.trim();
    if (name.isEmpty) {
      FlushBarHelper.flushBarErrorMessage('Please enter your name', context);
      return;
    }

    isUpdating.value = true;
    try {
      final response = await _api.postMultipart(
        '${AppConstants.baseUrl}${AppConstants.updateProfile}',
        {'name': name, '_method': 'PUT'},
        filePath: newProfileImagePath.value,
        fileFieldName: 'profile_image',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = ProfileModel.fromJson(response);
        if (model.data?.user != null) {
          user.value = model.data!.user;
        } else {
          fetchProfile();
        }
        Get.back();
        FlushBarHelper.flushBarSuccessMessage(
          response['message'] ?? 'Profile updated',
          context,
        );
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Failed to update profile',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isUpdating.value = false;
    }
  }

  // ================= CHANGE PASSWORD =================
  Future<void> changePassword(BuildContext context) async {
    final oldPass = oldPasswordController.text.trim();
    final newPass = changeNewPasswordController.text.trim();
    final confirmPass = changeConfirmPasswordController.text.trim();

    if (oldPass.isEmpty || newPass.isEmpty || confirmPass.isEmpty) {
      FlushBarHelper.flushBarErrorMessage('Please fill all fields', context);
      return;
    }
    if (newPass != confirmPass) {
      FlushBarHelper.flushBarErrorMessage('Passwords do not match', context);
      return;
    }

    isChangingPassword.value = true;
    try {
      final response = await _api.postMultipart(
        '${AppConstants.baseUrl}${AppConstants.changePassword}',
        {
          'old_password': oldPass,
          'new_password': newPass,
          'confirm_password': confirmPass,
        },
      );

      if (response['code'] == 200 || response['code'] == 201) {
        oldPasswordController.clear();
        changeNewPasswordController.clear();
        changeConfirmPasswordController.clear();
        Get.back();
        FlushBarHelper.flushBarSuccessMessage(
          response['message'] ?? 'Password changed successfully',
          context,
        );
      } else {
        FlushBarHelper.flushBarErrorMessage(
          response['message'] ?? 'Failed to change password',
          context,
        );
      }
    } catch (e) {
      FlushBarHelper.flushBarErrorMessage(e.toString(), context);
    } finally {
      isChangingPassword.value = false;
    }
  }

  void _showError(String msg) {
    final ctx = Get.context;
    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}
