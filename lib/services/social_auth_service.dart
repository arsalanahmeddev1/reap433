import 'package:firebase_auth/firebase_auth.dart';
import 'package:google_sign_in/google_sign_in.dart';
import 'package:flutter_facebook_auth/flutter_facebook_auth.dart';

class SocialAuthService {
  static final _auth = FirebaseAuth.instance;
  static bool _googleInitialized = false;

  static Future<void> _ensureGoogleInit() async {
    if (_googleInitialized) return;
    await GoogleSignIn.instance.initialize();
    _googleInitialized = true;
  }

  /// GOOGLE — returns firebase idToken + name, ya null agar cancel
  static Future<Map<String, String?>?> signInWithGoogle() async {
    try {
      await _ensureGoogleInit();
      final googleUser = await GoogleSignIn.instance.authenticate();

      final googleAuth = googleUser.authentication;

      final credential = GoogleAuthProvider.credential(
        idToken: googleAuth.idToken,
      );

      final userCred = await _auth.signInWithCredential(credential);
      final firebaseToken = await userCred.user?.getIdToken();

      return {
        'firebase_token': firebaseToken,
        'name': userCred.user?.displayName,
        'email': userCred.user?.email,
        'photo': userCred.user?.photoURL,
      };
    } on GoogleSignInException catch (e) {
      if (e.code == GoogleSignInExceptionCode.canceled) return null;
      rethrow;
    } catch (e) {
      rethrow;
    }
  }

  // /// FACEBOOK
  // static Future<Map<String, String?>?> signInWithFacebook() async {
  //   try {
  //     final result = await FacebookAuth.instance.login(
  //       permissions: ['email', 'public_profile'],
  //     );
  //     if (result.status != LoginStatus.success) return null;

  //     final credential = FacebookAuthProvider.credential(
  //       result.accessToken!.tokenString,
  //     );

  //     final userCred = await _auth.signInWithCredential(credential);
  //     final firebaseToken = await userCred.user?.getIdToken();

  //     return {
  //       'firebase_token': firebaseToken,
  //       'name': userCred.user?.displayName,
  //       'email': userCred.user?.email,
  //       'photo': userCred.user?.photoURL,
  //     };
  //   } catch (e) {
  //     rethrow;
  //   }
  // }

  static Future<Map<String, String?>?> signInWithFacebook() async {
  try {
    final result = await FacebookAuth.instance.login(
      permissions: ['email', 'public_profile'],
      loginTracking: LoginTracking.enabled, // classic login (limited nahi)
    );

    if (result.status != LoginStatus.success) {
      print('FB LOGIN STATUS: ${result.status}');
      print('FB LOGIN MESSAGE: ${result.message}');
      return null;
    }

    // Graph API se user data (email, name, photo)
    final userData = await FacebookAuth.instance.getUserData(
      fields: 'id,name,email,picture.width(500).height(500)',
    );
    print('FACEBOOK DATA: $userData');

    final credential = FacebookAuthProvider.credential(
      result.accessToken!.tokenString,
    );

    final userCred = await _auth.signInWithCredential(credential);
    final firebaseToken = await userCred.user?.getIdToken();

    // dono jagah se email try — Graph API + Firebase
    final emailFromGraph = userData['email']?.toString();
    final emailFromFirebase = userCred.user?.email;
    print('EMAIL (graph): $emailFromGraph');
    print('EMAIL (firebase): $emailFromFirebase');

    return {
      'firebase_token': firebaseToken,
      'name': userData['name']?.toString() ?? userCred.user?.displayName,
      'email': emailFromGraph ?? emailFromFirebase, // dono try
      'photo': userData['picture']?['data']?['url']?.toString() ??
          userCred.user?.photoURL,
    };
  } catch (e) {
    print('FB SERVICE ERROR: $e');
    rethrow;
  }
}

  static Future<void> signOut() async {
    try {
      await GoogleSignIn.instance.signOut();
      await FacebookAuth.instance.logOut();
      await _auth.signOut();
    } catch (_) {}
  }
}
