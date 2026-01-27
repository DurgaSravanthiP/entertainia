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

$username = $_SESSION['username'] ?? '';
$role = $_SESSION['role'] ?? 'user';
$backTarget = ($role === 'admin') ? 'admin_dashboard.php' : 'selection.php';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cuddle Toons - Playful Cartoons</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="cartoon.css">
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
    <div class="special-features">Special Edition!</div>

    <h1 class="header" id="mainHeader">Cuddle Toons</h1>

    <div class="category-tabs" id="categoryTabs">
        <div class="category-tab active" data-category="all">
            <i class="fas fa-star"></i> All
        </div>
        <div class="category-tab" data-category="comedy">
            <i class="fas fa-laugh-squint"></i> Comedy
        </div>
        <div class="category-tab" data-category="adventure">
            <i class="fas fa-map-marked-alt"></i> Adventure
        </div>
    </div>

    <div class="cartoon-container" id="cartoonContainer" style="display: none;">
        <!-- Cartoons will be loaded here -->
    </div>

    <div class="player-container" id="playerContainer">
        <div class="video-player">
            <div class="close-btn" id="closeBtn">
                <i class="fas fa-times"></i>
            </div>
            <div class="video-container">
                <iframe id="cartoonVideo" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <div class="notification" id="notification">
        <i class="fas fa-bell"></i>
        <span>New episodes added weekly!</span>
    </div>

    <script>
        // Create floating clouds
        function createClouds() {
            const colors = ['#DAD2FF', '#FFF2AF', '#B2A5FF', '#ffffff'];
            for (let i = 0; i < 15; i++) {
                const cloud = document.createElement('div');
                cloud.className = 'cloud';
                cloud.style.width = `${Math.random() * 120 + 50}px`;
                cloud.style.height = `${Math.random() * 80 + 30}px`;
                cloud.style.top = `${Math.random() * 80}%`;
                cloud.style.left = `${Math.random() * 100}%`;
                cloud.style.animationDelay = `${Math.random() * 5}s`;
                cloud.style.animationDuration = `${Math.random() * 10 + 5}s`;
                cloud.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                document.body.appendChild(cloud);
            }
        }

        // Create floating characters
        function createFloatingCharacters() {
            const characters = [
                'https://clipart-library.com/data_images/358936.png', // Doraemon
                'https://clipart-library.com/images_k/shinchan-transparent/shinchan-transparent-4.png' // Shinchan
            ];

            const positions = [];
            for (let i = 0; i < 10; i++) {
                let top, left, isOverlapping;
                do {
                    top = Math.random() * 80 + 10;
                    left = (i % 3 === 0) ? Math.random() * 30 : (i % 3 === 1) ? Math.random() * 30 + 35 : Math.random() * 30 + 70;
                    isOverlapping = positions.some(pos =>
                        Math.abs(pos.top - top) < 15 && Math.abs(pos.left - left) < 15
                    );
                } while (isOverlapping);

                positions.push({ top, left });

                const char = document.createElement('div');
                char.className = 'floating-character';
                char.style.backgroundImage = `url('${characters[Math.floor(Math.random() * characters.length)]}')`;
                char.style.top = `${top}%`;
                char.style.left = `${left}%`;
                char.style.animationDelay = `${Math.random() * 5}s`;
                char.style.animationDuration = `${Math.random() * 20 + 10}s`;
                document.body.appendChild(char);
            }
        }

        // Create confetti
        function createConfetti() {
            const colors = ['#493D9E', '#B2A5FF', '#DAD2FF', '#FFF2AF', '#FFB6C1', '#98FF98'];
            for (let i = 0; i < 100; i++) {
                const confetti = document.createElement('div');
                confetti.className = 'confetti';
                confetti.style.left = `${Math.random() * 100}%`;
                confetti.style.backgroundColor = colors[Math.floor(Math.random() * colors.length)];
                confetti.style.animationDuration = `${Math.random() * 3 + 2}s`;
                confetti.style.animationDelay = `${Math.random() * 2}s`;
                confetti.style.width = `${Math.random() * 15 + 5}px`;
                confetti.style.height = `${Math.random() * 15 + 5}px`;
                confetti.style.borderRadius = `${Math.random() * 50}%`;
                document.body.appendChild(confetti);

                // Remove confetti after animation
                setTimeout(() => {
                    confetti.remove();
                }, 5000);
            }
        }

        // Cartoon data with episodes
        const cartoons = [
            {
                id: 1,
                title: "Doraemon",
                image: "https://pixahive.com/wp-content/uploads/2021/04/Doraemon-Cartoon-Illustration-410092-pixahive.jpg",
                year: "1979",
                duration: "24 min",
                category: "comedy",
                featured: true,
                episodes: [
                    { title: "Nobita's Birthday", video: "https://www.youtube.com/embed/JuPWQm1Abtk?si=AjuGdz_eFz9LJEjY" },
                    { title: "Time Machine Adventure", video: "https://www.youtube.com/embed/5xX6qFsyeCs?si=M-6YjhY_KIB9qjHb" },
                    { title: "Giant Cookie Trouble", video: "https://www.youtube.com/embed/RjcseBsq5jA?si=Sf3WOkTTlu--7x4g" },
                    { title: "Pills magic", video: "https://www.youtube.com/embed/WHxUbPpDiDo?si=kIQBP9yUs9t8Uwc0" },
                    { title: "Little girl", video: "https://www.youtube.com/embed/WuU_kCgTKJw?si=0B9uVoYqm4hRQjSp" },
                    { title: "Vaccum clean", video: "https://www.youtube.com/embed/wjIPlJOBemA?si=Gkw3jjATm97F_gSe" },
                    { title: "Magic Lamp", video: "https://www.youtube.com/embed/tgYZ2EzvoH8?si=IM7lVfpbeq4-nHUs" },
                    { title: "Nobitha Ninja training", video: "https://www.youtube.com/embed/N1Q9UVeiUYk?si=Qqm4BYWkobrsw4AC" },
                    { title: "Tressure Box", video: "https://www.youtube.com/embed/WNNANJvtLpQ?si=wAUQb43xdwaJYPvW" },
                    { title: "Picnic", video: "https://www.youtube.com/embed/L9-Wz1utMqE?si=ptmdbfBRmnu2R-q0" }
                ]
            },
            {
                id: 2,
                title: "Shinchan",
                image: "https://wallpapercave.com/wp/wp7590525.jpg",
                year: "1992",
                duration: "20 min",
                category: "comedy",
                featured: true,
                episodes: [
                    { title: "Action Kamen", video: "https://www.youtube.com/embed/W1sDpYXhWyg?si=nRpiv3Kon9dRgT9V" },
                    { title: "Zoo Adventure", video: "https://www.youtube.com/embed/axA0nX969XA?si=xKzkYfGNShucEGvd" },
                    { title: "Pool day", video: "https://www.youtube.com/embed/orBPgl6uirI?si=lnS43RU3oZ18JNWD" },
                    { title: "Sinchan Foodie", video: "https://www.youtube.com/embed/zV6UB8Z1mr4?si=HPoNRJmIrVYFms93" },
                    { title: "Rainy Day", video: "https://www.youtube.com/embed/gEvrGLGYtvM?si=pDNVfgk9tpe428oS" },
                    { title: "AC Home", video: "https://www.youtube.com/embed/_1ZQT2iwYVw?si=SqNGXWG9kM3-qx7B" },
                    { title: "Eat by Chopstick", video: "https://www.youtube.com/embed/RlIsQSAukFk?si=mHj66rSnVvHaQ4Wm" },
                    { title: "Sinchan mom return", video: "https://www.youtube.com/embed/pVFQdJ0a-PQ?si=deR0Ia0VW14tAySW" },
                    { title: "Picnic Day", video: "https://www.youtube.com/embed/nsKX_FcMGuM?si=8X2M9ovhSj9_dd2U" },
                    { title: "Clean Sinchan", video: "https://www.youtube.com/embed/7YfhMXBAGaQ?si=oGyJkYabvkCaIIMn" }
                ]
            },
            {
                id: 3,
                title: "Mr. Bean",
                image: "https://wallpapercave.com/wp/wp6126418.jpg",
                year: "1990",
                duration: "25 min",
                category: "comedy",
                episodes: [
                    { title: "Baking", video: "https://www.youtube.com/embed/4Unv7rw5HNk?si=5Oxi5LfNwZigi3dl" },
                    { title: "Eating Contest", video: "https://www.youtube.com/embed/RyqPS2HlZFw?si=eOuNQpQMA818KHn9" },
                    { title: "Animated", video: "https://www.youtube.com/embed/R08vmJkUAU8?si=_dmiA2RsyHVi3fct" },
                    { title: "At the Cinema", video: "https://www.youtube.com/embed/W1TM9rhYu-E?si=5LUV-N5K9DPPXUY9" },
                    { title: "Coffee Bean", video: "https://www.youtube.com/embed/pPbf-eVGS6E?si=4fs1h7ZGvhG4_xVw" },
                    { title: "Gold Fish", video: "https://www.youtube.com/embed/7oB2UccZoaE?si=ERyDQCLXAZG32LCh" },
                    { title: "Be my guest", video: "https://www.youtube.com/embed/jp3hU0AV2sg?si=s8gBnK_jlzElxi_V" },
                    { title: "Car Wash", video: "https://www.youtube.com/embed/fukTa9pQLY4?si=UMTJP5R_YfCmYDxT" },
                    { title: "Young Bean", video: "https://www.youtube.com/embed/0-F_kBHSU6w?si=2sDiKwsqpq9c5vM6" },
                    { title: "Green Bean", video: "https://www.youtube.com/embed/9r7HgcgDwqY?si=vIsFb4UZk8irSBUj" }
                ]
            },
            {
                id: 4,
                title: "Ben 10",
                image: "https://coolwallpapers.me/picsup/5444520-ben-10-wallpapers.jpg",
                year: "2005",
                duration: "22 min",
                category: "adventure",
                episodes: [
                    { title: "And Then There Were 10", video: "https://www.youtube.com/embed/6T-ygNCkEW4?si=e5RvY29otrjst1mM" },
                    { title: "Washington B.C.", video: "https://www.youtube.com/embed/c_nzwrc7_PY?si=YFQpsgVvcqrEBrgH" },
                    { title: "The Krakken", video: "https://www.youtube.com/embed/P1dQqhjaDN8?si=rSUpORUueAzyLexv" },
                    { title: "Permanent Retirement", video: "https://www.youtube.com/embed/_jJJD3W5ssE?si=2oTJ0jcN89cKjFqx" },
                    { title: "Hunted", video: "https://www.youtube.com/embed/l0TvdGOk93w?si=pRiFLps40YmfHCzY" },
                    { title: "Tourist Trap", video: "https://www.youtube.com/embed/J4GhkiOk3OM?si=zLf7rGFGvAAQqKwc" },
                    { title: "Kevin 11", video: "https://www.youtube.com/embed/G41y4cjCwCA?si=jX8DHfGcRxQ29Ghk" },
                    { title: "The Alliance", video: "https://www.youtube.com/embed/59aB0WuehB0?si=dkuGzdHd7TTwECfZ" },
                    { title: "Last Laugh", video: "https://www.youtube.com/embed/-c51y14sbdM?si=0jAtx4y4EDjc6MRM" },
                    { title: "Lucky Girl", video: "https://www.youtube.com/embed/s2xtzoQiaMY?si=ao6uJnRywLUKh8lJ" }
                ]
            },
            {
                id: 5,
                title: "Courage the Cowardly Dog",
                image: "https://tse1.mm.bing.net/th?id=OIP.qKiJRXRQWw_2EcSYCYeIYAHaFj&rs=1&pid=ImgDetMain",
                year: "1999",
                duration: "22 min",
                category: "adventure",
                episodes: [
                    { title: "The Curse of Shirley", video: "https://www.youtube.com/embed/cY87Qp7FA0U?si=ZYcUh0RzS53x6bSk" },
                    { title: "The Clutching Foot", video: "https://www.youtube.com/embed/HpAvF6eMHWs?si=nhdJNWPOgd6ZCrH7" },
                    { title: "King Ramses' Curse", video: "https://www.youtube.com/embed/kKnC674-ZDU?si=CibsGS2pmo2ehBYW" },
                    { title: "The Demon in the Mattress", video: "https://www.youtube.com/embed/2jf9aNR8ifg?si=kbovRKtsBpADgNUs" },
                    { title: "Freaky Fred", video: "https://www.youtube.com/embed/_J4NdAuRnIc?si=DDtwyCIITP1SiuIr" },
                    { title: "Say ARGH ", video: "https://www.youtube.com/embed/lLoDUh33-70?si=69zkaa4YNfzmLw1l" },
                    { title: "The Quilt Club", video: "https://www.youtube.com/embed/VBrCL67MMbw?si=Gk8jAiOOQ6OKJpOz" },
                    { title: "The Hunchback of Nowhere", video: "https://www.youtube.com/embed/X8UX0Lmrftw?si=rRFgFpC9XRX1-Sno" },
                    { title: "The Mask", video: "https://www.youtube.com/embed/NJnFerr_xos?si=pTqg_JnP42tIwU9P" },
                    { title: "The Sandman Sleeps", video: "https://www.youtube.com/embed/PbPvAw6cz3o?si=BV9d48jjv2n9nthG" }
                ]
            },
            {
                id: 6,
                title: "Phineas and Ferb",
                image: "https://flxt.tmsimg.com/assets/p186178_b_h9_az.jpg",
                year: "2007",
                duration: "22 min",
                category: "comedy",
                episodes: [
                    { title: "Rollercoaster", video: "https://www.youtube.com/embed/fLWBx37eKBM?si=-_KV8fB8IpyuDbHW" },
                    { title: "Lawn Gnome Beach Party", video: "https://www.youtube.com/embed/NNOkMRw78t8?si=SZMeRnj-C8hE_Grt" },
                    { title: "The Fast and the Phineas", video: "https://www.youtube.com/embed/vid9Z3sE26Y?si=GvkfhnR3J1oAk4U0" },
                    { title: "Lights, Candace, Action!", video: "https://www.youtube.com/embed/lXW1g38ylOw?si=0ECWfRZDb8bKIFSC" },
                    { title: "Raging Bully", video: "https://www.youtube.com/embed/4y1nItfA5gU?si=iy1Diszc5P9Yk3LX" },
                    { title: "It's About Time!", video: "https://www.youtube.com/embed/iiTMAYiT4E8?si=qkZgiZNYGb5-ORsr" },
                    { title: "Dude, We're Getting the Band Back", video: "https://www.youtube.com/embed/kZ-IOh9-f90?si=ntapy3LiWQkrI2qV" },
                    { title: "Ready for the Bettys", video: "https://www.youtube.com/embed/3tMaQ18Scy0?si=nrpnzQBZFiH_pfbD" },
                    { title: "Tree to Get Ready", video: "https://www.youtube.com/embed/gyooXUIpFNg?si=3gnov0Tzp0r-W_6Q" },
                    { title: "It's a Mud, Mud, Mud World", video: "https://www.youtube.com/embed/24Sh4VDieRE?si=5TGy8JSRRzokcCCX" }
                ]
            }
        ];

        // Display cartoons
        function displayCartoons(category = 'all') {
            const container = document.getElementById('cartoonContainer');
            container.innerHTML = '';

            const filteredCartoons = category === 'all'
                ? cartoons
                : cartoons.filter(cartoon => cartoon.category === category);

            filteredCartoons.forEach(cartoon => {
                const card = document.createElement('div');
                card.className = 'cartoon-card';
                card.dataset.id = cartoon.id;

                // Add featured badge if cartoon is featured
                const featuredBadge = cartoon.featured
                    ? `<div class="featured-badge"><i class="fas fa-crown"></i> Featured</div>`
                    : '';

                card.innerHTML = `
                    ${featuredBadge}
                    <img src="${cartoon.image}" alt="${cartoon.title}" class="cartoon-img">
                    <h3 class="cartoon-title">${cartoon.title}</h3>
                    <div class="cartoon-meta">
                        <span>${cartoon.year}</span>
                        <span>${cartoon.duration}</span>
                    </div>
                    <button class="play-btn"><i class="fas fa-play"></i></button>
                    <div class="episode-selector">
                        <h4 class="episode-title">Choose Your Episode!</h4>
                        <div class="episode-list">
                            ${cartoon.episodes.slice(0, 10).map((episode, index) => `
                                <div class="episode-item" data-video="${episode.video}">
                                    <span class="episode-number">Ep ${index + 1}</span>
                                    ${episode.title}
                                </div>
                            `).join('')}
                        </div>
                    </div>
                `;
                container.appendChild(card);
            });

            // Add click events to play buttons
            document.querySelectorAll('.play-btn').forEach(btn => {
                btn.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const card = this.closest('.cartoon-card');
                    const id = parseInt(card.dataset.id);
                    const cartoon = cartoons.find(c => c.id === id);
                    if (cartoon) {
                        playVideo(cartoon.episodes[0].video);
                    }
                });
            });

            // Add click events to episode items
            document.querySelectorAll('.episode-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.stopPropagation();
                    const videoUrl = this.dataset.video;
                    playVideo(videoUrl);
                });
            });
        }

        // Function to play video with error handling
        function playVideo(videoUrl) {
            const playerContainer = document.getElementById('playerContainer');
            const videoFrame = document.getElementById('cartoonVideo');

            try {
                videoFrame.src = videoUrl;
                playerContainer.style.display = 'flex';

                // Add error listener
                videoFrame.onerror = function () {
                    videoFrame.src = '';
                    alert("Sorry, this episode isn't available. Please try another one!");
                    playerContainer.style.display = 'none';
                };
            } catch (error) {
                alert("Error loading the video. Please try another episode.");
                playerContainer.style.display = 'none';
            }
        }

        // Initialize the page
        document.addEventListener('DOMContentLoaded', function () {
            createClouds();
            createFloatingCharacters();

            // Show notification briefly
            setTimeout(() => {
                document.getElementById('notification').classList.add('show');
                setTimeout(() => {
                    document.getElementById('notification').classList.remove('show');
                }, 5000);
            }, 2000);

            // Immediately show main content (no intro overlay)
            document.getElementById('mainHeader').style.display = 'block';
            document.getElementById('categoryTabs').style.display = 'flex';
            document.getElementById('cartoonContainer').style.display = 'flex';
            displayCartoons();

            // Category tabs
            document.querySelectorAll('.category-tab').forEach(tab => {
                tab.addEventListener('click', function () {
                    document.querySelectorAll('.category-tab').forEach(t => t.classList.remove('active'));
                    this.classList.add('active');
                    const category = this.dataset.category;
                    displayCartoons(category);
                });
            });

            // Close video player
            document.getElementById('closeBtn').addEventListener('click', function () {
                document.getElementById('playerContainer').style.display = 'none';
                document.getElementById('cartoonVideo').src = '';
            });

            // Close player when clicking outside
            document.getElementById('playerContainer').addEventListener('click', function (e) {
                if (e.target === this) {
                    document.getElementById('playerContainer').style.display = 'none';
                    document.getElementById('cartoonVideo').src = '';
                }
            });
        });
    </script>
</body>

</html>