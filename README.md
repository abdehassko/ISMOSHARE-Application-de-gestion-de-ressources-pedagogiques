"# ISMOSHARE-Application-de-gestion-de-ressources-pedagogiques" 
# ISMOSHARE

ISMOSHARE is a **PHP-based academic resource sharing platform** that allows users to upload, browse,comment, and download educational resources with role-based access and download tracking.

---

## 🚀 Features

* 🔐 User authentication (login / registration)
* 👥 Role management (admin, formateur, stagiaire)
* 📂 Resource comment and upload/download
* 🗂️ Organized resources by module / filière
* 🛡️ Secure file handling

---

## 📁 Project Structure

```
ISMOSHARE/
│
├── assets/ # Images, icons, logos
├── styles/ # CSS files
├── js/ # JavaScript files
├── notifications/ # Notifications actions (mark as read - mark all as read)
├── others/ # Database connection & helpers
├── uploaded_files/ # Uploaded resources (PDF, docs, etc.)
├── pages/
│ ├── connexion/ # Login pages
│ ├── inscription/ # Registration pages
│ ├── principale/ # Main dashboard
│ ├── forum/ # Forum page
│ ├── ressource/ # Resource management
│ ├── utilisateurs/ # User management
│ └── validation/ # Admin validation
│ ├── profile/ # User profile
│ └── annonce/ # annonce page
├── database_script.sql # database script

```
---

## ⚙️ Requirements

* PHP >= 8.0
* MySQL / MariaDB
* XAMPP / WAMP / LAMP
* Web browser

---

## 🛠️ Installation

1. Clone the repository:

```bash
git clone https://github.com/abdehassko/ISMOSHARE-Application-de-gestion-de-ressources-pedagogiques
```

2. Move the project to your server root:

```
C:/xampp/htdocs/ISMOSHARE
```

3. Import the database:

* Open **phpMyAdmin**
* Create a database
* Import the provided `.sql` file

4. Configure database connection:
   Edit:

```
others/code-conexionAvecDB.php
```

5. Run the app:

```
http://localhost/ISMOSHARE/
```

---

## 📥 File Upload & Download

* Uploaded files are stored in:

```
ISMOSHARE/uploaded_files/
```

* Downloads are handled securely via `telecharger.php`
* Each download is logged in the database

---

## 🧑‍💻 Author

**Abderrahim Elhasskouri**
Digital Development Student – OFPPT

---

⭐ If you like this project, feel free to star the repository!


