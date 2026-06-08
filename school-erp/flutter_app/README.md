# School ERP — Flutter Mobile App

Flutter mobile app for the School ERP system. Supports 4 user roles:
**Parent · Student · Staff (Teacher) · Correspondent / Principal**

## Setup

### 1. Prerequisites
- Flutter SDK 3.10+
- Android Studio or VS Code with Flutter plugin
- Firebase project with Android/iOS apps configured

### 2. Configure API Base URL
Edit `lib/core/api/api_client.dart`:
```dart
static const String baseUrl = 'http://YOUR_SERVER_IP/api';
```

### 3. Firebase Setup
- Download `google-services.json` → place in `android/app/`
- Download `GoogleService-Info.plist` → place in `ios/Runner/`

### 4. Install dependencies
```bash
flutter pub get
```

### 5. Run
```bash
flutter run
```

## Folder Structure

```
lib/
├── main.dart                          # App entry, Firebase + FCM init
├── core/
│   ├── api/
│   │   ├── api_client.dart            # Dio client + auth interceptor
│   │   └── api_endpoints.dart         # All API endpoint constants
│   ├── services/
│   │   ├── auth_service.dart          # Login/Logout/Token management
│   │   ├── student_service.dart       # Student/Parent data
│   │   ├── fee_service.dart           # Fee + Razorpay
│   │   ├── staff_service.dart         # Staff profile, attendance, payslips
│   │   └── correspondent_service.dart # Dashboard, reports
│   └── utils/
│       ├── app_theme.dart             # Colors, text styles, inputs
│       └── app_router.dart            # GoRouter with role-based redirect
├── screens/
│   ├── auth/
│   │   └── login_screen.dart          # Common login for all roles
│   ├── parent/
│   │   ├── parent_home_screen.dart    # Children list, quick links
│   │   └── fee_screen.dart            # Pending fees + Razorpay payment
│   ├── student/
│   │   └── student_home_screen.dart   # Dashboard, attendance summary
│   ├── staff/
│   │   └── staff_home_screen.dart     # Classes, homework, payslips
│   └── correspondent/
│       └── correspondent_home_screen.dart  # KPI dashboard, reports
└── widgets/
    ├── stat_card.dart                 # Reusable KPI card
    ├── section_header.dart            # Section label with optional trailing
    └── info_row.dart                  # Label + value row

```

## API Summary

| Role         | Key Endpoints |
|--------------|--------------|
| All          | `POST /auth/login`, `GET /auth/me`, `GET /notifications` |
| Parent       | `GET /profile`, `GET /fees/pending`, `POST /fees/create-order`, `POST /fees/verify` |
| Student      | `GET /profile`, `GET /student/{id}/attendance`, `GET /results`, `GET /exams` |
| Staff        | `GET /staff/profile`, `GET /staff/payslips`, `POST /staff/attendance/mark`, `POST /staff/homework` |
| Correspondent| `GET /correspondent/dashboard`, `GET /correspondent/fee-summary`, `POST /correspondent/notifications/send` |

## Default Credentials (after seeding)

| Role           | Phone         | Password      |
|----------------|---------------|---------------|
| Super Admin    | (from seeder) | (from seeder) |
| Parent         | (parent mobile) | same as mobile |
| Student        | (parent mobile) | same as mobile |
| Staff          | (staff phone) | same as phone  |
