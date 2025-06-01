# V-Rides - College Bicycle Rental System

A modern web-based bicycle rental system designed specifically for college campuses. V-Rides provides students and staff with an easy, affordable, and convenient way to rent bicycles using QR code technology.

## 🚴‍♂️ About the Project

V-Rides is a comprehensive college cycle rental system that offers flexible rental duration, multiple payment options, bicycle health monitoring, user profiles, pre-booking capabilities, and support for multiple bicycle bookings. The system uses QR code-based locking and unlocking to ensure security and ease of use.

## ✨ Key Features

- **🔒 QR Code Integration**: Secure locking and unlocking system
- **⏰ Flexible Rental Duration**: Rent bicycles for any duration you need
- **💰 Real-time Fare Calculator**: Calculate rental costs before booking
- **👤 User Profiles**: Manage your rental history and preferences
- **📅 Pre-booking System**: Reserve bicycles in advance
- **🚲 Multiple Bicycle Support**: Book multiple bicycles for groups
- **🔧 Bicycle Health Monitoring**: Track the condition and maintenance status
- **📱 Responsive Design**: Works seamlessly on all devices
- **🎨 User-friendly Interface**: Clean and intuitive design
- **📊 Analytics Dashboard**: Comprehensive data visualization for administrators

## 💰 Pricing

- **First 15 minutes**: ₹10 (base fare)
- **Additional time**: ₹1 per minute after the first 15 minutes

## 📁 Project Structure

```
V-Rides/
├── index.html                    # Main landing page
├── login.php                     # User authentication
├── signup.php                    # User registration
├── user_dashboard.php            # User control panel
├── dev_dashboard.php             # Developer/Admin dashboard
├── cycle_ride.php                # Cycle selection and ride initiation
├── ongoing_ride.php              # Active ride monitoring
├── ride_completed.php            # Ride completion and payment
├── previous_rides.php            # Ride history
├── wallet.php                    # Digital wallet management
├── user_queries.php              # User support queries
├── handle_query.php              # Admin query management
├── cycle_info.php                # Cycle information and analytics
├── cycle_maintenance.php         # Maintenance management
├── user_info.php                 # User analytics and management
├── user_authentication.php       # QR code authentication
├── predictHealth.py              # ML model for cycle health prediction
├── cycleReviewTraining.ipynb     # Machine learning training notebook
├── submit_feedback.php           # Feedback submission handler
├── logout.php                    # Session termination
├── css/
│   ├── index.css                 # Main landing page styles
│   ├── login.css                 # Login page styles
│   ├── signup.css                # Registration page styles
│   ├── user_dashboard.css        # User dashboard styles
│   ├── dev_dashboard.css         # Developer dashboard styles
│   ├── cycle_ride.css            # Cycle selection styles
│   ├── ongoing_ride.css          # Active ride styles
│   ├── ride_completed.css        # Ride completion styles
│   ├── previous_rides.css        # Ride history styles
│   ├── wallet.css                # Wallet interface styles
│   ├── user_queries.css          # Query management styles
│   ├── handle_query.css          # Admin query styles
│   ├── cycle_info.css            # Cycle analytics styles
│   ├── user_info.css             # User management styles
│   └── ride_info.css             # Ride information styles
├── assets/
│   ├── bg_index.png              # Background image
│   ├── logo_index.png            # Main logo
│   ├── index_feature.png         # Features section image
│   ├── V-Rides.png               # About section logo
│   └── circle.png                # User avatar placeholder
└── Documentation/
    ├── trained_model.joblib       # Trained ML model
    └── cycleReview.csv           # Training data
```

## 🖼️ Application Screenshots

### 🏠 Landing Page

