import 'package:url_launcher/url_launcher.dart';

class HelperMethods {
  static Future<void> openUrl(String url) async {
    final Uri uri = Uri.parse(url);

    // Try to open in Chrome (Android)

    try {
      bool launched = await launchUrl(
        uri,
        mode: LaunchMode.externalApplication,
        webViewConfiguration: WebViewConfiguration(),
        browserConfiguration: BrowserConfiguration(),
      );

      if (!launched) {
        // If Chrome is not installed, open in default browser
        await launchUrl(uri, mode: LaunchMode.externalApplication);
      }
    } catch (e) {
      // Fallback in case anything fails
      await launchUrl(uri, mode: LaunchMode.externalApplication);
    }
  }
}
