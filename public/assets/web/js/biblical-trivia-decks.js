/**
 * Reap433 Bible Trivia — deck data (from Claude artifact)
 * https://claude.ai/public/artifacts/e35df8f7-cee8-4849-8404-61745b6535d2
 */
(function (global) {
    'use strict';

    global.BiblicalTriviaDecks = {
        DECK_ORDER:         [
                "reapsow",
                "faith",
                "baptism",
                "tithing",
                "salvation",
                "holyspirit",
                "gifts"
        ],
        DECKS: {
    "faith": {
        "title": "Faith",
        "color": "#5b7c4f",
        "verse": "“Now faith is the substance of things hoped for, the evidence of things not seen.”",
        "ref": "Hebrews 11:1",
        "desc": "Believing, trusting, walking by what is unseen",
        "cards": [
            {
                "q": "Hebrews 11:1 defines faith as the substance of things hoped for, and the evidence of...?",
                "options": [
                    "Things to come",
                    "Things not seen",
                    "Things believed",
                    "Things eternal"
                ],
                "answer": 1,
                "ref": "Hebrews 11:1",
                "note": "The classic biblical definition of faith — confidence in what cannot yet be seen."
            },
            {
                "q": "Romans 10:17 says faith comes by hearing, and hearing by what?",
                "options": [
                    "Prayer",
                    "The word of God",
                    "Tradition",
                    "The Spirit's voice"
                ],
                "answer": 1,
                "ref": "Romans 10:17",
                "note": "Faith is rooted in the proclaimed word, not abstract feeling."
            },
            {
                "q": "Ephesians 2:8 says salvation through faith is, itself, a ___ of God.",
                "options": [
                    "Mercy",
                    "Gift",
                    "Will",
                    "Promise"
                ],
                "answer": 1,
                "ref": "Ephesians 2:8-9",
                "note": "‘Not of works, lest any man should boast’ (v.9)."
            },
            {
                "q": "James 2:17 says faith without works is what?",
                "options": [
                    "Incomplete",
                    "Weak",
                    "Dead",
                    "Useless to others"
                ],
                "answer": 2,
                "ref": "James 2:17",
                "note": "‘Even so faith, if it hath not works, is dead, being alone.’"
            },
            {
                "q": "Jesus says if you have faith as a grain of ___, you could tell a mountain to move and it would obey.",
                "options": [
                    "Wheat",
                    "Mustard seed",
                    "Sand",
                    "Salt"
                ],
                "answer": 1,
                "ref": "Matthew 17:20",
                "note": "A picture of small faith producing outsized results through God's power."
            },
            {
                "q": "Hebrews 11:6 says without faith it is impossible to do what?",
                "options": [
                    "Pray",
                    "Please God",
                    "Receive grace",
                    "Enter heaven"
                ],
                "answer": 1,
                "ref": "Hebrews 11:6",
                "note": "‘For he that cometh to God must believe that he is, and that he is a rewarder of them that diligently seek him.’"
            },
            {
                "q": "Genesis 15:6 says Abraham believed the Lord, and it was counted to him as...?",
                "options": [
                    "Faithfulness",
                    "Righteousness",
                    "Wisdom",
                    "Blessing"
                ],
                "answer": 1,
                "ref": "Genesis 15:6",
                "note": "Foundational text Paul builds on in Romans 4 and Galatians 3 for justification by faith."
            },
            {
                "q": "Hebrews 11 highlights this figure for building an ark in faith before any rain had fallen.",
                "options": [
                    "Abraham",
                    "Noah",
                    "Moses",
                    "Enoch"
                ],
                "answer": 1,
                "ref": "Hebrews 11:7",
                "note": "Noah, ‘being warned of God... prepared an ark for the saving of his house.’"
            },
            {
                "q": "When Peter began to sink while walking on water, Jesus asked, ‘O thou of little faith, wherefore didst thou ___?’",
                "options": [
                    "Fear",
                    "Doubt",
                    "Fall",
                    "Sink"
                ],
                "answer": 1,
                "ref": "Matthew 14:31",
                "note": "Faith and doubt are pictured side by side in the same moment."
            },
            {
                "q": "2 Corinthians 5:7 says, ‘For we walk by faith, not by ___.’",
                "options": [
                    "Works",
                    "Sight",
                    "Law",
                    "Fear"
                ],
                "answer": 1,
                "ref": "2 Corinthians 5:7",
                "note": "A guiding posture for how believers are to move through life."
            },
            {
                "q": "1 John 5:4 says the victory that overcomes the world is...?",
                "options": [
                    "Love",
                    "Faith",
                    "Hope",
                    "Obedience"
                ],
                "answer": 1,
                "ref": "1 John 5:4",
                "note": "‘This is the victory that overcomes the world, even our faith.’"
            },
            {
                "q": "When the apostles asked Jesus to increase their faith, he said faith like a mustard seed could uproot what kind of tree?",
                "options": [
                    "A mountain",
                    "A fig tree",
                    "A sycamine tree",
                    "A cedar"
                ],
                "answer": 2,
                "ref": "Luke 17:5-6",
                "note": "A sycamine (mulberry-type) tree — known for an especially deep, stubborn root system."
            }
        ]
    },
    "baptism": {
        "title": "Baptism",
        "color": "#4a7c94",
        "verse": "“Therefore we are buried with him by baptism into death... that we also should walk in newness of life.”",
        "ref": "Romans 6:4",
        "desc": "Water, the Spirit, and being joined to Christ",
        "cards": [
            {
                "q": "At Jesus' baptism, what descended on him like a dove as a voice declared, ‘This is my beloved Son’?",
                "options": [
                    "An angel",
                    "Fire",
                    "The Holy Spirit",
                    "A cloud"
                ],
                "answer": 2,
                "ref": "Matthew 3:16-17",
                "note": "All three Persons are present at this moment — Father's voice, Son in the water, Spirit descending."
            },
            {
                "q": "In the Great Commission, Jesus commands baptism ‘in the name of the Father, and of the Son, and of the ___.’",
                "options": [
                    "Holy Spirit",
                    "Kingdom",
                    "Church",
                    "Covenant"
                ],
                "answer": 0,
                "ref": "Matthew 28:19",
                "note": "The baptismal formula tied to discipleship and the Great Commission."
            },
            {
                "q": "In Acts 2:38, Peter tells the crowd that those who repent and are baptized will receive what?",
                "options": [
                    "Healing",
                    "The gift of the Holy Spirit",
                    "Eternal life",
                    "A new name"
                ],
                "answer": 1,
                "ref": "Acts 2:38",
                "note": "Peter's Pentecost sermon ties repentance, baptism, and the Spirit together."
            },
            {
                "q": "Romans 6:4 says we are buried with Christ by baptism into death, so that we might walk in ___ of life.",
                "options": [
                    "Newness",
                    "Fullness",
                    "Assurance",
                    "Power"
                ],
                "answer": 0,
                "ref": "Romans 6:3-4",
                "note": "Baptism pictures union with Christ's death and resurrection."
            },
            {
                "q": "What did the Ethiopian eunuch see that prompted him to ask Philip, ‘What doth hinder me to be baptized?’",
                "options": [
                    "A river",
                    "A dove",
                    "Water",
                    "A baptismal font"
                ],
                "answer": 2,
                "ref": "Acts 8:36",
                "note": "A simple roadside moment — Philip had just explained Isaiah 53 to him."
            },
            {
                "q": "1 Peter 3:21 says baptism is not the removal of dirt from the flesh, but the answer of a good ___ toward God.",
                "options": [
                    "Heart",
                    "Conscience",
                    "Faith",
                    "Confession"
                ],
                "answer": 1,
                "ref": "1 Peter 3:21",
                "note": "Baptism is framed as an inward reality, not merely a physical washing."
            },
            {
                "q": "John the Baptist preached a baptism of ___ for the remission of sins.",
                "options": [
                    "Fire",
                    "Repentance",
                    "Water",
                    "Cleansing"
                ],
                "answer": 1,
                "ref": "Mark 1:4",
                "note": "John's baptism prepared the way; it pointed forward to Christ."
            },
            {
                "q": "When Paul met disciples at Ephesus who had only known John's baptism, he had them baptized into what?",
                "options": [
                    "The Father's name",
                    "The name of the Lord Jesus",
                    "The Holy Spirit's name",
                    "Repentance"
                ],
                "answer": 1,
                "ref": "Acts 19:1-5",
                "note": "A transition moment from John's preparatory baptism to baptism in Jesus' name."
            },
            {
                "q": "Galatians 3:27 says as many as have been baptized into Christ have ‘put on’ what?",
                "options": [
                    "The armor of God",
                    "Christ",
                    "The Spirit",
                    "Righteousness"
                ],
                "answer": 1,
                "ref": "Galatians 3:27",
                "note": "Clothing imagery — identity is now wrapped up in Christ."
            },
            {
                "q": "In the story of Cornelius, what notable thing happened BEFORE he and his household were baptized?",
                "options": [
                    "They fasted",
                    "The Holy Spirit fell on them",
                    "An angel appeared to Peter",
                    "They were circumcised"
                ],
                "answer": 1,
                "ref": "Acts 10:44-48",
                "note": "Surprising even to Peter — the Spirit came on Gentile believers ahead of water baptism."
            },
            {
                "q": "1 Peter 3:20 compares baptism to the salvation of how many people through water in Noah's day?",
                "options": [
                    "Two",
                    "Seven",
                    "Eight",
                    "Twelve"
                ],
                "answer": 2,
                "ref": "1 Peter 3:20-21",
                "note": "Noah plus his seven family members — eight souls saved through the flood waters."
            },
            {
                "q": "Ephesians 4:5 lists ‘one Lord, one faith, one ___’ among the marks of unity in the body.",
                "options": [
                    "Spirit",
                    "Baptism",
                    "Hope",
                    "Church"
                ],
                "answer": 1,
                "ref": "Ephesians 4:5",
                "note": "Baptism is named as one of the unifying realities of the church."
            }
        ]
    },
    "tithing": {
        "title": "Tithing",
        "color": "#c9962f",
        "verse": "“Bring ye all the tithes into the storehouse... and prove me now herewith, saith the Lord of hosts.”",
        "ref": "Malachi 3:10",
        "desc": "Firstfruits, generosity, and the heart behind giving",
        "cards": [
            {
                "q": "After defeating his enemies, Abram gave a tenth of everything to whom?",
                "options": [
                    "Lot",
                    "Melchizedek",
                    "The king of Sodom",
                    "Pharaoh"
                ],
                "answer": 1,
                "ref": "Genesis 14:18-20",
                "note": "Melchizedek, priest-king of Salem — later referenced again in Hebrews 7."
            },
            {
                "q": "At Bethel, Jacob vowed that if God brought him back safely, he would give what portion back to God?",
                "options": [
                    "A fifth",
                    "A tenth",
                    "Half",
                    "All his flocks"
                ],
                "answer": 1,
                "ref": "Genesis 28:20-22",
                "note": "One of the earliest personal tithing vows recorded in Scripture."
            },
            {
                "q": "Leviticus 27:30 says all the tithe of the land, whether seed or fruit, belongs to whom?",
                "options": [
                    "The priest",
                    "The king",
                    "The Lord",
                    "The Levite"
                ],
                "answer": 2,
                "ref": "Leviticus 27:30",
                "note": "‘It is holy unto the Lord’ — the tithe is set apart by definition."
            },
            {
                "q": "Malachi 3:10 says if Israel brings the tithe into the storehouse, God will open what?",
                "options": [
                    "The gates of heaven",
                    "The windows of heaven",
                    "His treasury",
                    "A new covenant"
                ],
                "answer": 1,
                "ref": "Malachi 3:10",
                "note": "A promise of blessing ‘poured out’ in response to obedience and trust."
            },
            {
                "q": "Malachi 3:8 accuses Israel of robbing God in what two things?",
                "options": [
                    "Tithes and offerings",
                    "Sacrifices and prayers",
                    "Sabbaths and feasts",
                    "Firstfruits and vows"
                ],
                "answer": 0,
                "ref": "Malachi 3:8",
                "note": "A sharp indictment that frames withheld giving as theft from God."
            },
            {
                "q": "Jesus rebukes the Pharisees for tithing mint, dill, and cumin while neglecting these ‘weightier matters’:",
                "options": [
                    "Prayer and fasting",
                    "Justice, mercy, and faithfulness",
                    "Sacrifice and worship",
                    "Love and humility"
                ],
                "answer": 1,
                "ref": "Matthew 23:23",
                "note": "Jesus doesn't dismiss tithing — he calls out misplaced priorities around it."
            },
            {
                "q": "Hebrews 7 argues that Levi (whose line collects tithes) effectively paid tithes to Melchizedek through whom?",
                "options": [
                    "Isaac",
                    "Jacob",
                    "Abraham",
                    "Esau"
                ],
                "answer": 2,
                "ref": "Hebrews 7:1-10",
                "note": "The argument: Levi was ‘in the loins’ of Abraham when Abraham tithed to Melchizedek."
            },
            {
                "q": "2 Corinthians 9:7 says God loves a giver who gives how?",
                "options": [
                    "Generously",
                    "Cheerfully",
                    "Sacrificially",
                    "Anonymously"
                ],
                "answer": 1,
                "ref": "2 Corinthians 9:7",
                "note": "‘Not grudgingly, or of necessity: for God loveth a cheerful giver.’"
            },
            {
                "q": "In Luke 11:42, Jesus says the Pharisees tithe even garden herbs but pass over what?",
                "options": [
                    "The poor",
                    "Judgment and the love of God",
                    "The Sabbath",
                    "The temple offering"
                ],
                "answer": 1,
                "ref": "Luke 11:42",
                "note": "Another instance of the same theme — ritual without relationship."
            },
            {
                "q": "Deuteronomy 14:23 says the tithe of grain, wine, and oil was to be eaten where?",
                "options": [
                    "At home",
                    "In the field",
                    "Before the Lord at the chosen place",
                    "At the city gate"
                ],
                "answer": 2,
                "ref": "Deuteronomy 14:22-23",
                "note": "A communal, worshipful meal — ‘that thou mayest learn to fear the Lord.’"
            },
            {
                "q": "Numbers 18:21 says the tithe in Israel was given to the Levites as their inheritance for their service in...?",
                "options": [
                    "The temple",
                    "The tabernacle of the congregation",
                    "The synagogue",
                    "The priesthood alone"
                ],
                "answer": 1,
                "ref": "Numbers 18:21",
                "note": "The tithe funded those set apart for ongoing service, since they had no land inheritance."
            },
            {
                "q": "In Mark 12, the widow's two small coins mattered more to Jesus because she gave...?",
                "options": [
                    "Out of her surplus",
                    "Out of her poverty, all she had",
                    "In secret",
                    "With a vow"
                ],
                "answer": 1,
                "ref": "Mark 12:41-44",
                "note": "‘She of her want did cast in all that she had, even all her living.’"
            }
        ]
    },
    "salvation": {
        "title": "Salvation",
        "color": "#a8552c",
        "verse": "“For God so loved the world, that he gave his only begotten Son, that whosoever believeth in him should not perish, but have everlasting life.”",
        "ref": "John 3:16",
        "desc": "Grace, faith, and the work of Christ",
        "cards": [
            {
                "q": "John 3:16 says whoever believes in the Son should not perish but have...?",
                "options": [
                    "Peace",
                    "Everlasting life",
                    "Forgiveness",
                    "Joy"
                ],
                "answer": 1,
                "ref": "John 3:16",
                "note": "Perhaps the most quoted verse summarizing the gospel message."
            },
            {
                "q": "Romans 10:9 says if you confess ‘Jesus is Lord’ and believe God did what, you will be saved.",
                "options": [
                    "Sent him to earth",
                    "Raised him from the dead",
                    "Forgave your sins",
                    "Anointed him king"
                ],
                "answer": 1,
                "ref": "Romans 10:9",
                "note": "Confession and belief in the resurrection are tied together here."
            },
            {
                "q": "Ephesians 2:9 says salvation is ‘not of works, lest any man should ___.’",
                "options": [
                    "Despair",
                    "Doubt",
                    "Boast",
                    "Fall"
                ],
                "answer": 2,
                "ref": "Ephesians 2:8-9",
                "note": "Grace removes any basis for human pride in salvation."
            },
            {
                "q": "Acts 4:12 says there is salvation in no other name, for there is no other name under heaven given among men by which we must be...?",
                "options": [
                    "Forgiven",
                    "Saved",
                    "Justified",
                    "Known"
                ],
                "answer": 1,
                "ref": "Acts 4:12",
                "note": "Peter's bold declaration before the religious council."
            },
            {
                "q": "Romans 6:23 contrasts the wages of sin (death) with the free gift of God, which is...?",
                "options": [
                    "Grace",
                    "Eternal life through Jesus Christ",
                    "Peace",
                    "Righteousness"
                ],
                "answer": 1,
                "ref": "Romans 6:23",
                "note": "A wage is earned; a gift is received — the contrast is deliberate."
            },
            {
                "q": "Titus 3:5 says God saved us ‘not by works of righteousness which we have done, but according to his ___.’",
                "options": [
                    "Plan",
                    "Mercy",
                    "Justice",
                    "Covenant"
                ],
                "answer": 1,
                "ref": "Titus 3:5",
                "note": "Mercy, not merit, is the basis for salvation."
            },
            {
                "q": "In John 14:6, Jesus says, ‘I am the way, the truth, and the life: no man cometh unto the Father, but by...?’",
                "options": [
                    "Faith",
                    "Me",
                    "The Spirit",
                    "The Law"
                ],
                "answer": 1,
                "ref": "John 14:6",
                "note": "One of Jesus' clearest statements about exclusivity of access to the Father."
            },
            {
                "q": "Romans 5:8 says God demonstrated his love for us in that, while we were still sinners, Christ did what?",
                "options": [
                    "Prayed for us",
                    "Died for us",
                    "Healed us",
                    "Called us"
                ],
                "answer": 1,
                "ref": "Romans 5:8",
                "note": "Love demonstrated before any change of heart on humanity's part."
            },
            {
                "q": "Philippians 2:12 instructs believers to work out their own salvation with fear and...?",
                "options": [
                    "Joy",
                    "Trembling",
                    "Faith",
                    "Patience"
                ],
                "answer": 1,
                "ref": "Philippians 2:12",
                "note": "A call to take the lived-out reality of salvation seriously."
            },
            {
                "q": "When the Philippian jailer asked what he must do to be saved, Paul and Silas told him to...?",
                "options": [
                    "Be baptized first",
                    "Repent and pray",
                    "Believe on the Lord Jesus Christ",
                    "Keep the commandments"
                ],
                "answer": 2,
                "ref": "Acts 16:30-31",
                "note": "‘...and thou shalt be saved, and thy house.’"
            },
            {
                "q": "Romans 3:23 says all have sinned and fall short of...?",
                "options": [
                    "The law",
                    "The glory of God",
                    "God's mercy",
                    "The covenant"
                ],
                "answer": 1,
                "ref": "Romans 3:23",
                "note": "Sets up the need for the grace described later in Romans."
            },
            {
                "q": "Hebrews 9:22 says that without the shedding of blood there is no...?",
                "options": [
                    "Atonement",
                    "Covenant",
                    "Remission",
                    "Sacrifice"
                ],
                "answer": 2,
                "ref": "Hebrews 9:22",
                "note": "‘...and almost all things are by the law purged with blood; and without shedding of blood is no remission.’"
            }
        ]
    },
    "holyspirit": {
        "title": "Holy Spirit",
        "color": "#6f5a96",
        "verse": "“But ye shall receive power, after that the Holy Ghost is come upon you... and ye shall be witnesses unto me.”",
        "ref": "Acts 1:8",
        "desc": "The Comforter, the fire, and life in the Spirit",
        "cards": [
            {
                "q": "On the Day of Pentecost, what appeared on the believers as the Holy Spirit filled them?",
                "options": [
                    "A bright light",
                    "Cloven tongues like fire",
                    "A dove",
                    "Oil"
                ],
                "answer": 1,
                "ref": "Acts 2:1-4",
                "note": "A visible, fiery sign accompanying the Spirit's outpouring."
            },
            {
                "q": "In John 14:26, Jesus calls the Holy Spirit the Comforter who will do what?",
                "options": [
                    "Judge the world",
                    "Teach all things and bring remembrance",
                    "Replace Jesus",
                    "Reveal the future only"
                ],
                "answer": 1,
                "ref": "John 14:26",
                "note": "‘...and bring all things to your remembrance, whatsoever I have said unto you.’"
            },
            {
                "q": "John 16:13 says the Spirit of truth will guide believers into all truth, speaking whatever he...?",
                "options": [
                    "Wills",
                    "Hears",
                    "Knows",
                    "Decides"
                ],
                "answer": 1,
                "ref": "John 16:13",
                "note": "‘...for he shall not speak of himself; but whatsoever he shall hear, that shall he speak.’"
            },
            {
                "q": "Romans 8:26 says the Spirit helps in our weakness, interceding for us with...?",
                "options": [
                    "Words of wisdom",
                    "Groanings that cannot be uttered",
                    "Tongues of angels",
                    "Silent prayers"
                ],
                "answer": 1,
                "ref": "Romans 8:26",
                "note": "A picture of the Spirit's intercession even when we lack words."
            },
            {
                "q": "Which of these is NOT listed among the fruit of the Spirit in Galatians 5:22-23?",
                "options": [
                    "Love, joy, peace",
                    "Patience, kindness, goodness",
                    "Faithfulness, gentleness, self-control",
                    "Wisdom, knowledge, prophecy"
                ],
                "answer": 3,
                "ref": "Galatians 5:22-23",
                "note": "Those three appear instead among the spiritual gifts in 1 Corinthians 12."
            },
            {
                "q": "1 Corinthians 3:16 asks, ‘Know ye not that ye are the temple of God, and that the Spirit of God dwells...?’",
                "options": [
                    "Among you",
                    "In you",
                    "Around you",
                    "Within the church only"
                ],
                "answer": 1,
                "ref": "1 Corinthians 3:16",
                "note": "The Spirit's dwelling place is described in deeply personal terms."
            },
            {
                "q": "Acts 1:8 says believers will be witnesses unto the uttermost part of...?",
                "options": [
                    "Jerusalem",
                    "Judea",
                    "The earth",
                    "Israel"
                ],
                "answer": 2,
                "ref": "Acts 1:8",
                "note": "Jesus traces an outward arc: Jerusalem, Judea, Samaria, then the ends of the earth."
            },
            {
                "q": "Genesis 1:2 says the Spirit of God did what over the face of the waters?",
                "options": [
                    "Spoke",
                    "Moved (hovered)",
                    "Shone",
                    "Rested"
                ],
                "answer": 1,
                "ref": "Genesis 1:2",
                "note": "The Spirit's presence is woven into the creation account from the start."
            },
            {
                "q": "In Luke 1:35, the angel tells Mary the Holy Spirit would do what to her?",
                "options": [
                    "Anoint her",
                    "Come upon her, overshadowing her",
                    "Speak through her",
                    "Heal her"
                ],
                "answer": 1,
                "ref": "Luke 1:35",
                "note": "‘...the power of the Highest shall overshadow thee.’"
            },
            {
                "q": "Ephesians 4:30 says believers should not do what to the Holy Spirit?",
                "options": [
                    "Ignore him",
                    "Grieve him",
                    "Test him",
                    "Resist him"
                ],
                "answer": 1,
                "ref": "Ephesians 4:30",
                "note": "Connected to how believers treat one another — sealed for the day of redemption."
            },
            {
                "q": "1 Corinthians 12:13 says all believers were baptized by one Spirit into one...?",
                "options": [
                    "Faith",
                    "Body",
                    "Church",
                    "Covenant"
                ],
                "answer": 1,
                "ref": "1 Corinthians 12:13",
                "note": "Jew or Greek, bond or free — unified by the same Spirit into one body."
            },
            {
                "q": "Zechariah 4:6 says, ‘Not by might, nor by power, but by my ___, saith the Lord of hosts.’",
                "options": [
                    "Word",
                    "Spirit",
                    "Hand",
                    "Covenant"
                ],
                "answer": 1,
                "ref": "Zechariah 4:6",
                "note": "A reminder that God's work is ultimately accomplished by his Spirit, not human strength."
            }
        ]
    },
    "gifts": {
        "title": "Spiritual Gifts",
        "color": "#b8763f",
        "verse": "“Now there are diversities of gifts, but the same Spirit.”",
        "ref": "1 Corinthians 12:4",
        "desc": "How the Spirit equips the body to serve",
        "cards": [
            {
                "q": "Which of these IS listed among the spiritual gifts in 1 Corinthians 12:8-10?",
                "options": [
                    "Discerning of spirits",
                    "Patience",
                    "Generosity",
                    "Hospitality"
                ],
                "answer": 0,
                "ref": "1 Corinthians 12:8-10",
                "note": "The fuller list includes wisdom, knowledge, faith, healing, miracles, prophecy, discerning of spirits, tongues, and interpretation of tongues."
            },
            {
                "q": "Romans 12 lists prophecy, ministry, teaching, exhortation, and giving — which pair completes the list?",
                "options": [
                    "Leadership and mercy",
                    "Healing and tongues",
                    "Wisdom and faith",
                    "Apostleship and evangelism"
                ],
                "answer": 0,
                "ref": "Romans 12:6-8",
                "note": "‘He that ruleth, with diligence; he that sheweth mercy, with cheerfulness.’"
            },
            {
                "q": "Ephesians 4:11 lists apostles, prophets, evangelists, and which two roles for equipping the church?",
                "options": [
                    "Pastors and teachers",
                    "Elders and deacons",
                    "Priests and Levites",
                    "Bishops and overseers"
                ],
                "answer": 0,
                "ref": "Ephesians 4:11",
                "note": "Often called the ‘fivefold ministry’ — given for building up the body."
            },
            {
                "q": "1 Corinthians 12:4 says there are diversities of gifts, but the same...?",
                "options": [
                    "Calling",
                    "Spirit",
                    "Church",
                    "Purpose"
                ],
                "answer": 1,
                "ref": "1 Corinthians 12:4-6",
                "note": "Verses 4-6 repeat the pattern: same Spirit, same Lord, same God."
            },
            {
                "q": "1 Corinthians 13 says that without love, speaking with the tongues of men and angels makes a person like what?",
                "options": [
                    "A withered branch",
                    "Sounding brass or a tinkling cymbal",
                    "An empty vessel",
                    "A clouded mirror"
                ],
                "answer": 1,
                "ref": "1 Corinthians 13:1",
                "note": "Gifts without love are described as noise rather than substance."
            },
            {
                "q": "1 Peter 4:10 says believers should use their gift to serve others as good stewards of the ___ of God.",
                "options": [
                    "Manifold grace",
                    "Mighty power",
                    "Holy calling",
                    "Eternal kingdom"
                ],
                "answer": 0,
                "ref": "1 Peter 4:10",
                "note": "Gifts are framed as grace entrusted for the benefit of others."
            },
            {
                "q": "1 Corinthians 14:1 urges believers to follow love and desire spiritual gifts, especially that they may...?",
                "options": [
                    "Speak in tongues",
                    "Prophesy",
                    "Heal the sick",
                    "Lead the church"
                ],
                "answer": 1,
                "ref": "1 Corinthians 14:1",
                "note": "Paul prioritizes intelligible, edifying communication for the gathered church."
            },
            {
                "q": "2 Timothy 1:6 urges Timothy to ‘stir up the gift of God’ given to him through what act?",
                "options": [
                    "Baptism",
                    "The laying on of Paul's hands",
                    "Prayer and fasting",
                    "His mother's teaching"
                ],
                "answer": 1,
                "ref": "2 Timothy 1:6",
                "note": "A reminder that gifts may need active stewardship, not just possession."
            },
            {
                "q": "At Pentecost, the believers spoke with other tongues as the Spirit gave them...?",
                "options": [
                    "Wisdom",
                    "Boldness",
                    "Utterance",
                    "Authority"
                ],
                "answer": 2,
                "ref": "Acts 2:4",
                "note": "‘...and began to speak with other tongues, as the Spirit gave them utterance.’"
            },
            {
                "q": "When the apostles chose seven men to serve tables (including Stephen), what two qualities did they look for?",
                "options": [
                    "Wisdom and the Spirit",
                    "Wealth and influence",
                    "Age and experience",
                    "Education and eloquence"
                ],
                "answer": 0,
                "ref": "Acts 6:1-6",
                "note": "‘Men of honest report, full of the Holy Ghost and wisdom.’"
            },
            {
                "q": "1 Corinthians 12 asks: if the whole body were an eye, where would the sense of hearing be?",
                "options": [
                    "In the hand",
                    "Nowhere",
                    "In the foot",
                    "In the heart"
                ],
                "answer": 1,
                "ref": "1 Corinthians 12:17",
                "note": "An illustration of why every member and gift in the body is needed."
            },
            {
                "q": "1 Corinthians 12:11 says all these gifts are worked by one and the same Spirit, dividing to every man as he...?",
                "options": [
                    "Sees fit",
                    "Wills",
                    "Determines is needed",
                    "Has planned"
                ],
                "answer": 1,
                "ref": "1 Corinthians 12:11",
                "note": "‘...dividing to every man severally as he will’ — the Spirit's sovereign distribution."
            }
        ]
    },
    "reapsow": {
        "title": "Reap What You Sow",
        "color": "#3f6b6b",
        "verse": "“Be not deceived; God is not mocked: for whatsoever a man soweth, that shall he also reap.”",
        "ref": "Galatians 6:7",
        "desc": "Sowing, reaping, and the harvest principle",
        "cards": [
            {
                "q": "Complete Galatians 6:7 — ‘Whatever a man sows, ____.’",
                "options": [
                    "that he shall also reap",
                    "so shall he gain",
                    "shall be multiplied",
                    "shall be forgiven"
                ],
                "answer": 0,
                "ref": "Galatians 6:7",
                "note": "Paul's core principle: sowing and reaping correspond — God is not mocked."
            },
            {
                "q": "In Hosea, what does Israel sow that produces a corrupt harvest?",
                "options": [
                    "The wind",
                    "Wickedness",
                    "Wheat among thorns",
                    "Bread without yeast"
                ],
                "answer": 1,
                "ref": "Hosea 8:7 / 10:13",
                "note": "‘They have sown the wind, and they shall reap the whirlwind’ (8:7); ‘You have plowed wickedness, you have reaped injustice’ (10:13)."
            },
            {
                "q": "Jesus' Parable of the Sower describes seed falling on how many types of ground?",
                "options": [
                    "Two",
                    "Three",
                    "Four",
                    "Five"
                ],
                "answer": 2,
                "ref": "Matthew 13:1-23 / Mark 4 / Luke 8",
                "note": "Path, rocky ground, thorns, and good soil — each representing a heart's response to the word."
            },
            {
                "q": "2 Corinthians 9:6 says the one who sows ___ will also reap ___.",
                "options": [
                    "quietly / silently",
                    "sparingly / sparingly",
                    "bountifully / bountifully",
                    "in tears / in tears"
                ],
                "answer": 2,
                "ref": "2 Corinthians 9:6",
                "note": "Used in context of generous giving — a cheerful, generous sower reaps generously."
            },
            {
                "q": "Which Old Testament figure deceived his father Isaac to reap a blessing he hadn't earned — and later reaped deception himself through his own sons?",
                "options": [
                    "Esau",
                    "Jacob",
                    "Laban",
                    "Reuben"
                ],
                "answer": 1,
                "ref": "Genesis 27; Genesis 37",
                "note": "Jacob deceived Isaac for the blessing; years later his sons deceived him about Joseph's ‘death’ — a sowing-and-reaping echo many commentators note."
            },
            {
                "q": "Galatians 6:8 contrasts sowing to the ___ versus sowing to the ___.",
                "options": [
                    "body / soul",
                    "flesh / Spirit",
                    "world / church",
                    "law / grace"
                ],
                "answer": 1,
                "ref": "Galatians 6:8",
                "note": "Sowing to the flesh reaps corruption/death; sowing to the Spirit reaps everlasting life."
            },
            {
                "q": "King David sowed adultery and murder with Bathsheba and Uriah. What did Nathan the prophet say would never depart from his house as a result?",
                "options": [
                    "Famine",
                    "The sword",
                    "Exile",
                    "Leprosy"
                ],
                "answer": 1,
                "ref": "2 Samuel 12:10",
                "note": "‘The sword shall never depart from your house’ — David reaped ongoing family violence (Amnon, Absalom, etc.)."
            },
            {
                "q": "Proverbs 22:8 says he who sows iniquity will reap what?",
                "options": [
                    "Sorrow",
                    "Calamity/vanity",
                    "Poverty",
                    "Shame"
                ],
                "answer": 1,
                "ref": "Proverbs 22:8",
                "note": "‘He who sows iniquity will reap sorrow/vanity, and the rod of his fury will fail’ (translations vary between ‘sorrow’ and ‘calamity’)."
            },
            {
                "q": "Job's friend Eliphaz argues that those who plow iniquity and sow trouble will reap what?",
                "options": [
                    "Peace",
                    "The same",
                    "Riches",
                    "Nothing at all"
                ],
                "answer": 1,
                "ref": "Job 4:8",
                "note": "‘Those who plow iniquity and sow trouble reap the same’ — though Job's story complicates simplistic reap-sow logic regarding suffering."
            },
            {
                "q": "In Matthew 7, Jesus says you will know false prophets by what?",
                "options": [
                    "Their words",
                    "Their fruits",
                    "Their followers",
                    "Their wealth"
                ],
                "answer": 1,
                "ref": "Matthew 7:15-20",
                "note": "‘By their fruits you shall know them’ — a thorn bush cannot produce figs; the harvest reveals the tree."
            },
            {
                "q": "Jeremiah 4:3 commands Judah to do what to their fallow ground before sowing?",
                "options": [
                    "Water it",
                    "Break it up",
                    "Burn it",
                    "Leave it untouched"
                ],
                "answer": 1,
                "ref": "Jeremiah 4:3",
                "note": "‘Break up your fallow ground, and do not sow among thorns’ — a call to repentance before renewed obedience."
            },
            {
                "q": "In the Parable of the Sower, what does the seed represent?",
                "options": [
                    "Faith",
                    "The Holy Spirit",
                    "The word of God",
                    "Money"
                ],
                "answer": 2,
                "ref": "Luke 8:11",
                "note": "‘The seed is the word of God’ — the soils represent the conditions of the heart that receive it."
            },
            {
                "q": "James 3:18 says the fruit of righteousness is sown in what?",
                "options": [
                    "Truth",
                    "Peace",
                    "Faith",
                    "Suffering"
                ],
                "answer": 1,
                "ref": "James 3:18",
                "note": "‘The fruit of righteousness is sown in peace by those who make peace.’"
            },
            {
                "q": "Hosea 10:12 urges Israel to sow what for themselves and reap mercy?",
                "options": [
                    "Righteousness",
                    "Justice",
                    "Kindness",
                    "Faithfulness"
                ],
                "answer": 0,
                "ref": "Hosea 10:12",
                "note": "‘Sow for yourselves righteousness, reap in mercy; break up your fallow ground.’"
            },
            {
                "q": "Galatians 6:9 says believers should not grow weary in doing good, for in due season they shall ___.",
                "options": [
                    "be rewarded",
                    "reap",
                    "be blessed",
                    "rest"
                ],
                "answer": 1,
                "ref": "Galatians 6:9",
                "note": "‘Let us not be weary in well doing: for in due season we shall reap, if we faint not.’"
            },
            {
                "q": "Ecclesiastes 11:1 says to cast your bread upon the waters, for ___.",
                "options": [
                    "it shall return as gold",
                    "you shall find it after many days",
                    "the fish will multiply it",
                    "the wind will scatter it"
                ],
                "answer": 1,
                "ref": "Ecclesiastes 11:1",
                "note": "A call to generous, faith-filled action whose results may not be seen immediately."
            },
            {
                "q": "Psalm 126:5 says those who sow in ___ shall reap in joy.",
                "options": [
                    "faith",
                    "tears",
                    "darkness",
                    "secret"
                ],
                "answer": 1,
                "ref": "Psalm 126:5",
                "note": "‘Those who sow in tears shall reap in joy’ — a promise tied to restoration after hardship."
            },
            {
                "q": "John 4:37 quotes a saying: ‘One sows and another ___.’",
                "options": [
                    "waits",
                    "reaps",
                    "plants",
                    "waters"
                ],
                "answer": 1,
                "ref": "John 4:37-38",
                "note": "Jesus tells the disciples they reap what others (prophets, John the Baptist) labored to sow."
            },
            {
                "q": "Which book of the Bible centers on a Moabite widow who gleans leftover grain in Boaz's field — itself a picture of provision following loyalty ‘sown’ to Naomi?",
                "options": [
                    "Esther",
                    "Ruth",
                    "Judges",
                    "Lamentations"
                ],
                "answer": 1,
                "ref": "Ruth 2",
                "note": "Ruth's sown loyalty to Naomi is reaped in Boaz's kindness — and ultimately in the lineage of David."
            },
            {
                "q": "Which agricultural law in Leviticus 19 instructs farmers not to reap to the very edges of their field, leaving margin for the poor?",
                "options": [
                    "The Sabbath year",
                    "Gleaning law",
                    "Jubilee",
                    "First fruits offering"
                ],
                "answer": 1,
                "ref": "Leviticus 19:9-10",
                "note": "Built-in generosity to the harvest system — provision for the poor and the sojourner."
            }
        ]
    }
},
    };
}(typeof window !== 'undefined' ? window : this));
