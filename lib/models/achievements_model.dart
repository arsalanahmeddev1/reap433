class AchievementsModel {
    AchievementsModel({
        required this.success,
        required this.code,
        required this.message,
        required this.data,
    });

    final bool? success;
    final int? code;
    final String? message;
    final Data? data;

    factory AchievementsModel.fromJson(Map<String, dynamic> json){ 
        return AchievementsModel(
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
        required this.achievements,
    });

    final List<Achievement> achievements;

    factory Data.fromJson(Map<String, dynamic> json){ 
        return Data(
            achievements: json["achievements"] == null ? [] : List<Achievement>.from(json["achievements"]!.map((x) => Achievement.fromJson(x))),
        );
    }

    Map<String, dynamic> toJson() => {
        "achievements": achievements.map((x) => x?.toJson()).toList(),
    };

}

class Achievement {
    Achievement({
        required this.id,
        required this.title,
        required this.slug,
        required this.imageUrl,
        required this.description,
        required this.xp,
        required this.coins,
        required this.status,
        required this.isCompleted,
        required this.isClaim,
    });

    final int? id;
    final String? title;
    final String? slug;
    final String? imageUrl;
    final String? description;
    final int? xp;
    final int? coins;
    final String? status;
    final int? isCompleted;
    final bool? isClaim;

    factory Achievement.fromJson(Map<String, dynamic> json){ 
        return Achievement(
            id: json["id"],
            title: json["title"],
            slug: json["slug"],
            imageUrl: json["image_url"],
            description: json["description"],
            xp: json["xp"],
            coins: json["coins"],
            status: json["status"],
            isCompleted: json["is_completed"],
            isClaim: json["is_claim"],
        );
    }

    Map<String, dynamic> toJson() => {
        "id": id,
        "title": title,
        "slug": slug,
        "image_url": imageUrl,
        "description": description,
        "xp": xp,
        "coins": coins,
        "status": status,
        "is_completed": isCompleted,
        "is_claim": isClaim,
    };

}
