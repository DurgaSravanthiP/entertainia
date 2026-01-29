# Entertainia – Fun & Entertainment Hub

Entertainia is a vibrant web-based entertainment platform designed to bring joy to users through a curated collection of music and cartoons. It features a user-friendly interface, personalized favorites, and a secure authentication system for both users and administrators.

🔗 **Live Demo:** [http://entertainia.rf.gd](http://entertainia.rf.gd)

---

## ✨ Features

### ✅ For Users
- **Secure Authentication:** Register, Login, and Password Reset functionality.
- **Music Player:** Integrated YouTube player with featured songs, categories (Melody, Mass, Pop), and search functionality.
- **Cartoon Hub:** Watch popular cartoons like Doraemon, Shinchan, Mr. Bean, and more with episode selection.
- **Favorites System:** "Heart" your favorite songs to save them to your personal collection.
- **Interactive UI:** Playful animations, floating elements, and a responsive design.

### ✅ For Admins
- **Admin Dashboard:** Overview of total registered users.
- **Content Management Access:** Quick links to verify Cartoon and Music pages.
- **User Management:** (Scalable for future administrative tasks).

### ✅ General Features
- **Responsive Design:** Optimized for various screen sizes.
- **Dynamic Database:** Real-time data fetching for users and favorites.
- **Secure Setup:** Automated database setup script for easy deployment.

---

## 🛠️ Tech Stack

- **Frontend:** HTML5, CSS3 (Custom Animations), JavaScript (Vanilla)
- **Backend:** PHP (Session Management, MySQLi)
- **Database:** MySQL
- **Server:** Apache (XAMPP/InfinityFree)

---

## 📂 Project Structure

```
Entertainia/
│
├── index.php                 # Landing Page 
├── login.php                 # Login Page 
├── register.php              # User Registration
├── privacy.php               # Privacy Policy 
├── selection.php             # User Dashboard
├── admin_dashboard.php       # Admin Dashboard
│
├── music.php                 # Music Player Page
├── cartoon.php               # Cartoon Player Page
│
├── db_connect.php            # Database Connection
├── setup_database.php        # Automated Setup Script
├── login_process.php         # Multi-table Login Logic
├── register_process.php      # Registration Logic
└── favorites_api.php         # Favorites Handlers
```

---

## 📌 How to Run the Project

1. **Clone the repository:**
   ```bash
   git clone https://github.com/DurgaSravanthiP/entertainia.git
   ```

2. **Setup Server:**
   - Place the folder inside `htdocs/` (XAMPP).
   - Start **Apache** and **MySQL**.

3. **Database Setup (One-Click):**
   - Open your browser and visit: `http://localhost/entertainia/setup_database.php`
   - This script will automatically:
     - Create the `funhub` database.
     - Create necessary tables (`users`, `admins`, `favorites`).
     - Create 4 default **Admin** accounts (`admin1` to `admin4` / `admin123`).

4. **Launch Application:**
   - Open: `http://localhost/entertainia/index.php` (Home)

---

## 🚀 Deployment (InfinityFree)

1. **Create Database:** Create a MySQL database in your hosting control panel.
2. **Configure:** Update `db_connect.php` with your hosting credentials (Host, User, Password, DB Name).
3. **Upload:** Upload all project files to `htdocs` via File Manager or FTP.
4. **Init DB:** Run `your-site.com/setup_database.php` once to initialize the tables.

---

## 📎 GitHub Repository

[https://github.com/DurgaSravanthiP/entertainia](https://github.com/DurgaSravanthiP/entertainia)

