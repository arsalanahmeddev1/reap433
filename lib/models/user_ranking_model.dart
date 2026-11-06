class UserRankingModel {
    UserRankingModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory UserRankingModel.fromJson(Map<String, dynamic> json){ 
        return UserRankingModel(
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
        required this.users,
    });

    final List<User> users;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            users: json["users"] == null ? [] : List<User>.from(json["users"]!.map((x) => User.fromJson(x))),
        );
    }

    Map<String, dynamic> toJson() => {
        "users": users.map((x) => x?.toJson()).toList(),
    };

}

class User {
    User({
        required this.id,
        required this.name,
        required this.profileImage,
        required this.rank,
        required this.totalXp,
        required this.totalCoins,
        required this.provider,
        required this.currentUser,
        required this.totalStreak,
    });

    final int? id;
    final String? name;
    final String? profileImage;
    final int? rank;
    final int? totalXp;
    final int? totalCoins;
    final String? provider;
    final bool? currentUser;
    final int? totalStreak;

    factory User.fromJson(Map<String, dynamic> json){ 
        return User(
            id: json["id"],
            name: json["name"],
            profileImage: json["profile_image"],
            rank: json["rank"],
            totalXp: json["total_xp"],
            totalCoins: json["total_coins"],
            provider: json["provider"],
            currentUser: json["current_user"],
            totalStreak: json["total_streak"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "name": name,
        "profile_image": profileImage,
        "rank": rank,
        "total_xp": totalXp,
        "total_coins": totalCoins,
        "provider": provider,
        "current_user": currentUser,
        "total_streak": totalStreak,
    };

}
