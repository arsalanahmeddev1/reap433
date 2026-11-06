import Flutter
import UIKit
import Firebase
import FirebaseMessaging
import UserNotifications

@main
@objc class AppDelegate: FlutterAppDelegate {
    
  override func application(
    _ application: UIApplication,
    didFinishLaunchingWithOptions launchOptions: [UIApplication.LaunchOptionsKey: Any]?
  ) -> Bool {
      
    // Firebase configure — sabse pehle
    FirebaseApp.configure()
      
    // APNs delegate set karo
    UNUserNotificationCenter.current().delegate = self
      
    // Plugin registration — standard tarika
    GeneratedPluginRegistrant.register(with: self)
      
    return super.application(application, didFinishLaunchingWithOptions: launchOptions)
  }
    
  // ✅ Yeh method APNs token Firebase ko deta hai
  override func application(
    _ application: UIApplication,
    didRegisterForRemoteNotificationsWithDeviceToken deviceToken: Data
  ) {
    Messaging.messaging().apnsToken = deviceToken
    print("✅ APNs Token set: \(deviceToken)")
    super.application(application, didRegisterForRemoteNotificationsWithDeviceToken: deviceToken)
  }
    
  // ✅ Agar APNs registration fail ho
  override func application(
    _ application: UIApplication,
    didFailToRegisterForRemoteNotificationsWithError error: Error
  ) {
    print("❌ APNs registration failed: \(error.localizedDescription)")
  }
}