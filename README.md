
"# ISMOSHARE-Application-de-gestion-de-ressources-pedagogiques" 
"# you can access to the platforme now : https://ismotetouan.infinityfree.me/" 
email : direction@ismo.ma
password : 1212

## 🚀 Features
# ISMOSHARE

ISMOSHARE is a **PHP-based academic resource sharing platform** that allows users to upload, browse,comment, and download educational resources with role-based access and download tracking.

---

## 🚀 Features

* 🔐 User authentication (login / registration)
* 👥 Role management (admin, formateur, stagiaire)
* 📂 Resource comment and upload/download
* 🗂️ Organized resources by module / filière
* 🗣️ Forum (Publish questions-Comment on questions-React to comments)
* 📢 Annonces 
* 👤 Utilisateurs (Manage user roles and account status by admin)
* ✔️ Validation (Admin and formateurs can validate or reject publications/comments)
* 🔔 Système de notifications en temps réel (nouveaux commentaires, validations, annonces)  
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
http://localhost/ISMOSHARE/pages/connexion/page-conexion.php
```

---

## 📥 File Upload & Download

* Uploaded files are stored in:

```
ISMOSHARE/uploaded_files/
```
## 🔐 first log in 
email : direction@ismo.ma
password : 1212

---

### 🖼️ Screenshots
<img width="1366" height="641" alt="2" src="https://github.com/user-attachments/assets/d707e699-a0c0-4ef4-89be-2bf251f2ee1c" />
<img width="1365" height="643" alt="3" src="https://github.com/user-attachments/assets/e11bc205-b5d2-4d94-aecd-bf0358fae6ef" />
<img width="1365" height="643" alt="3" src="https://github.com/user-attachments/assets/bb955a54-218a-4a8d-8950-cda3f5e81457" />
<img width="1366" height="643" alt="4" src="https://github.com/user-attachments/assets/56510114-16fc-4309-acc7-c2addff02d4f" />
<img width="489" height="577" alt="5" src="https://github.com/user-attachments/assets/bcd13f2b-64e3-4497-ba7a-5ad86050e6b9" />
<img width="1366" height="641" alt="6" src="https://github.com/user-attachments/assets/99b0a904-1534-43dc-9a0d-e764ab1a4003" />
<img width="1366" height="642" alt="7" src="https://github.com/user-attachments/assets/e9f86d37-6b6c-4d23-b3e8-9ea096b929a3" />
<img width="1344" height="636" alt="9" src="https://github.com/user-attachments/assets/2fdc7f0a-16a3-434c-abc7-18c28f2d04cc" />
<img width="1366" height="641" alt="10" src="https://github.com/user-attachments/assets/62580501-28a1-42b7-af5d-3fb165e3a625" />
<img width="1366" height="639" alt="11" src="https://github.com/user-attachments/assets/2b8dd102-58f1-404b-bf23-7a00fc4eb899" />
<img width="315" height="244" alt="12" src="https://github.com/user-attachments/assets/2aa3b42a-9836-4fca-9895-4886fbbe921c" />












---

### 🚀 Future Improvements

* 📱 Version mobile / application responsive pour une meilleure expérience sur smartphones et tablettes  
* 🌐 Support multilingue (Espagnole / Anglais / Arabe)  

## 🧑‍💻 Author

**Abderrahim Elhasskouri**
Digital Development Student – OFPPT

---

⭐ If you like this project, feel free to star the repository!


