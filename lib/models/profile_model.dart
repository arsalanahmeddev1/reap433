class ProfileModel {
    ProfileModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory ProfileModel.fromJson(Map<String, dynamic> json){ 
        return ProfileModel(
            success: json["success"],
            code: json["code"],
            message: json["message"],
            data: json["data"] == null ? null : Data.fromJson(json["data"]),
        );
    }

    Map<String, dynamic> toJson() => {
        "success": success,
        "code": code,
        "message": message,
        "data": data?.toJson(),
    };

}

class Data {
    Data({
        required this.user,
    });

    final User? user;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            user: json["user"] == null ? null : User.fromJson(json["user"]),
        );
    }

    Map<String, dynamic> toJson() => {
        "user": user?.toJson(),
    };

}

class User {
    User({
        required this.id,
        required this.name,
        required this.email,
        required this.profileImage,
        required this.role,
        required this.totalXp,
        required this.totalCoins,
        required this.provider,
        required this.totalStreak,
        required this.dailyChallenge,
    });

    final int? id;
    final String? name;
    final String? email;
    final String? profileImage;
    final String? role;
    final int? totalXp;
    final int? totalCoins;
    final String? provider;
    final int? totalStreak;
    final DailyChallenge? dailyChallenge;

    factory User.fromJson(Map<String, dynamic> json){ 
        return User(
            id: json["id"],
            name: json["name"],
            email: json["email"],
            profileImage: json["profile_image"],
            role: json["role"],
            totalXp: json["total_xp"],
            totalCoins: json["total_coins"],
            provider: json["provider"],
            totalStreak: json["total_streak"],
            dailyChallenge: json["daily_challenge"] == null ? null : DailyChallenge.fromJson(json["daily_challenge"]),
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "email": email,
        "profile_image": profileImage,
        "role": role,
        "total_xp": totalXp,
        "total_coins": totalCoins,
        "provider": provider,
        "total_streak": totalStreak,
        "daily_challenge": dailyChallenge?.toJson(),
    };

}

class DailyChallenge {
    DailyChallenge({
        required this.quizCategoryId,
        required this.quizTypeId,
        required this.totalXp,
        required this.totalCoins,
        required this.challengeReset,
        required this.isPlayed,
    });

    final int? quizCategoryId;
    final int? quizTypeId;
    final int? totalXp;
    final int? totalCoins;
    final String? challengeReset;
    final bool? isPlayed;

    factory DailyChallenge.fromJson(Map<String, dynamic> json){ 
        return DailyChallenge(
            quizCategoryId: json["quiz_category_id"],
            quizTypeId: json["quiz_type_id"],
            totalXp: json["total_xp"],
            totalCoins: json["total_coins"],
            challengeReset: json["challenge_reset"],
            isPlayed: json["is_played"],
        );
    }

    Map<String, dynamic> toJson() => {
        "quiz_category_id": quizCategoryId,
        "quiz_type_id": quizTypeId,
        "total_xp": totalXp,
        "total_coins": totalCoins,
        "challenge_reset": challengeReset,
        "is_played": isPlayed,
    };

}
