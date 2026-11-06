import 'package:get/get.dart';
import 'package:reap433/models/user_ranking_model.dart';
import 'package:reap433/services/api_service.dart';
import 'package:reap433/utils/constants.dart';
import 'package:reap433/utils/flushbar_helper.dart';

class RankingController extends GetxController {
  final _api = ApiService();

  var selectedTab = 0.obs;
  final isLoading = false.obs;

  final users = <User>[].obs;

  void selectTab(int i) {
    selectedTab.value = i;
    fetchLeaderboard();
  }

  // tab index → period param (backend jab filter add kare)
  String get period {
    switch (selectedTab.value) {
      case 0:
        return 'daily';
      case 1:
        return 'weekly';
      case 2:
        return 'monthly';
      case 3:
        return 'all_time';
      default:
        return 'daily';
    }
  }

  @override
  void onInit() {
    super.onInit();
    fetchLeaderboard();
  }

  Future<void> fetchLeaderboard() async {
    isLoading.value = true;
    try {
      final response = await _api.getApi(
        '${AppConstants.baseUrl}${AppConstants.userRanking}?filter=$period',
      );

      if (response['code'] == 200 || response['code'] == 201) {
        final model = UserRankingModel.fromJson(response);
        users.assignAll(model.data?.users ?? []);
      } else {
        _showError(response['message'] ?? 'Failed to load ranking');
      }
    } catch (e) {
      _showError(e.toString());
    } finally {
      isLoading.value = false;
    }
  }

  /// current user list mein hai ya nahi (top mein na ho to alag dikhana)
  User? get currentUser {
    try {
      return users.firstWhere((u) => u.currentUser == true);
    } catch (_) {
      return null;
    }
  }

  /// current user top list (jaise top 10) mein hai?
  /// yahan poori list dikhti hai toh agar current_user list me hai to ranked hai
  bool get isCurrentUserRanked => currentUser != null;

  void _showError(String msg) {
    final ctx = Get.context;
    if (ctx != null) {
      FlushBarHelper.flushBarErrorMessage(msg, ctx);
    }
  }
}