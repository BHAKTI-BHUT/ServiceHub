# ServiceHub — Supervisor API Documentation

This document contains the API specification for the **Supervisor App** development. All endpoints require **Bearer Token Authentication** (using standard Laravel Sanctum).

- **Base URL:** `http://127.0.0.1:8000/api` (Local Dev) or your production domain.
- **Headers Required:**
  - `Accept: application/json`
  - `Content-Type: application/json`
  - `Authorization: Bearer <token>`

---

## 1. Get Assigned Bookings List
Fetch the list of bookings assigned to the logged-in supervisor.

- **URL:** `/supervisor/bookings`
- **Method:** `GET`
- **Query Parameters:**
  - `status` (optional): `active` (pending/confirmed/in_progress), `completed`, or `cancelled`
- **Response (200 OK):**
```json
{
  "success": true,
  "bookings": {
    "current_page": 1,
    "data": [
      {
        "id": 14,
        "booking_number": "BPP-2026-0014",
        "customer_id": 8,
        "vendor_id": 2,
        "supervisor_id": 5,
        "pickup_location": "Sec 62, Noida",
        "drop_location": "Sec 18, Noida",
        "shifting_date": "2026-07-25",
        "status": "in_progress",
        "tracking_status": "shifting_started",
        "amount": "4884.00",
        "remaining_amount": "4884.00",
        "remaining_payment_status": "pending"
      }
    ]
  }
}
```

---

## 2. Get Booking Details
Fetch complete details of a specific assigned booking, including selected shifting items, add-ons, matched categories, vehicle, and uploaded photo proofs.

- **URL:** `/supervisor/bookings/{id}`
- **Method:** `GET`
- **Response (200 OK):**
```json
{
  "success": true,
  "booking": {
    "id": 14,
    "booking_number": "BPP-2026-0014",
    "customer_id": 8,
    "pickup_location": "Sec 62, Noida",
    "drop_location": "Sec 18, Noida",
    "amount": "4884.00",
    "remaining_amount": "4884.00",
    "remaining_payment_status": "pending",
    "tracking_status": "shifting_started",
    "items": [
      {
        "id": 1,
        "name": "Sofa 3 Seater",
        "pivot": {
          "quantity": 1
        }
      }
    ],
    "add_ons": [],
    "category": {
      "id": 1,
      "name": "1 BHK Shifting"
    },
    "vehicle": {
      "id": 2,
      "name": "Tata Ace",
      "registration_number": "UP-16-AT-9988"
    },
    "proofs": []
  }
}
```

---

## 3. Start Trip (Vehicle Departed)
Mark the vehicle as departed and advance status to `trip_started`.

- **URL:** `/supervisor/bookings/{id}/start-trip`
- **Method:** `POST`
- **Response (200 OK):**
```json
{
  "success": true,
  "message": "Trip started successfully!",
  "tracking_status": "trip_started"
}
```

---

## 4. Start Shifting (Packing & Loading)
Mark the shifting process as active, moving the booking status to `in_progress` and `tracking_status` to `shifting_started`.

- **URL:** `/supervisor/bookings/{id}/start-shifting`
- **Method:** `POST`
- **Response (200 OK):**
```json
{
  "success": true,
  "message": "Shifting started successfully!",
  "status": "in_progress",
  "tracking_status": "shifting_started"
}
```

---

## 5. Upload Booking Photo Proofs (Supports Multiple Photos)
Upload photo proof of shifting (e.g. loading or unloading state). Uploading proof of type `pickup` automatically advances the tracking status to `pickup_completed` and unlocks the payment button for the customer.

- **URL:** `/supervisor/bookings/{id}/upload-proof`
- **Method:** `POST`
- **Headers:** `Content-Type: multipart/form-data`
- **Body Params:**
  - `images[]`: Array of photo files (JPEG, PNG, JPG, WEBP, max 5MB each).
  - `type`: String, must be `pickup`, `delivery`, or `general`.
- **Response (200 OK):**
```json
{
  "success": true,
  "message": "Photo proofs uploaded successfully!",
  "proofs": [
    {
      "id": 101,
      "booking_id": 14,
      "file_path": "uploads/booking_proofs/proof_1784396000_65fa.png",
      "type": "pickup"
    }
  ],
  "tracking_status": "pickup_completed"
}
```

---

## 6. Update Shifting Items/Addons (Live Price Recalculation)
Update the items or addons dynamically on the spot if the customer demands changes during active packing/loading. It automatically recalculates the fare and updates the remaining balance.

- **URL:** `/supervisor/bookings/{id}/update-items`
- **Method:** `POST`
- **Body Params:**
```json
{
  "items": [
    {"id": 1, "quantity": 2},
    {"id": 5, "quantity": 1}
  ],
  "addons": [2, 3]
}
```
- **Response (200 OK):**
```json
{
  "success": true,
  "message": "Booking items updated successfully!",
  "new_total": 5250.00,
  "remaining": 4700.00
}
```

---

## 7. Update Live Location (Real-time tracking trail like Rapido)
Send the supervisor's current location to track their movement. The app should call this endpoint periodically (e.g., every 30-60 seconds) during active trips.

- **URL:** `/supervisor/location`
- **Method:** `POST`
- **Body Params:**
```json
{
  "latitude": 28.6258,
  "longitude": 77.3792,
  "booking_id": 14
}
```
- **Response (200 OK):**
```json
{
  "success": true,
  "message": "Live location updated successfully.",
  "location": {
    "id": 2042,
    "supervisor_id": 5,
    "booking_id": 14,
    "latitude": 28.6258,
    "longitude": 77.3792,
    "created_at": "2026-07-18 23:48:00"
  }
}
```

---

## 8. Complete Shifting (Unloading & Delivery Completed)
Mark the job as fully completed. This endpoint **strictly validates** that the customer has completed the pending amount payment (`remaining_payment_status` is `'paid'`). If unpaid, it returns a 400 Bad Request error.

- **URL:** `/supervisor/bookings/{id}/complete-shifting`
- **Method:** `POST`
- **Response on Success (200 OK):**
```json
{
  "success": true,
  "message": "Shifting completed successfully!",
  "status": "completed",
  "tracking_status": "completed"
}
```
- **Response on Error (400 Bad Request - if customer has not paid remaining amount):**
```json
{
  "success": false,
  "message": "Cannot complete shifting. Remaining payment of ₹4,884.00 is pending from customer."
}
```
