import 'package:get/get.dart';
import 'package:reap433/models/progress_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';

class MyProgressController extends GetxController {
  final _api = ApiService();

  final isLoading = false.obs;

  final progress = Rxn<Data>();

  Future<void> fetchProgress() async {
    isLoading.value = true;

    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.progress}',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = ProgressModel.fromJson(response);
        progress.value = model.data;
      } else {
        _showError(response['message'] ?? 'Failed to load progress');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isLoading.value = false;
    }
  }

  void _showError(String msg) {
    final ctx = Get.context;

    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}