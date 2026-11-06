import 'package:get/get.dart';
import 'package:reap433/models/achievements_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';

class AchievementController extends GetxController {
  final _api = ApiService();

  final isLoading = false.obs;
  final claimingId = (-1).obs; // kaunsa claim ho raha (loader ke liye)

  // 0 = All, 1 = Earned (completed), 2 = Locked
  final selectedTab = 0.obs;
  void selectTab(int i) => selectedTab.value = i;

  final all = <Achievement>[].obs;

  @override
  void onInit() {
    super.onInit();
    fetchAchievements();
  }

  Future<void> fetchAchievements() async {
    isLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.achievements}',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = AchievementsModel.fromJson(response);
        all.assignAll(model.data?.achievements ?? []);
      } else {
        _showError(response['message'] ?? 'Failed to load achievements');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isLoading.value = false;
    }
  }

  /// tab filter:
  /// Earned = completed (is_completed == 1), Locked = not completed
  List<Achievement> get filtered {
    switch (selectedTab.value) {
      case 1: // Earned
        return all.where((a) => (a.isCompleted ?? 0) == 1).toList();
      case 2: // Locked
        return all.where((a) => (a.isCompleted ?? 0) == 0).toList();
      default:
        return all;
    }
  }

  /// CLAIM — POST achievements/claim
  Future<void> claim(Achievement a) async {
    claimingId.value = a.id ?? -1;
    try {
      final response = await _api.postApi(
        '${AppConstants.baseUrl}${AppConstants.achievementsClaim}',
        {'achievement_id': a.id},
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final ctx = Get.context;
        if (ctx != null) {
          FlushBarHelper.flushBarSuccessMessage(
            response['message'] ?? 'Reward claimed', ctx);
        }
        await fetchAchievements(); // refresh — ab tick dikhega
      } else {
        _showError(response['message'] ?? 'Failed to claim');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      claimingId.value = -1;
    }
  }

  void _showError(String msg) {
    final ctx = Get.context;
    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}