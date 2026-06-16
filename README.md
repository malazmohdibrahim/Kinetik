# KINETIK - Luxury Digital Automotive Showroom 

---

## Student Information

| Item | Details |
|--------|---------|
| Student Name | Malaz Mohammed Ibrahim |
| Registration Number | 24579/2024 |
| Course | EWA408510 – E-Commerce and Web Application |
| Project Title | KINETIK - Online Car Dealership Management System |
| Academic Year | 2025-2026 |

---

# Table of Contents

| No | Section |
|----|---------|
| 1 | Introduction |
| 2 | Problem Statement |
| 3 | Objectives |
| 4 | System Features |
| 5 | Technologies Used |
| 6 | System Architecture |
| 7 | Screenshots |
| 8 | GitHub Repository Link |
| 9 | Deployment Link |
| 10 | CI/CD Description |
| 11 | Challenges Encountered |
| 12 | Future Work |
| 13 | Conclusion |

---

# 1. Introduction
KINETIK is a specialized high-performance web-based car dealership and staging application designed for exotic sports car collectors and enthusiasts in Kigali, Rwanda. The system bridges the gap between premium global automotive listings and local buyers by providing, dynamic specifications, and custom persistent customer garages.
KINETIK is designed to simplify vehicle inventory management and improve the customer experience. The system enables administrators to manage vehicles, brands, and categories while allowing customers to browse available vehicles online through a responsive and user-friendly interface.
The project was developed using PHP, MySQL, HTML, CSS, JavaScript, Docker, and GitHub. It demonstrates practical application of web development, database management, deployment, and DevOps concepts.

---

# 2. Problem Statement
In Rwanda, there is currently no dedicated online platform focused on managing and selling vehicles through a complete e-commerce system. Most car dealerships rely on social media platforms, messaging applications, spreadsheets, or manual records to advertise and manage their inventory.
These methods make it difficult to keep vehicle information organized and up to date. Customers often have to contact sellers directly to confirm availability, pricing, and vehicle specifications. As a result, finding accurate information can be time-consuming and inconvenient for both customers and dealerships.
In addition, dealerships face challenges in tracking available vehicles, managing inventory efficiently, and presenting their vehicles in a professional and centralized manner online.
Therefore, there is a need for a web-based car dealership management system that allows dealerships to manage vehicle listings digitally while providing customers with easy access to vehicle information, pricing, and availability through a single online platform.

---

# 3. Objectives
### General Objective:
To develop a web-based luxury and sports car dealership platform in Rwanda that allows customers to browse available vehicles, view detailed car information, and purchase vehicles online.
### Specific Objectives
### Specific Objectives
* To provide an online platform where customers can browse luxury and sports cars available in Rwanda.
* To display detailed vehicle information, including specifications, pricing, and images.
* To organize vehicles by brands and categories for easier navigation.
* To provide secure administrator access for managing vehicle listings.
* To improve the customer experience when searching for and exploring vehicles online.
* To allow administrators to add, update, and remove vehicle listings efficiently.
* To deploy the application online so it can be accessed from anywhere.
* To use Docker and GitHub to support development, deployment, and maintenance of the system.

---

# 4. System Features

| Feature | Description |
|----------|------------|
| Administrator Authentication | Secure login system that allows only authorized administrators to manage the dealership platform. |
| Vehicle Management | Administrators can add, update, and remove luxury and sports car listings. |
| Brand Management | Vehicles can be organized and managed according to their brands. |
| Category Management | Vehicles can be grouped into categories such as Sports Cars, Supercars, SUVs, and Luxury Sedans. |
| Vehicle Catalog | Customers can browse all available vehicles in the dealership. |
| Vehicle Details Page | Customers can view detailed information including price, specifications, images, and descriptions. |
| Search and Navigation | Customers can easily find vehicles by browsing categories and brands. |
| Checkout Request System | Customers can submit their details and purchase requests for selected vehicles. |
| Order Management | Purchase requests are stored and managed through the system database. |
| Responsive Design | The platform is optimized for desktop, tablet, and mobile devices. |
| Docker Integration | The application is containerized using Docker for consistent deployment across different environments. |
| Online Deployment | The system is deployed online, allowing customers to access the dealership from anywhere. |
---

