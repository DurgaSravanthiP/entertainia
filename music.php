<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: index.php");
    exit();
}

// Prevent cached access after logout
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");

require 'db_connect.php';

$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';
$backTarget = ($role === 'admin') ? 'admin_dashboard.php' : 'selection.php';

// Resolve user id
$userId = null;
$userStmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
$userStmt->bind_param("s", $username);
$userStmt->execute();
$userStmt->bind_result($userId);
$userStmt->fetch();
$userStmt->close();

$initialFavorites = [];
if (!empty($userId)) {
    $favStmt = $conn->prepare("SELECT movie_title AS movieTitle, song_title AS songTitle, artist AS songArtist, poster, embed_url AS embed FROM favorites WHERE user_id = ? ORDER BY created_at DESC");
    $favStmt->bind_param("i", $userId);
    $favStmt->execute();
    $favResult = $favStmt->get_result();
    while ($row = $favResult->fetch_assoc()) {
        $initialFavorites[] = $row;
    }
    $favStmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Melody Toons - Playful Music Player</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="music.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <div class="top-nav">
        <div class="nav-left">
            <button class="nav-btn secondary" onclick="window.location.href='<?php echo $backTarget; ?>'">
                <?php echo ($role === 'admin') ? 'Back to Admin' : 'Back to Selection'; ?>
            </button>
        </div>
        <div class="nav-right">
            <span class="user-pill"><?php echo htmlspecialchars($username); ?></span>
            <button class="nav-btn" onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </div>
    <div class="main-container" id="mainContent">
        <h1 class="header">Melody Toons</h1>

        <!-- Search Bar and Favorites Button -->
        <div class="nav-container">
            <div class="search-container">
                <i class="fas fa-search search-icon"></i>
                <input type="text" class="search-bar" id="searchBar"
                    placeholder="Search for movies, songs or artists...">
            </div>
            <button class="favorites-btn" id="favoritesBtn">
                <i class="fas fa-heart"></i> My Favorites
            </button>
        </div>

        <!-- Movies Grid -->
        <div class="movies-grid" id="moviesGrid">
            <!-- Movies will be loaded here -->
        </div>

        <!-- Favorites Page -->
        <div class="favorites-page" id="favoritesPage">
            <button class="back-btn" id="backBtn">
                <i class="fas fa-arrow-left"></i> Back to Music
            </button>

            <h2 class="header" style="font-size: 3rem;">My Favorites</h2>

            <div class="favorites-grid" id="favoritesGrid">
                <!-- Favorites will be loaded here -->
            </div>

            <div class="empty-state" id="emptyFavorites" style="display: none;">
                <div class="empty-icon">
                    <i class="fas fa-heart-broken"></i>
                </div>
                <div class="empty-text">
                    You haven't added any favorites yet!
                </div>
                <button class="explore-btn" id="backToMusicBtn" style="padding: 12px 30px; font-size: 1.2rem;">
                    Explore Songs
                </button>
            </div>
        </div>
    </div>

    <!-- Music Player -->
    <div class="player-container" id="playerContainer">
        <div class="video-player">
            <div class="close-btn" id="closeBtn">
                <i class="fas fa-times"></i>
            </div>
            <div class="video-container">
                <iframe id="musicVideo" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Notification -->
    <div class="notification" id="notification">
        <i class="fas fa-bell"></i>
        <span>New songs added weekly!</span>
    </div>

    <!-- Floating Decorative Elements -->
    <div class="floating-element"
        style="top: 10%; left: 5%; background-image: url('https://img.icons8.com/color/96/000000/music.png');"></div>
    <div class="floating-element"
        style="top: 30%; right: 8%; background-image: url('https://img.icons8.com/color/96/000000/headphones.png');">
    </div>
    <div class="floating-element"
        style="bottom: 20%; left: 10%; background-image: url('https://img.icons8.com/color/96/000000/musical-notes.png');">
    </div>

    <script>
        // Movie data
        const movies = {
            manam: {
                title: "Manam",
                image: "https://images.ottplay.com/images/manam-official-poster-1716574495.jpg?width=1200&height=675&quality=50&impolicy=ottplay-202410&format=webp",
                year: "2014",
                category: "melody",
                songs: [
                    {
                        title: "Piyo Piyo Re",
                        artist: "Anup Rubens • 2014",
                        embed: "https://www.youtube.com/embed/UY22VIQM6PY?si=pzgKeA50IL57tHjL",
                        genre: "melody"
                    },
                    {
                        title: "Kanulanu Thaake",
                        artist: "Anup Rubens • 2014",
                        embed: "https://www.youtube.com/embed/WreHin-F3PI?si=fLobMQq8iZh-nZXf",
                        genre: "melody"
                    },
                    {
                        title: "Chinni Chinni Aasalu",
                        artist: "Anup Rubens • 2014",
                        embed: "https://www.youtube.com/embed/S74cV_L5u54?si=Iyo3_XWwCk2uAAQK",
                        genre: "melody"
                    },
                    {
                        title: "Kani Penchina Ma Ammake",
                        artist: "Anup Rubens • 2014",
                        embed: "https://www.youtube.com/embed/J2Bt3sE8Gmo?si=gVFL4KnR-kctW9Yo",
                        genre: "classical"
                    }
                ]
            },

            okkadu: {
                title: "Okkadu",
                image: "https://sund-images.sunnxt.com/10645/832x623_Okkadu_10645_933c7302-39ba-4355-a3ad-a41193c5b8ca.jpg",
                year: "2003",
                category: "mass",
                songs: [
                    {
                        title: "Hare Rama",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/f7R-3ySpDJw?si=lUFJLFHqr9M5to4e",
                        genre: "mass"
                    },
                    {
                        title: "Nuvvemaya Chesavo",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/aqXidwmKRLU?si=4pSnYBfljCdkvxG3",
                        genre: "melody"
                    },
                    {
                        title: "Cheppave Chirugaali",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/M34DMAUWiXY?si=GyGmrrsq4Nj0_-Mo",
                        genre: "mass"
                    },
                    {
                        title: "Hay Rey Hai",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/V7zOE4QBhHU?si=_DFQl2zaMwoOrqsV",
                        genre: "electronic"
                    },
                    {
                        title: "Attarintiki",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/9J-0pEEfWxQ?si=zPI-2OiADJtWmxNo",
                        genre: "melody"
                    },
                    {
                        title: "Sahasam",
                        artist: "Mani Sharma • 2003",
                        embed: "https://www.youtube.com/embed/Gtorma7Ih70?si=bc97hoHc0tfer-Yl",
                        genre: "melody"
                    }
                ]
            },
            julayi: {
                title: "Julayi",
                image: "https://files.prokerala.com/movies/pics/800/view-10702.jpg",
                year: "2012",
                category: "mass",
                songs: [
                    {
                        title: "Chakkani Bike Undhi",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/58fsfc1qFHI?si=ovp0f1JFrOKEU6gZ",
                        genre: "mass"
                    },
                    {
                        title: "Julayi",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/FcY3-ZAfxbk?si=MoPQNUOsoMf3IpID",
                        genre: "pop"
                    },
                    {
                        title: "Mee Intiki Mundhu",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/zN5IpeGSCbk?si=IcH6tKYg-SbE6w6z",
                        genre: "mass"
                    },
                    {
                        title: "O Madhu",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/GaC01X8GEGI?si=s0AADqicaJWlSnX6",
                        genre: "melody"
                    },
                    {
                        title: "Osey Osey",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/H3vJyNBy56Q?si=9NBcfunXYjITOGsw",
                        genre: "electronic"
                    },
                    {
                        title: "Pakado Pakado",
                        artist: "Devi Sri Prasad • 2012",
                        embed: "https://www.youtube.com/embed/VaGlw05lMac?si=ytrB3tuJzOkf6-fw",
                        genre: "electronic"
                    }
                ]
            },
            nuvvosthanante: {
                title: "Nuvvosthanante Nenodhantaana",
                image: "https://resizing.flixster.com/1G-yxCHGs_47yk0a7fSz6VtBJCc=/fit-in/352x330/v2/https://resizing.flixster.com/-XZAfHZM39UwaGJIFWKAE8fS0ak=/v3/t/assets/p9821835_v_h9_aa.jpg",
                year: "2005",
                category: "melody",
                songs: [
                    {
                        title: "Niluvadhamu Ninu",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/fdEzDqiSC3U?si=-ZvTNWkKYN6y-A3b",
                        genre: "melody"
                    },
                    {
                        title: "Something Something",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/WXC4ScQ0YVg?si=LDTuzM5DCG6rvU95",
                        genre: "mass"
                    },
                    {
                        title: "Ghal Ghal Ghal",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/81fRS0FildI?si=ANZTm_IoagywDcye",
                        genre: "electronic"
                    },
                    {
                        title: "Chandrullo Unde",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/RIriENOmOpo?si=68jQnVaEU7mARNt8",
                        genre: "melody"
                    },
                    {
                        title: "Paripoke Pitta",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/hkcycMVAJnY?si=Q5Wz6hx0ycVCCIE_",
                        genre: "melody"
                    },
                    {
                        title: "Adire Adire",
                        artist: "Devi Sri Prasad • 2005",
                        embed: "https://www.youtube.com/embed/TXxRtcXJyuc?si=8Mc-vif0jmGkzc0V",
                        genre: "melody"
                    },
                ]
            },
            mirchi: {
                title: "Mirchi",
                image: "https://files.prokerala.com/movies/pics/800/movie-posters-17333.jpg",
                year: "2013",
                category: "mass",
                songs: [
                    {
                        title: "Barbie Girl",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/OZmn2TS6WbM?si=FYaynNFlwksVXKVW",
                        genre: "mass"
                    },
                    {
                        title: "Darlingey",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/5jDWeo2HHx8?si=aMgZE1CkvVNakEzp",
                        genre: "mass"
                    },
                    {
                        title: "Idhedho Bagundhe",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/VQ2-HPwxAZY?si=Cgj9kWWiSdBEo3oH",
                        genre: "melody"
                    },
                    {
                        title: "Mirchi",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/rQbL36wJ-nA?si=QssuVeDqwBLiXJLi",
                        genre: "pop"
                    },
                    {
                        title: "Pandagala",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/C153lEabO5k?si=jjAmrn9IlaSt_hqx",
                        genre: "mass"
                    },
                    {
                        title: "Yahoon Yahoon",
                        artist: "Devi Sri Prasad • 2013",
                        embed: "https://www.youtube.com/embed/bqbWbSJpkfI?si=eQcjzgJLBqVk7rm1",
                        genre: "mass"
                    }
                ]
            },
            baadshah: {
                title: "Baadshah",
                image: "https://i.pinimg.com/564x/72/9b/1f/729b1f3517fbd3fc370afa7df74c0735.jpg",
                year: "2013",
                category: "mass",
                songs: [
                    {
                        title: "Baadshah",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/6At2AhG0A4w?si=TCA2Mwj_gWf8aYCH",
                        genre: "mass"
                    },
                    {
                        title: "Banthi Poola Janaki",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/0f94HkZp7rE?si=ONI5F22XqUl9sXCk",
                        genre: "melody"
                    },
                    {
                        title: "Diamond Girl",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/XSegRHkaUco?si=KXT35tGtNFrf4zBZ",
                        genre: "pop"
                    },
                    {
                        title: "Rangoli Rangoli",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/P_8J7VzcVVI?si=FlA9FIc6XqBQUdTG",
                        genre: "electronic"
                    },
                    {
                        title: "Sairo Sairo",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/D0-Drqktml4?si=88-AZN8JIff4oTap",
                        genre: "electronic"
                    },
                    {
                        title: "Welcome Kanakam",
                        artist: "S. Thaman • 2013",
                        embed: "https://www.youtube.com/embed/yegcakkzxMQ?si=oENOEpDkXtZ-B7pt",
                        genre: "electronic"
                    }
                ]
            },
            kushi: {
                title: "Kushi",
                image: "https://upload.wikimedia.org/wikipedia/en/thumb/2/27/Kushi_Theatrical_Poster.jpg/250px-Kushi_Theatrical_Poster.jpg",
                year: "2001",
                category: "melody",
                songs: [
                    {
                        title: "Ye Mera Jaha",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/8LSZFtfbHwQ?si=HsggNyp39ZPA2h0P",
                        genre: "melody"
                    },
                    {
                        title: "Ammaye Sannaga",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/yEBee5d_S8U?si=5MEJQPc2lmLWhriS",
                        genre: "mass"
                    },
                    {
                        title: "Cheliya Cheliya",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/-Z9jQn442Ts?si=rBjmRU5_RyXOoqwr",
                        genre: "pop"
                    },
                    {
                        title: "Premante",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/uzEHxM8uMLw?si=b5iWFlfkOx5E_Xuv",
                        genre: "melody"
                    },
                    {
                        title: "Holi Holi",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/9AwF8Iyjbyk?si=AULLTrJFM3CQgWWj",
                        genre: "melody"
                    },
                    {
                        title: "Aduvari Matalaku",
                        artist: "Mani Sharma • 2001",
                        embed: "https://www.youtube.com/embed/ySWjbASVRIc?si=PjDC-RBhGgelqi_W",
                        genre: "melody"
                    }
                ]
            },
            orange: {
                title: "Orange",
                image: "https://wallpapercave.com/wp/wp6851037.jpg",
                year: "2010",
                category: "melody",
                songs: [
                    {
                        title: "Ola Olaala Ala",
                        artist: "Harris Jayaraj • 2010",
                        embed: "https://www.youtube.com/embed/OmDtcHZ9W0Y?si=LuqjfbYFTqIlJBTF",
                        genre: "melody"
                    },
                    {
                        title: "Chilipiga",
                        artist: "Karthik, Chinmayi • 2010",
                        embed: "https://www.youtube.com/embed/ne6PDAoyiBA?si=N3nEzgaNUM_Eplw5",
                        genre: "melody"
                    },
                    {
                        title: "Nenu Nuvvantu",
                        artist: "A.R. Rahman, Shreya Ghoshal • 2010",
                        embed: "https://www.youtube.com/embed/XZGTTLiWRXg?si=Z4IgUMUn1-ZGk3q1",
                        genre: "mass"
                    },
                    {
                        title: "Hello Rammante",
                        artist: "Karthik, Shreya Ghoshal • 2010",
                        embed: "https://www.youtube.com/embed/QntqP3PrW3c?si=cSDi0iZl7nfa63QQ",
                        genre: "melody"
                    },
                    {
                        title: "O'range",
                        artist: "Karthik, Chinmayi • 2010",
                        embed: "https://www.youtube.com/embed/kLtlgOWzf-Q?si=vyKtMwa6JMHDsiq5",
                        genre: "melody"
                    },
                    {
                        title: "Rooba Rooba",
                        artist: "Karthik, Chinmayi • 2010",
                        embed: "https://www.youtube.com/embed/hgQeo55s4So?si=0DjfqZDjEF3XmaZt",
                        genre: "melody"
                    }
                ]
            },
            venky: {
                title: "Venky",
                image: "https://m.media-amazon.com/images/M/MV5BMzNlZDY2NzItODBhYi00MDE0LTgyYmEtNjU4NzMwZTdkOGY2XkEyXkFqcGc@._V1_.jpg",
                year: "2004",
                category: "mass",
                songs: [
                    {
                        title: "Silakemo(Mass Tho Pettukunte)",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/xpW_eBW2kdY?si=eV5tQhuqv74XHAkz",
                        genre: "mass"
                    },
                    {
                        title: "Andala Chukkala Lady",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/AjFc3o_pADg?si=bvuFr23toB_WTUtA",
                        genre: "pop"
                    },
                    {
                        title: "Maar Maar",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/93J6KLVz5xg?si=tSfH5S4jO8GO4mDB",
                        genre: "melody"
                    },
                    {
                        title: "O Manasa",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/K8PgLaA_PYk?si=3cG2zOhQAFqoRDkj",
                        genre: "melody"
                    },
                    {
                        title: "Anaganaga Kadhala",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/IWk8PiH84G0?si=Um_ux6ZlAyd2TjCb",
                        genre: "mass"
                    },
                    {
                        title: "Gongoora Thotakada",
                        artist: "Devi Sri Prasad • 2004",
                        embed: "https://www.youtube.com/embed/9vNGmAj__aU?si=B-9MS-KNaS8iO0Qy",
                        genre: "mass"
                    }
                ]
            },
            "ninnu-kori": {
                title: "Ninnu Kori",
                image: "https://i.scdn.co/image/ab67616d0000b27367038f7285789e85996f1a96",
                year: "2017",
                category: "melody",
                songs: [
                    {
                        title: "Adiga Adiga",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/evbYFsSJ4pU?si=HKpEiKV2hx6D_cxN",
                        genre: "melody"
                    },
                    {
                        title: "Unnatundi Gundey",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/-twi5MBq1TQ?si=BHuVXr4pj9r98wfI",
                        genre: "melody"
                    },
                    {
                        title: "Once Upon A Time Lo",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/sEeNHIwopGk?si=uLLQbuwrIcMEx-wX",
                        genre: "pop"
                    },
                    {
                        title: "Hey Badhulu Cheppavey",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/RITbbGIXZe0?si=ycMu-XxxsPQ9Z3RQ",
                        genre: "melody"
                    },
                    {
                        title: "Ninnu Kori",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/gPDkCAMW4mY?si=0J36Gy2ECJFAZY08",
                        genre: "melody"
                    }
                ]
            },
            "nuvvu-naaku-nachav": {
                title: "Nuvvu Naaku Nachav",
                image: "https://m.media-amazon.com/images/M/MV5BMzY4NjI0NTgtNTk1Ny00MzAzLThkZTYtYWMxYmNlZDU5ZThjXkEyXkFqcGc@._V1_.jpg",
                year: "2001",
                category: "melody",
                songs: [
                    {
                        title: "Unnamata Cheppaniva",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/rADxNvZFSnQ?si=zdwznC1EP8y3CgCH",
                        genre: "mass"
                    },
                    {
                        title: "O Navvu Chalu",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/aof2NeZA-54?si=vF_fejuoDQMrHTsf",
                        genre: "mass"
                    },
                    {
                        title: "Aakasham",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/f_8hlpCjCUU?si=1h-8HCKAOp8G8SCo",
                        genre: "melody"
                    },
                    {
                        title: "Naa Chupe Ninu",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/Yz0V99HL--Y?si=LWneQs7f6L6MgqvC",
                        genre: "melody"
                    },
                    {
                        title: "O Priyatama",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/dzRgnnltCs0?si=V7Ok9ZFlh01DJ8v4",
                        genre: "melody"
                    },
                    {
                        title: "Okkasari Cheppaleva",
                        artist: "Koti • 2001",
                        embed: "https://www.youtube.com/embed/8lHeSeWYa_4?si=NRRkC2GGAsSCgN72",
                        genre: "electronic"
                    }
                ]
            },
            "raarandai-veduka-choodaam": {
                title: "Raarandai Veduka Choodaam",
                image: "https://m.media-amazon.com/images/M/MV5BOTBlZjRjNjYtMzQ5NS00ZGJiLTkyNDctZjNiOWVjMmQ3ZTZmXkEyXkFqcGc@._V1_FMjpg_UX1000_.jpg",
                year: "2017",
                category: "melody",
                songs: [
                    {
                        title: "Raarandai Veduka Choodaam",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/X93R56NFBe4?si=QG57NGXHoY0L4554",
                        genre: "melody"
                    },
                    {
                        title: "Nee Vente Nenunte",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/U6_4Nm-nax8?si=KRliFatPuVB-2gEF",
                        genre: "pop"
                    },
                    {
                        title: "Bhramaramba Ki Nachesanu",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/nP_SuJPrtIg?si=logdeXahrjDA-azu",
                        genre: "electronic"
                    },
                    {
                        title: "Thakita Thakajham",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/p9kCgQdy_VE?si=rwLUlqiLCwfCo5Dy",
                        genre: "melody"
                    },
                    {
                        title: "Break-Up",
                        artist: "Gopi Sundar • 2017",
                        embed: "https://www.youtube.com/embed/xzttSsWL7SY?si=96YJ-uOS6sd0R3uX",
                        genre: "melody"
                    }
                ]
            },
            "geetha-govindham": {
                title: "Geetha Govindham",
                image: "https://image.tmdb.org/t/p/original/2w4YlrPemW2uBIglh102R1w4yaP.jpg",
                year: "2018",
                category: "melody",
                songs: [
                    {
                        title: "Inkem Inkem Inkem Kaavaale",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/qFYj1w69OZA?si=qZA0WJGVAjy54X2V",
                        genre: "melody"
                    },
                    {
                        title: "What The Life",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/mQuNZToUlXc?si=NCiwVCY7Go1y0daS",
                        genre: "pop"
                    },
                    {
                        title: "Yenti Yenti",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/2Hro7K55Ivs?si=6klSDEBDNivafAE2",
                        genre: "hiphop"
                    },
                    {
                        title: "Vachindamma",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/aotMkXvjXtc?si=06W_MuRjjsoRt0v5",
                        genre: "melody"
                    },
                    {
                        title: "Kanureppala Kaalam",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/JPBjdkoqWuc?si=Mm2sw5rubrzFIaz4",
                        genre: "melody"
                    },
                    {
                        title: "Tanemandhe Tanemandhe",
                        artist: "Gopi Sundar • 2018",
                        embed: "https://www.youtube.com/embed/wDZNcwJ1JOE?si=zMsFhGsrTTBUiO-P",
                        genre: "melody"
                    }
                ]
            }
        };
        // Session/user data for favorites
        const currentUser = <?php echo json_encode($username); ?>;
        const initialFavorites = <?php echo json_encode($initialFavorites); ?>;

        // DOM Elements
        const mainContent = document.getElementById('mainContent');
        const searchBar = document.getElementById('searchBar');
        const moviesGrid = document.getElementById('moviesGrid');
        const playerContainer = document.getElementById('playerContainer');
        const closeBtn = document.getElementById('closeBtn');
        const musicVideo = document.getElementById('musicVideo');
        const notification = document.getElementById('notification');
        const favoritesBtn = document.getElementById('favoritesBtn');
        const favoritesPage = document.getElementById('favoritesPage');
        const backBtn = document.getElementById('backBtn');
        const favoritesGrid = document.getElementById('favoritesGrid');
        const emptyFavorites = document.getElementById('emptyFavorites');
        const backToMusicBtn = document.getElementById('backToMusicBtn');

        // Page state
        let currentPage = 'main'; // 'main' or 'favorites'

        // Favorites array
        let favorites = Array.isArray(initialFavorites) ? [...initialFavorites] : [];

        // Load movies immediately (no intro screen)
        (async () => {
            await refreshFavoritesFromServer();
            loadMovies();
        })();

        // Favorites button click handler
        favoritesBtn.addEventListener('click', () => {
            showFavoritesPage();
        });

        // Back button click handler
        backBtn.addEventListener('click', () => {
            showMainPage();
        });

        // Back to music button click handler
        backToMusicBtn.addEventListener('click', () => {
            showMainPage();
        });

        // Close player button
        closeBtn.addEventListener('click', () => {
            playerContainer.style.display = 'none';
            // Stop the video by removing the src
            musicVideo.src = '';
        });

        // Show main page function
        async function showMainPage() {
            currentPage = 'main';
            favoritesPage.style.display = 'none';
            moviesGrid.style.display = 'grid';

            // Load movies
            await refreshFavoritesFromServer();
            loadMovies();
        }

        // Show favorites page function
        async function showFavoritesPage() {
            currentPage = 'favorites';
            moviesGrid.style.display = 'none';
            favoritesPage.style.display = 'block';

            // Load favorites
            await refreshFavoritesFromServer();
            loadFavorites();
        }

        // Load movies into the grid
        function loadMovies(searchQuery = '') {
            moviesGrid.innerHTML = '';

            let filteredMovies = Object.values(movies);

            // Apply search filter
            if (searchQuery) {
                const query = searchQuery.toLowerCase();
                filteredMovies = filteredMovies.filter(movie => {
                    // Check movie title or any song title/artist
                    return movie.title.toLowerCase().includes(query) ||
                        movie.songs.some(song =>
                            song.title.toLowerCase().includes(query) ||
                            song.artist.toLowerCase().includes(query)
                        );
                });
            }

            if (filteredMovies.length === 0) {
                moviesGrid.innerHTML = `
            <div class="empty-state" style="grid-column: 1 / -1">
                <div class="empty-icon">
                    <i class="fas fa-music"></i>
                </div>
                <div class="empty-text">
                    No movies found matching your search.
                </div>
            </div>
        `;
                return;
            }

            filteredMovies.forEach(movie => {
                const movieCard = document.createElement('div');
                movieCard.className = 'movie-card';

                movieCard.innerHTML = `
            <img src="${movie.image}" alt="${movie.title}" class="movie-poster">
            <div class="movie-info">
                <h3 class="movie-title">${movie.title}</h3>
                <div class="movie-year">${movie.year}</div>
                <div class="movie-songs">
                    <div class="songs-title">Choose Your Song!</div>
                    <div class="songs-list">
                        ${movie.songs.map((song) => `
                            <div class="song-item" data-video="${song.embed}" data-movie="${movie.title}" data-poster="${movie.image}" data-artist="${song.artist}" data-title="${song.title}">
                                <div class="song-title">${song.title}</div>
                                <div class="song-artist">${song.artist.split('•')[0]}</div>
                                <div class="song-actions">
                                    <button class="play-song-btn" title="Play song">
                                        <i class="fas fa-play"></i>
                                    </button>
                                    <button class="favorite-btn${isFavorite(movie.title, song.title) ? ' favorited' : ''}" title="Add to favorites">
                                        <i class="fas fa-heart"></i>
                                    </button>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
            </div>
        `;

                // Add click handlers for song items
                movieCard.querySelectorAll('.song-item').forEach(item => {
                    // Play song button
                    const playBtn = item.querySelector('.play-song-btn');
                    playBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        playVideo(item.dataset.video);
                    });

                    // Favorite button
                    const favBtn = item.querySelector('.favorite-btn');
                    const songTitle = item.dataset.title;
                    const songArtist = item.dataset.artist;
                    const movieTitle = item.dataset.movie;
                    const poster = item.dataset.poster;

                    favBtn.addEventListener('click', async (e) => {
                        e.stopPropagation();
                        await toggleFavorite({
                            movieTitle,
                            songTitle,
                            songArtist,
                            embed: item.dataset.video,
                            poster
                        }, favBtn);
                    });
                });

                moviesGrid.appendChild(movieCard);
            });
        }

        // Play video function
        function playVideo(videoUrl) {
            musicVideo.src = videoUrl;
            playerContainer.style.display = 'flex';
        }

        // Search functionality
        searchBar.addEventListener('input', () => {
            const searchQuery = searchBar.value.trim();
            loadMovies(searchQuery);
        });

        // Favorites functionality
        async function refreshFavoritesFromServer() {
            try {
                const res = await fetch('favorites_api.php');
                const data = await res.json();
                if (data.success) {
                    favorites = data.favorites || [];
                }
            } catch (err) {
                console.error('Failed to refresh favorites', err);
            }
        }

        function isFavorite(movieTitle, songTitle) {
            return favorites.some(fav =>
                fav.movieTitle === movieTitle && fav.songTitle === songTitle
            );
        }

        function updateFavoriteButtonsState() {
            document.querySelectorAll('.favorite-btn').forEach(btn => {
                const item = btn.closest('.song-item');
                if (!item) return;
                const songTitle = item.dataset.title;
                const movieTitle = item.dataset.movie;
                btn.classList.toggle('favorited', isFavorite(movieTitle, songTitle));
            });
        }

        // Toggle favorite function using server storage
        async function toggleFavorite(song, favBtn) {
            const { movieTitle, songTitle, songArtist, embed, poster } = song;
            const isFavorited = isFavorite(movieTitle, songTitle);
            const action = isFavorited ? 'remove' : 'add';

            try {
                const res = await fetch('favorites_api.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        action,
                        movieTitle,
                        songTitle,
                        songArtist,
                        embed,
                        poster
                    })
                });
                const data = await res.json();
                if (!data.success) {
                    throw new Error(data.message || 'Unable to update favorites.');
                }

                favorites = data.favorites || [];

                if (favBtn) {
                    favBtn.classList.toggle('favorited', action === 'add');
                }
                updateFavoriteButtonsState();

                showNotification(action === 'add'
                    ? `"${songTitle}" added to favorites!`
                    : `"${songTitle}" removed from favorites!`);

                if (currentPage === 'favorites') {
                    loadFavorites();
                }
            } catch (err) {
                showNotification(err.message || 'Something went wrong updating favorites.');
            }
        }

        // Load favorites into the grid
        function loadFavorites() {
            favoritesGrid.innerHTML = '';

            if (favorites.length === 0) {
                emptyFavorites.style.display = 'block';
                return;
            }

            emptyFavorites.style.display = 'none';

            favorites.forEach((fav) => {
                const favoriteCard = document.createElement('div');
                favoriteCard.className = 'favorite-card';

                favoriteCard.innerHTML = `
            <img src="${fav.poster}" alt="${fav.movieTitle}" class="favorite-poster">
            <div class="favorite-info">
                <h3 class="favorite-title">${fav.songTitle}</h3>
                <div class="favorite-artist">${fav.songArtist}</div>
                <div class="favorite-actions">
                    <button class="play-favorite-btn">
                        <i class="fas fa-play"></i> Play
                    </button>
                    <button class="remove-favorite-btn" data-movie="${fav.movieTitle}" data-song="${fav.songTitle}">
                        <i class="fas fa-times"></i> Remove
                    </button>
                </div>
            </div>
        `;

                // Add play functionality
                favoriteCard.querySelector('.play-favorite-btn').addEventListener('click', () => {
                    playVideo(fav.embed);
                });

                // Add remove functionality - FIXED to use the toggleFavorite function properly
                favoriteCard.querySelector('.remove-favorite-btn').addEventListener('click', async () => {
                    await toggleFavorite(fav, null);
                });

                favoritesGrid.appendChild(favoriteCard);
            });
        }

        function showNotification(message) {
            const notificationEl = document.getElementById('notification');
            notificationEl.querySelector('span').textContent = message;
            notificationEl.classList.add('show');

            setTimeout(() => {
                notificationEl.classList.remove('show');
            }, 3000);
        }

        // Initialize with showing the startup screen
        setTimeout(() => {
            // Show notification
            showNotification('New songs added weekly!');
        }, 3000);
    </script>
</body>

</html>