![Home Page](https://github.com/user-attachments/assets/74211af1-f40e-4288-a8ec-d5ef047de8d9)

### 💰 Pricing Calculator

![Pricing Calculator](https://github.com/user-attachments/assets/1c159a15-105d-4af1-b2f4-f2cb7a5c3f2e)

### 🔐 Authentication System

#### Login Page

![Login Page](https://github.com/user-attachments/assets/fe171f08-d97f-427b-ad10-5b7ce4664d01)

#### Sign Up Page

![Sign Up Page](https://github.com/user-attachments/assets/ddc70bfa-9c6c-481e-b802-3ab1ca041a2d)

### 🚴‍♂️ Ride Experience

#### QR Code for Cycle Access

_Scan this QR code to unlock and access the cycle_

![image](https://github.com/user-attachments/assets/ba9076b8-3edc-4f65-a804-8b58a0f990c7)


#### Cycle Selection & Start Ride

_Scan QR code to unlock cycle, then click "Start Ride" to begin_
![Cycle Selection](https://github.com/user-attachments/assets/f4543b4e-2c7b-4f54-96bf-471a6a74d0d8)

#### Ongoing Ride Monitoring

![Ongoing Ride](https://github.com/user-attachments/assets/52e9fded-8f58-4e7c-9f57-6266acbee50c)

#### Ride Completion Summary

_View ride summary and logout after completion_
![Ride Completion](https://github.com/user-attachments/assets/ca04d7f0-a28f-414b-9f8b-c2637788b226)

### 👤 User Dashboard

![User Dashboard](https://github.com/user-attachments/assets/bc3752f1-ae26-4a69-bbc9-a35681e234c9)

### 🛠️ Administrative Features

#### Developer Dashboard

![Developer Dashboard](https://github.com/user-attachments/assets/e9456813-7e29-4e14-b79f-5cc22a21e92b)

#### Cycle Monitoring & Analytics

![Cycle Monitoring](https://github.com/user-attachments/assets/010c14e1-7f64-464f-95de-f1a26c93bd25)

#### User Analytics

![User Analytics](https://github.com/user-attachments/assets/a6dcc2e2-d29a-43fa-9df9-75a7b0da22db)

## 🛠️ Technologies Used

### Frontend

- **HTML5** - Structure and semantics
- **CSS3** - Styling and responsive design
- **JavaScript** - Interactive functionality
- **Font Awesome** - Icons and visual elements
- **AOS Library** - Scroll animations
- **Chart.js** - Data visualization

### Backend

- **PHP** - Server-side logic and database operations
- **MySQL** - Database management
- **Python** - Machine learning for cycle health prediction
- **scikit-learn** - ML model training and prediction

### Design & UI/UX

- **Google Fonts (Poppins)** - Typography
- **Custom CSS** - Responsive design framework
- **Bootstrap components** - UI components

## 🤖 Machine Learning Features

The system includes an intelligent cycle health prediction model:

- **Training Data**: `Documentation/cycleReview.csv`
- **Model**: Random Forest Classifier
- **Features**: Break function, pedaling smoothness, tire condition, gear shifting, frame stability
- **Output**: Health score predictions for maintenance scheduling

## 📱 Responsive Design

Fully optimized for:

- 🖥️ Desktop computers (1920px+)
- 💻 Laptops (1024px - 1920px)
- 📱 Tablets (768px - 1024px)
- 📱 Mobile devices (320px - 768px)

## 👨‍💻 Developer

**Shubh Gupta**

- 🎓 Project Developer and Designer
- 📧 Contact: shubhorai12@gmail.com
- 💼 LinkedIn: https://linkedin.com/in/ishubhgupta
- 🐙 GitHub: https://github.com/ishubhgupta

## 📞 Support

For support and queries:

- 📧 Email: shubhorai12@gmail.com
- 🐛 Issues: Create an issue in this repository
- 💬 Feedback: Use the in-app feedback form

## 🙏 Acknowledgments

- **Font Awesome** for comprehensive icon library
- **Google Fonts** for beautiful typography
- **AOS Library** for smooth scroll animations
- **Chart.js** for data visualization capabilities
- **scikit-learn** for machine learning framework
- **All beta testers and contributors** for valuable feedback

---

<div align="center">

**Made with ❤️ by Shubh Gupta**

_Empowering sustainable campus transportation through technology_

</div>