# 5. Technologies Used
| Category | Technology |
|-----------|------------|
| Frontend | HTML5, CSS3 (Custom Dark Theme Gradients & Glassmorphism Framework) |
| Backend | PHP (Hypertext Preprocessor with object-oriented session tracking parameters) |
| Database | MySQL Server Database Engine running structured indexing using phpmmyadmin |
| Persistence Layer | PDO (PHP Data Objects) utilizing bound parameters for secure SQL interaction |
| Version Control | Git, GitHub |
| Containerization | Docker Engine with specialized environment containers |
| Cloud Infrastructure | infintyfree |

---
# 6. System Architecture

```text
                    Customer / Administrator
                              ↓
                         Web Browser
                              ↓
          Frontend (HTML, CSS, JavaScript)
                              ↓
                     PHP Application
                              ↓
                       MySQL Database
```

### Architecture Description

The KINETIK system follows a three-tier architecture consisting of the presentation layer, application layer, and data layer.

#### Presentation Layer

The presentation layer is developed using HTML, CSS, and JavaScript. It provides the user interface through which customers can browse luxury and sports cars and administrators can manage vehicle listings.

#### Application Layer

The application layer is developed using PHP. It handles business logic, user authentication, vehicle management, order processing, and communication between the frontend and the database.

#### Data Layer

The data layer uses MySQL to store and manage application data. This includes vehicle information, brands, categories, administrator accounts, customer orders, and checkout details.

#### Deployment Architecture

```text
Developer
    ↓
GitHub Repository
    ↓
Docker Container
    ↓
Online Server
    ↓
Users Access KINETIK
```
### Architecture Description

The frontend handles user interactions and displays content. PHP processes business logic and communicates with the MySQL database, which stores application data such as vehicles, brands, categories, and administrator accounts.

---

# 7. Screenshots

## Home Page

![Home Page](screenshots/index.png)

*Description: Landing page displaying featured vehicles .*

---

## Collection Page

![collection](screenshots/kinetik.freedev.app_src_collection.php.png)

*Description: Displays all available vehicles.*

---

## Vehicle Details Page

![Vehicle Details](screenshots/product-details.png)

*Description: Shows detailed information about a selected vehicle.*

---

## cart page:

![cart](screenshots/garage.png)

*Description: Shows detailed information about a selected vehicle.*

---

## Admin Dashboard

![Dashboard](screenshots/admin.png)

*Description: Administration interface for managing inventory.*

---

## contact us 

![contact us](screenshots/contact.png)

*Description: contact form with topic specification.*

---
## about us 

![about us](screenshots/about.png)

*Description: our mission , story and moto.*

---
## checkout

![checkout](screenshots/checkout.png)

*Description: choose payment option and confirm payment*

---

# 8. GitHub Repository Link

**Repository URL**

```text
https://github.com/YOUR_USERNAME/kinetik
```

---

# 9. Deployment Link

**Live Application**

```text
https://YOUR_APP_URL.onrender.com
```

---

# 10. CI/CD Description

The project uses GitHub for version control and automated deployment.

### Workflow

```text
Developer
 ↓
GitHub Repository
 ↓
Automatic Build Process
 ↓
Render Deployment
 ↓
Live Application
```

Whenever code is pushed to GitHub, the deployment platform automatically rebuilds and deploys the latest version of the application.

---

# 11. Challenges Encountered

- Docker configuration and container management.
- Database connectivity issues.
- Deployment troubleshooting.
- Image upload handling.
- Responsive design implementation.
- Managing project versions using Git.

---

# 12. Future Work

Future enhancements include:

- Customer registration and login.
- Vehicle reservation system.
- Online payment integration.
- Email notifications.
- AI-powered vehicle recommendations.
- Mobile application development.
- Advanced analytics dashboard.

---

# 13. Conclusion

KINETIK successfully provides a centralized platform for managing dealership inventory and presenting vehicle information online. The project demonstrates the practical application of web development technologies, database integration, deployment, and containerization while delivering a modern and scalable solution for automotive businesses.
